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

        return view('door_numbers.index', [
            'doorNumbers' => $doorNumbers,
            'sortBy'  => array_search($sortBy, $allowedSorts),
            'sortDir' => $sortDir,
        ]);
    }

    public function create(Request $request) {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $preselectId = $request->query('member_id');

        return view('door_numbers.create', compact('members', 'preselectId'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'member_id'       => 'required|exists:members,id',
            'door_no'         => 'required|string|max:30|unique:door_numbers,door_no',
            'driver_name'     => 'nullable|string|max:150',
            'plate_no'        => 'required|string|max:15',
            'vehicle_type'    => 'nullable|string|max:50',
            'registered_date' => 'required|date',
        ]);

        $member = Member::findOrFail($data['member_id']);
        if ($member->status !== 'active') {
            return back()->withInput()->with('error', 'Nomor pintu hanya dapat didaftarkan pada anggota berstatus aktif.');
        }

        $doorNumber = DB::transaction(function () use ($data, $request) {
            $np = DoorNumber::create($data + ['status' => DoorNumber::STATUS_ACTIVE]);

            DoorNumberHistory::create([
                'door_number_id' => $np->id,
                'from_member_id' => null,
                'to_member_id'   => $np->member_id,
                'action'         => DoorNumberHistory::ACTION_REGISTERED,
                'action_date'    => $np->registered_date,
                'notes'          => 'Pendaftaran nomor pintu baru.',
                'performed_by'   => auth()->id(),
            ]);

            return $np;
        });

        return redirect()->route('door-numbers.show', $doorNumber)
            ->with('success', 'Nomor pintu ' . $doorNumber->door_no . ' berhasil didaftarkan.');
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
