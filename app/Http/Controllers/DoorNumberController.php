<?php

namespace App\Http\Controllers;

use App\Models\DoorNumber;
use App\Models\DoorNumberHistory;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoorNumberController extends Controller
{
    public function index(Request $request) {
        $allowedSorts = [
            'door_no'         => 'door_numbers.door_no',
            'member'          => 'members.name',
            'driver'          => 'door_numbers.driver_name',
            'plate'           => 'door_numbers.plate_no',
            'status'          => 'door_numbers.status',
            'registered_date' => 'door_numbers.registered_date',
            'newest'          => 'door_numbers.created_at',
        ];

        $sortBy  = $allowedSorts[$request->query('sort_by')] ?? $allowedSorts['newest'];
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $doorNumbers = DoorNumber::query()
            ->join('members', 'members.id', '=', 'door_numbers.member_id', 'left')
            ->select('door_numbers.*')
            ->with('member')
            ->search($request->query('q'))
            ->orderBy($sortBy, $sortDir)
            ->paginate(20)
            ->withQueryString();

        $activeDoorNumbers = DoorNumber::where('status','active')
            ->count();

        $availableDoorNumbers = DoorNumber::where('status','available')
            ->count();

        return view('door_numbers.index', [
            'doorNumbers' => $doorNumbers,
            'activeDoorNumbers' => $activeDoorNumbers,
            'availableDoorNumbers' => $availableDoorNumbers,
            'sortBy'  => array_search($sortBy, $allowedSorts),
            'sortDir' => $sortDir,
        ]);
    }

    public function create(Request $request) {
        return view('door_numbers.create');
    }

    /**
     * Daftarkan nomor pintu ke pool KBP: status default AVAILABLE, tanpa pemegang.
     * Mendukung pendaftaran tunggal (NP-101) maupun rentang (NP-001 s/d NP-100).
     */
    public function store(Request $request) {
        $data = $request->validate([
            'mode'            => 'required|in:single,range',
            'door_no'         => 'required_if:mode,single|nullable|string|max:30|unique:door_numbers,door_no',
            'door_no_start'   => 'required_if:mode,range|nullable|string|max:30',
            'door_no_end'     => 'required_if:mode,range|nullable|string|max:30|different:door_no_start',
            'registered_date' => 'required|date',
            'notes'           => 'nullable|string|max:500',
        ], [
            'door_no.unique'          => 'Nomor pintu tersebut sudah terdaftar.',
            'door_no_end.different'   => 'Nomor akhir harus berbeda dengan nomor awal.',
        ]);

        // Parse nomor: harus pola [prefix][angka]
        $parse = fn (string $s) => preg_match('/^(.*?)(\d+)$/u', trim($s), $m) ? [$m[1], (int) $m[2], strlen($m[2])] : null;

        if ($data['mode'] === 'single') {
            $items = [[$data['door_no'], $data['door_no']]];
        } else {
            $start = $parse($data['door_no_start']);
            $end   = $parse($data['door_no_end']);

            if (!$start || !$end) {
                return back()->withInput()->with('error', 'Nomor harus diakhiri angka. Contoh: NP-001 s/d NP-100.');
            }
            if ($start[0] !== $end[0]) {
                return back()->withInput()->with('error', 'Awalan (prefix) nomor awal dan akhir harus sama. Contoh: NP-001 s/d NP-100.');
            }
            if ($end[1] <= $start[1]) {
                return back()->withInput()->with('error', 'Nomor akhir harus lebih besar dari nomor awal.');
            }
            if ($end[1] - $start[1] + 1 > 500) {
                return back()->withInput()->with('error', 'Maksimal 500 nomor pintu per pendaftaran rentang.');
            }

            $pad = max($start[2], $end[2]);
            $items = [];
            for ($i = $start[1]; $i <= $end[1]; $i++) {
                $items[] = [$start[0] . str_pad((string) $i, $pad, '0', STR_PAD_LEFT), null];
            }
        }

        // Cek duplikat sebelum menulis (redirect ramah, bukan error page)
        $doorNos = array_column($items, 0);
        $existing = DoorNumber::whereIn('door_no', $doorNos)->pluck('door_no')->all();
        if ($existing) {
            return back()->withInput()->with('error', 'Pendaftaran dibatalkan — nomor pintu sudah terdaftar: '
                . implode(', ', array_slice($existing, 0, 5)) . (count($existing) > 5 ? ', dan ' . (count($existing) - 5) . ' lainnya' : '')
                . '. Tidak ada data yang tersimpan.');
        }

        $created = DB::transaction(function () use ($items, $data, $request) {
            $doorNumbers = collect();

            foreach ($items as [$doorNo, $explicit]) {
                $np = DoorNumber::create([
                    'member_id'       => null,
                    'door_no'         => $doorNo,
                    'plate_no'        => null,
                    'vehicle_type'    => null,
                    'driver_name'     => null,
                    'status'          => DoorNumber::STATUS_AVAILABLE,
                    'registered_date' => $data['registered_date'],
                ]);

                DoorNumberHistory::create([
                    'door_number_id' => $np->id,
                    'from_member_id' => null,
                    'to_member_id'   => null,
                    'action'         => DoorNumberHistory::ACTION_REGISTERED,
                    'action_date'    => $np->registered_date,
                    'notes'          => $data['notes'] ?? 'Didaftarkan ke pool KBP.',
                    'performed_by'   => auth()->id(),
                ]);

                $doorNumbers->push($np);
            }

            return $doorNumbers;
        });

        $message = $data['mode'] === 'single'
            ? 'Nomor pintu ' . $created->first()->door_no . ' berhasil didaftarkan ke pool KBP.'
            : count($created) . ' nomor pintu (' . $created->first()->door_no . ' s/d ' . $created->last()->door_no . ') berhasil didaftarkan ke pool KBP.';

        return redirect()
            ->route($data['mode'] === 'single' ? 'door-numbers.show' : 'door-numbers.index', $data['mode'] === 'single' ? $created->first() : [])
            ->with('success', $message);
    }

    /**
     * Halaman pengambilan nomor pintu dari pool KBP oleh anggota.
     */
    public function claimForm(Request $request) {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $available = DoorNumber::where('status', DoorNumber::STATUS_AVAILABLE)->orderBy('door_no')->get();
        $preselectDoor = $request->query('door_number');
        $preselectMember = $request->query('member_id');

        return view('door_numbers.claim', compact('members', 'available', 'preselectDoor', 'preselectMember'));
    }

    /**
     * Anggota aktif mengambil nomor pintu available: langsung ACTIVE + nempel ke dia.
     */
    public function claim(Request $request) {
        $data = $request->validate([
            'member_id'    => 'required|exists:members,id',
            'door_number'  => 'required|exists:door_numbers,id',
            'driver_name'  => 'nullable|string|max:150',
            'plate_no'     => 'nullable|string|max:15',
            'vehicle_type' => 'nullable|string|max:50',
            'claim_date'   => 'required|date',
        ]);

        $np = DoorNumber::findOrFail($data['door_number']);
        $member = Member::findOrFail($data['member_id']);

        if (!$np->isAvailable()) {
            return back()->withInput()->with('error', 'Nomor pintu tersebut tidak tersedia (sudah diambil atau dinonaktifkan).');
        }

        if ($member->status !== 'active') {
            return back()->withInput()->with('error', 'Hanya anggota berstatus aktif yang dapat mengambil nomor pintu.');
        }

        DB::transaction(function () use ($np, $member, $data) {
            DoorNumberHistory::create([
                'door_number_id' => $np->id,
                'from_member_id' => null,
                'to_member_id'   => $member->id,
                'action'         => DoorNumberHistory::ACTION_TRANSFERRED,
                'action_date'    => $data['claim_date'],
                'notes'          => 'Diambil dari pool KBP' . ($data['plate_no'] ? ' — plat ' . $data['plate_no'] : '') . '.',
                'performed_by'   => auth()->id(),
            ]);

            $np->update([
                'member_id'    => $member->id,
                'status'       => DoorNumber::STATUS_ACTIVE,
                'driver_name'  => $data['driver_name'] ?? null,
                'plate_no'     => $data['plate_no'] ?? null,
                'vehicle_type' => $data['vehicle_type'] ?? null,
            ]);
        });

        return redirect()->route('door-numbers.show', $np)
            ->with('success', 'Nomor pintu ' . $np->door_no . ' berhasil diambil oleh ' . $member->name . ' dan kini aktif.');
    }

    public function show(DoorNumber $doorNumber) {
        $doorNumber->load(['member', 'histories.fromMember', 'histories.toMember', 'histories.performer']);

        $exchangeCandidates = collect();
        if ($doorNumber->isInactive()) {
            $exchangeCandidates = DoorNumber::with('member')
                ->where('status', DoorNumber::STATUS_INACTIVE)
                ->where('id', '!=', $doorNumber->id)
                ->whereNotNull('member_id')
                ->whereHas('member', fn ($m) => $m->where('status', 'active'))
                ->where('member_id', '!=', $doorNumber->member_id)
                ->orderBy('door_no')
                ->get();
        }

        return view('door_numbers.show', compact('doorNumber', 'exchangeCandidates'));
    }

    public function edit(DoorNumber $doorNumber) {
        return view('door_numbers.edit', compact('doorNumber'));
    }

    public function update(Request $request, DoorNumber $doorNumber) {
        // Pemegang (member_id) sengaja TIDAK bisa diubah di sini.
        // Pergantian pemegang hanya melalui alur Transfer / Tukar agar tercatat di histori.
        $data = $request->validate([
            'door_no'         => 'required|string|max:30|unique:door_numbers,door_no,' . $doorNumber->id,
            'driver_name'     => 'nullable|string|max:150',
            'plate_no'        => 'required|string|max:15',
            'vehicle_type'    => 'nullable|string|max:50',
            'registered_date' => 'required|date',
        ]);

        $doorNumber->update($data);

        return redirect()->route('door-numbers.show', $doorNumber)
            ->with('success', 'Data nomor pintu diperbarui.');
    }

    /**
     * Transisi status: deactivate / activate / release.
     */
    public function updateStatus(Request $request, DoorNumber $doorNumber) {
        $data = $request->validate([
            'action'         => 'required|in:deactivate,activate,release',
            'action_date'    => 'required_unless:action,activate|nullable|date|after_or_equal:registered_date',
        ], [
            'action_date.required_unless' => 'Tanggal wajib diisi.',
            'action_date.after_or_equal'  => 'Tanggal tidak boleh sebelum tanggal daftar.',
        ]);

        $action = $data['action'];

        if (!$doorNumber->hasSettledObligations()) {
            return back()->with('error', 'Hak dan kewajiban nomor pintu ini belum selesai, tidak dapat diproses.');
        }

        $valid = match ($action) {
            'deactivate' => $doorNumber->isActive(),
            'activate'   => $doorNumber->isInactive(),
            'release'    => $doorNumber->isActive() || $doorNumber->isInactive(),
        };

        if (!$valid) {
            return back()->with('error', 'Perubahan status tidak valid dari kondisi saat ini.');
        }

        DB::transaction(function () use ($doorNumber, $action, $data) {
            $actionDate = $data['action_date'] ?? now()->toDateString();

            $history = [
                'door_number_id' => $doorNumber->id,
                'from_member_id' => $doorNumber->member_id,
                'to_member_id'   => $doorNumber->member_id,
                'action_date'    => $actionDate,
                'performed_by'   => auth()->id(),
            ];

            if ($action === 'deactivate') {
                $doorNumber->update([
                    'status'           => DoorNumber::STATUS_INACTIVE,
                    'deactivated_date' => $actionDate,
                ]);
                $history['action'] = DoorNumberHistory::ACTION_DEACTIVATED;
                $history['notes']  = 'Nomor pintu dinonaktifkan.';
            } elseif ($action === 'activate') {
                $doorNumber->update([
                    'status'           => DoorNumber::STATUS_ACTIVE,
                    'deactivated_date' => null,
                ]);
                $history['action'] = DoorNumberHistory::ACTION_ACTIVATED;
                $history['notes']  = 'Nomor pintu diaktifkan kembali.';
            } else { // release
                $doorNumber->update([
                    'status'           => DoorNumber::STATUS_AVAILABLE,
                    'member_id'        => null,
                    'deactivated_date' => null,
                ]);
                $history['action'] = DoorNumberHistory::ACTION_RELEASED;
                $history['notes']  = 'Nomor pintu dilepas ke KBP, menunggu pemegang baru.';
            }

            DoorNumberHistory::create($history);
        });

        $message = match ($action) {
            'deactivate' => 'Nomor pintu dinonaktifkan.',
            'activate'   => 'Nomor pintu diaktifkan kembali.',
            'release'    => 'Nomor pintu dilepas ke KBP (tersedia).',
        };

        return redirect()->route('door-numbers.show', $doorNumber)->with('success', $message);
    }

    public function transferForm(DoorNumber $doorNumber) {
        if (!$doorNumber->isAvailable() && !$doorNumber->isInactive()) {
            return redirect()->route('door-numbers.show', $doorNumber)
                ->with('error', 'Transfer hanya dapat dilakukan pada nomor pintu berstatus tersedia atau nonaktif.');
        }

        $members = Member::where('status', 'active')->orderBy('name')->get();

        return view('door_numbers.transfer', compact('doorNumber', 'members'));
    }

    public function transfer(Request $request, DoorNumber $doorNumber) {
        $data = $request->validate([
            'member_id'    => 'required|exists:members,id',
            'transfer_date'=> 'required|date|after_or_equal:registered_date',
            'notes'        => 'nullable|string|max:500',
        ], [
            'transfer_date.after_or_equal' => 'Tanggal transfer tidak boleh sebelum tanggal daftar.',
        ]);

        if (!$doorNumber->isAvailable() && !$doorNumber->isInactive()) {
            return back()->with('error', 'Transfer hanya dapat dilakukan pada nomor pintu berstatus tersedia atau nonaktif.');
        }

        if (!$doorNumber->hasSettledObligations()) {
            return back()->with('error', 'Hak dan kewajiban nomor pintu ini belum selesai, tidak dapat ditransfer.');
        }

        $target = Member::findOrFail($data['member_id']);
        if ($target->status !== 'active') {
            return back()->with('error', 'Nomor pintu hanya dapat ditransfer ke anggota berstatus aktif.');
        }

        if ($doorNumber->member_id === $target->id) {
            return back()->with('error', 'Nomor pintu sudah berada pada anggota tersebut.');
        }

        DB::transaction(function () use ($doorNumber, $target, $data) {
            DoorNumberHistory::create([
                'door_number_id' => $doorNumber->id,
                'from_member_id' => $doorNumber->member_id,
                'to_member_id'   => $target->id,
                'action'         => DoorNumberHistory::ACTION_TRANSFERRED,
                'action_date'    => $data['transfer_date'],
                'notes'          => $data['notes'] ?? 'Transfer ke anggota baru.',
                'performed_by'   => auth()->id(),
            ]);

            $doorNumber->update([
                'member_id'        => $target->id,
                'status'           => DoorNumber::STATUS_ACTIVE,
                'deactivated_date' => null,
            ]);
        });

        return redirect()->route('door-numbers.show', $doorNumber)
            ->with('success', 'Nomor pintu ' . $doorNumber->door_no . ' berhasil ditransfer ke ' . $target->name . '.');
    }

    public function exchangeForm(Request $request) {
        $candidates = DoorNumber::with('member')
            ->where('status', DoorNumber::STATUS_INACTIVE)
            ->whereNotNull('member_id')
            ->whereHas('member', fn ($m) => $m->where('status', 'active'))
            ->orderBy('door_no')
            ->get();

        $preselectA = $request->query('door_number_id');

        return view('door_numbers.exchange', compact('candidates', 'preselectA'));
    }

    public function exchange(Request $request) {
        $data = $request->validate([
            'door_number_a' => 'required|exists:door_numbers,id',
            'door_number_b' => 'required|exists:door_numbers,id|different:door_number_a',
            'exchange_date' => 'required|date',
            'notes'         => 'nullable|string|max:500',
        ]);

        $npA = DoorNumber::with('member')->findOrFail($data['door_number_a']);
        $npB = DoorNumber::with('member')->findOrFail($data['door_number_b']);

        $errors = [];
        if (!$npA->isInactive() || !$npB->isInactive()) {
            $errors[] = 'Kedua nomor pintu harus berstatus nonaktif sebelum dipertukarkan.';
        }
        if (!$npA->member || !$npB->member || $npA->member_id === $npB->member_id) {
            $errors[] = 'Kedua nomor pintu harus milik anggota yang berbeda.';
        }
        if ($npA->member?->status !== 'active' || $npB->member?->status !== 'active') {
            $errors[] = 'Kedua anggota pemilik harus berstatus aktif.';
        }
        if (!$npA->hasSettledObligations() || !$npB->hasSettledObligations()) {
            $errors[] = 'Hak dan kewajiban kedua nomor pintu harus sudah selesai.';
        }

        if ($errors) {
            return back()->withInput()->with('error', implode(' ', $errors));
        }

        DB::transaction(function () use ($npA, $npB, $data) {
            $holderA = $npA->member_id;
            $holderB = $npB->member_id;

            foreach ([[$npA, $holderA, $holderB], [$npB, $holderB, $holderA]] as [$np, $from, $to]) {
                DoorNumberHistory::create([
                    'door_number_id' => $np->id,
                    'from_member_id' => $from,
                    'to_member_id'   => $to,
                    'action'         => DoorNumberHistory::ACTION_EXCHANGED,
                    'action_date'    => $data['exchange_date'],
                    'notes'          => $data['notes'] ?? 'Pertukaran dengan nomor pintu lain.',
                    'performed_by'   => auth()->id(),
                ]);

                $np->update([
                    'member_id'        => $to,
                    'status'           => DoorNumber::STATUS_ACTIVE,
                    'deactivated_date' => null,
                ]);
            }
        });

        return redirect()->route('door-numbers.show', $npA)
            ->with('success', 'Pertukaran berhasil: ' . $npA->door_no . ' ↔ ' . $npB->door_no . '. Keduanya kembali aktif dengan pemegang baru.');
    }
}
