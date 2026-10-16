<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparepartController extends Controller
{
    public function index(Request $request) {
        $allowedSorts = [
            'kode'         => 'spareparts.kode',
            'nama'         => 'spareparts.nama',
            'kategori'     => 'spareparts.kategori',
            'harga_anggota'=> 'spareparts.harga_anggota',
            'stok_qty'     => 'spareparts.stok_qty',
            'newest'       => 'spareparts.created_at',
        ];

        $sortBy  = $allowedSorts[$request->query('sort_by')] ?? $allowedSorts['newest'];
        $sortDir = strtolower($request->query('sort_dir', 'asc')) === 'asc' ? 'asc' : 'desc';

        $spareparts = Sparepart::query()
            ->search($request->query('q'))
            ->orderBy($sortBy, $sortDir)
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_item'  => Sparepart::count(),
            'low_stock'   => Sparepart::whereColumn('stok_qty', '<=', 'stok_minimal')->where('status', 'active')->count(),
            'stock_value' => Sparepart::selectRaw('COALESCE(SUM(harga_beli * stok_qty), 0) as v')->value('v'),
            'available'   => Sparepart::where('status', 'active')->count(),
        ];

        return view('spareparts.index', [
            'spareparts' => $spareparts,
            'stats'      => $stats,
            'sortBy'     => array_search($sortBy, $allowedSorts),
            'sortDir'    => $sortDir,
        ]);
    }

    public function create() {
        return view('spareparts.create', ['kategori' => Sparepart::KATEGORI]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nama'          => 'required|string|max:150',
            'kategori'      => 'required|in:' . implode(',', Sparepart::KATEGORI),
            'satuan'        => 'required|string|max:20',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_anggota' => 'required|numeric|min:0',
            'harga_driver'  => 'required|numeric|min:0',
            'harga_umum'    => 'required|numeric|min:0',
            'stok_qty'      => 'required|numeric|min:0',
            'stok_minimal'  => 'required|numeric|min:0',
        ], [
            'harga_anggota.gt' => 'Harga anggota harus lebih dari nol.',
        ]);

        $sparepart = DB::transaction(function () use ($data) {
            $sparepart = Sparepart::create($data);

            if ((float) $data['stok_qty'] > 0) {
                StockMovement::create([
                    'sparepart_id' => $sparepart->id,
                    'type'         => StockMovement::TYPE_IN,
                    'qty'          => $data['stok_qty'],
                    'stock_before' => 0,
                    'stock_after'  => $data['stok_qty'],
                    'reference'    => 'Stok awal',
                    'notes'        => 'Stok awal saat pendaftaran barang.',
                    'performed_by' => auth()->id(),
                ]);
            }

            return $sparepart;
        });

        return redirect()->route('spareparts.show', $sparepart)
            ->with('success', 'Sparepart ' . $sparepart->nama . ' berhasil ditambahkan.');
    }

    public function show(Sparepart $sparepart) {
        $sparepart->load('stockMovements.performer');

        return view('spareparts.show', compact('sparepart'));
    }

    public function edit(Sparepart $sparepart) {
        return view('spareparts.edit', ['sparepart' => $sparepart, 'kategori' => Sparepart::KATEGORI]);
    }

    public function update(Request $request, Sparepart $sparepart) {
        // Stok TIDAK diubah di sini — stok hanya lewat Restock / transaksi penjualan
        $data = $request->validate([
            'nama'          => 'required|string|max:150',
            'kategori'      => 'required|in:' . implode(',', Sparepart::KATEGORI),
            'satuan'        => 'required|string|max:20',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_anggota' => 'required|numeric|min:0',
            'harga_driver'  => 'required|numeric|min:0',
            'harga_umum'    => 'required|numeric|min:0',
            'stok_minimal'  => 'required|numeric|min:0',
        ]);

        $sparepart->update($data);

        return redirect()->route('spareparts.show', $sparepart)
            ->with('success', 'Data sparepart diperbarui.');
    }

    public function destroy(Sparepart $sparepart) {
        $sparepart->update(['status' => Sparepart::STATUS_INACTIVE]);
        return back()->with('success', 'Sparepart dinonaktifkan.');
    }

    /**
     * Toggle status aktif/nonaktif master barang.
     */
    public function updateStatus(Request $request, Sparepart $sparepart) {
        $data = $request->validate(['status' => 'required|in:active,inactive']);
        $sparepart->update(['status' => $data['status']]);

        $message = $data['status'] === 'active' ? 'Sparepart diaktifkan kembali.' : 'Sparepart dinonaktifkan.';
        return redirect()->route('spareparts.show', $sparepart)->with('success', $message);
    }

    public function restockForm(Sparepart $sparepart) {
        return view('spareparts.restock', compact('sparepart'));
    }

    /**
     * Stok masuk dari supplier + opsi update HPP.
     */
    public function restock(Request $request, Sparepart $sparepart) {
        $data = $request->validate([
            'qty'         => 'required|numeric|min:0.01',
            'harga_beli'  => 'nullable|numeric|min:0',
            'restock_date'=> 'required|date',
            'notes'       => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($sparepart, $data) {
            $before = (float) $sparepart->stok_qty;
            $after = $before + (float) $data['qty'];

            $update = ['stok_qty' => $after];
            if (isset($data['harga_beli']) && $data['harga_beli'] !== null) {
                $update['harga_beli'] = $data['harga_beli'];
            }
            $sparepart->update($update);

            StockMovement::create([
                'sparepart_id' => $sparepart->id,
                'type'         => StockMovement::TYPE_IN,
                'qty'          => $data['qty'],
                'stock_before' => $before,
                'stock_after'  => $after,
                'reference'    => 'Restock',
                'notes'        => $data['notes'] ?? 'Restock dari supplier.',
                'performed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('spareparts.show', $sparepart)
            ->with('success', 'Stok ' . $sparepart->nama . ' bertambah ' . $data['qty'] . ' ' . $sparepart->satuan . '.');
    }
}
