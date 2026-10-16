<?php

namespace App\Http\Controllers;

use App\Models\DoorNumber;
use App\Models\Member;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Saving;
use App\Models\Sparepart;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request) {
        $allowedSorts = [
            'invoice_no' => 'sales.invoice_no',
            'buyer'      => 'members.name',
            'total'      => 'sales.total',
            'status'     => 'sales.status',
            'sale_date'  => 'sales.sale_date',
            'newest'     => 'sales.created_at',
        ];

        $sortBy  = $allowedSorts[$request->query('sort_by')] ?? $allowedSorts['newest'];
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sales = Sale::query()
            ->join('members', 'members.id', '=', 'sales.member_id', 'left')
            ->select('sales.*')
            ->with(['member', 'items', 'payments'])
            ->search($request->query('q'))
            ->when($request->query('status'), fn ($q, $s) => $q->where('sales.status', $s))
            ->when($request->query('buyer_type'), fn ($q, $b) => $q->where('sales.buyer_type', $b))
            ->orderBy($sortBy, $sortDir)
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', [
            'sales'    => $sales,
            'sortBy'   => array_search($sortBy, $allowedSorts),
            'sortDir'  => $sortDir,
            'filterStatus' => $request->query('status'),
            'filterBuyer'  => $request->query('buyer_type'),
        ]);
    }

    /**
     * Halaman kasir (POS): pilih buyer, susun item, split payment.
     */
    public function create(Request $request) {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $spareparts = Sparepart::active()->where('stok_qty', '>', 0)->orderBy('nama')->get();

        // Dropdown driver: nama pemegang nomor pintu aktif (distinct) + nama driver manual
        $drivers = DoorNumber::where('status', DoorNumber::STATUS_ACTIVE)
            ->whereNotNull('driver_name')
            ->distinct()
            ->orderBy('driver_name')
            ->pluck('driver_name')
            ->unique()
            ->values();

        // Fallback: driver juga bisa diambil dari anggota pemegang nomor pintu aktif
        $memberDrivers = DoorNumber::where('status', DoorNumber::STATUS_ACTIVE)
            ->whereNull('driver_name')
            ->with('member')
            ->get()
            ->pluck('member.name')
            ->filter()
            ->unique()
            ->values();

        $preselectMember = $request->query('member_id');

        return view('sales.pos', compact('members', 'spareparts', 'drivers', 'memberDrivers', 'preselectMember'));
    }

    /**
     * Simpan transaksi: kurangi stok (lockForUpdate), snapshot harga, split payment.
     * Semua all-or-nothing dalam DB transaction.
     */
    public function store(Request $request) {
        $data = $request->validate([
            'buyer_type'     => 'required|in:anggota,driver,umum',
            'member_id'      => 'required_if:buyer_type,anggota|nullable|exists:members,id',
            'driver_name'    => 'required_if:buyer_type,driver|nullable|string|max:150',
            'sale_date'      => 'required|date',
            'notes'          => 'nullable|string|max:500',
            'items'          => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'payments'       => 'required|array|min:1',
            'payments.*.method' => 'required|in:cash,simpanan,utang',
            'payments.*.amount' => 'required|numeric|min:0.01',
        ], [
            'items.required'        => 'Minimal satu item harus ditambahkan.',
            'items.*.qty.min'       => 'Qty minimal 0.01.',
            'member_id.required_if' => 'Anggota wajib dipilih untuk pembeli anggota.',
            'driver_name.required_if' => 'Nama driver wajib diisi untuk pembeli driver.',
            'payments.min'          => 'Minimal satu metode pembayaran.',
        ]);

        // Validasi buyer
        $member = null;
        if ($data['buyer_type'] === Sale::BUYER_ANGGOTA) {
            $member = Member::findOrFail($data['member_id']);
            if ($member->status !== 'active') {
                return back()->withInput()->with('error', 'Pembeli anggota harus berstatus aktif.');
            }
        }

        $result = DB::transaction(function () use ($data, $member) {
            // Kunci & validasi stok per item (all-or-nothing)
            $items = [];
            $total = 0;

            foreach (collect($data['items'])->groupBy('sparepart_id') as $sparepartId => $group) {
                $qty = (float) $group->sum('qty');

                /** @var Sparepart $sp */
                $sp = Sparepart::where('id', $sparepartId)->lockForUpdate()->firstOrFail();

                if (!$sp->isActive()) {
                    throw new \InvalidArgumentException('Sparepart ' . $sp->nama . ' berstatus nonaktif.');
                }
                if ($qty > (float) $sp->stok_qty) {
                    throw new \InvalidArgumentException('Stok ' . $sp->nama . ' tidak cukup (tersedia ' . (float) $sp->stok_qty . ' ' . $sp->satuan . ', diminta ' . $qty . ').');
                }

                $price = $sp->priceFor($data['buyer_type']);
                $subtotal = $price * $qty;

                $items[] = [
                    'sparepart'    => $sp,
                    'qty'          => $qty,
                    'harga_satuan' => $price,
                    'subtotal'     => $subtotal,
                ];
                $total += $subtotal;
            }

            // Validasi split payment terhadap total
            $payments = collect($data['payments'])
                ->groupBy('method')
                ->map(fn ($g) => ['method' => $g->first()['method'], 'amount' => (float) $g->sum('amount')])
                ->values();

            // Potong simpanan sukarela (hanya jika ada method simpanan)
            foreach ($payments as $p) {
                if ($p['method'] === SalePayment::METHOD_SIMPANAN) {
                    if (!$member) {
                        throw new \InvalidArgumentException('Pembayaran simpanan hanya untuk pembeli anggota.');
                    }

                    $saldoSukarela = (float) Saving::where('member_id', $member->id)
                        ->where('type', 'sukarela')
                        ->sum('amount');

                    if ($p['amount'] > $saldoSukarela) {
                        throw new \InvalidArgumentException('Simpanan sukarela ' . $member->name . ' tidak cukup (saldo Rp ' . number_format($saldoSukarela, 0, ',', '.') . ', dibutuhkan Rp ' . number_format($p['amount'], 0, ',', '.') . ').');
                    }
                }
            }

            $totalPaid = $payments->sum('amount');
            if ($totalPaid < $total) {
                throw new \InvalidArgumentException('Total pembayaran (Rp ' . number_format($totalPaid, 0, ',', '.') . ') kurang dari total belanja (Rp ' . number_format($total, 0, ',', '.') . ').');
            }

            // Buat header
            $sale = Sale::create([
                'buyer_type'    => $data['buyer_type'],
                'member_id'     => $member?->id,
                'driver_name'   => $data['driver_name'] ?? null,
                'total'         => $total,
                'total_paid'    => min($totalPaid, $total),
                'change_amount' => max(0, $totalPaid - $total),
                'status'        => $totalPaid >= $total ? Sale::STATUS_PAID : Sale::STATUS_PARTIAL,
                'sale_date'     => $data['sale_date'],
                'notes'         => $data['notes'] ?? null,
                'performed_by'  => auth()->id(),
            ]);

            // Detail item + kurangi stok + movement
            foreach ($items as $it) {
                SaleItem::create([
                    'sale_id'      => $sale->id,
                    'sparepart_id' => $it['sparepart']->id,
                    'nama_snapshot' => $it['sparepart']->nama,
                    'qty'          => $it['qty'],
                    'harga_satuan' => $it['harga_satuan'],
                    'subtotal'     => $it['subtotal'],
                ]);

                $before = (float) $it['sparepart']->stok_qty;
                $after = $before - $it['qty'];
                $it['sparepart']->update(['stok_qty' => $after]);

                StockMovement::create([
                    'sparepart_id' => $it['sparepart']->id,
                    'type'         => StockMovement::TYPE_OUT,
                    'qty'          => $it['qty'],
                    'stock_before' => $before,
                    'stock_after'  => $after,
                    'reference'    => $sale->invoice_no,
                    'notes'        => 'Penjualan ' . $sale->invoice_no,
                    'performed_by' => auth()->id(),
                ]);
            }

            // Split payments
            foreach ($payments as $p) {
                $reference = null;

                if ($p['method'] === SalePayment::METHOD_SIMPANAN) {
                    // Potong simpanan sukarela: buat record saving negatif (pengeluaran)
                    // menggunakan pattern existing: baris saving dengan amount negatif
                    // dan description merujuk invoice. Aggregate sum tetap valid.
                    $saving = Saving::create([
                        'member_id'        => $member->id,
                        'type'             => 'sukarela',
                        'amount'           => -$p['amount'],
                        'transaction_date' => $data['sale_date'],
                        'description'      => 'Pemotongan pembelian sparepart ' . $sale->invoice_no,
                    ]);
                    $reference = 'SV-' . $saving->id;
                } elseif ($p['method'] === SalePayment::METHOD_UTANG) {
                    $reference = 'Utang koperasi';
                }

                SalePayment::create([
                    'sale_id'   => $sale->id,
                    'method'    => $p['method'],
                    'amount'    => $p['amount'],
                    'reference' => $reference,
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $result)
            ->with('success', 'Transaksi ' . $result->invoice_no . ' berhasil disimpan.');
    }

    public function show(Sale $sale) {
        $sale->load(['member', 'items.sparepart', 'payments', 'performer']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Void transaksi: kembalikan stok, hapus tidak — tandai via movement adjust.
     * Status penjualan tetap tercatat untuk audit, stok dikembalikan penuh.
     */
    public function destroy(Sale $sale) {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $sp = Sparepart::where('id', $item->sparepart_id)->lockForUpdate()->first();
                if (!$sp) continue;

                $before = (float) $sp->stok_qty;
                $after = $before + (float) $item->qty;
                $sp->update(['stok_qty' => $after]);

                StockMovement::create([
                    'sparepart_id' => $sp->id,
                    'type'         => StockMovement::TYPE_IN,
                    'qty'          => $item->qty,
                    'stock_before' => $before,
                    'stock_after'  => $after,
                    'reference'    => 'VOID ' . $sale->invoice_no,
                    'notes'        => 'Pembatalan transaksi ' . $sale->invoice_no,
                    'performed_by' => auth()->id(),
                ]);
            }

            // Kembalikan potongan simpanan (kalau ada) sebagai saving positif
            foreach ($sale->payments()->where('method', SalePayment::METHOD_SIMPANAN)->get() as $payment) {
                if ($sale->member_id) {
                    Saving::create([
                        'member_id'        => $sale->member_id,
                        'type'             => 'sukarela',
                        'amount'           => $payment->amount,
                        'transaction_date' => now()->toDateString(),
                        'description'      => 'Pengembalian pembatalan ' . $sale->invoice_no,
                    ]);
                }
            }

            // Catat bahwa transaksi di-void via notes agar historis tetap jelas
            $sale->update([
                'status' => Sale::STATUS_PARTIAL,
                'notes'  => trim(($sale->notes ? $sale->notes . ' ' : '') . '[VOID ' . now()->format('d-m-Y H:i') . ']'),
            ]);
        });

        return redirect()->route('sales.index')
            ->with('success', 'Transaksi ' . $sale->invoice_no . ' dibatalkan. Stok dan simpanan telah dikembalikan.');
    }
}
