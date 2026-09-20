<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request) {
        $allowedSorts = [
            'member_no' => 'members.member_no',
            'name'      => 'members.name',
            'phone'     => 'members.phone',
            'status'    => 'members.status',
            'newest'    => 'members.created_at',
        ];

        $sortBy  = $allowedSorts[$request->query('sort_by')] ?? $allowedSorts['newest'];
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $members = Member::orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        return view('members.index', [
            'members' => $members,
            'sortBy'  => array_search($sortBy, $allowedSorts),
            'sortDir' => $sortDir,
        ]);
    }

    public function create() { return view('members.create'); }

    public function store(Request $request) {
        $data = $request->validate([
            'member_no'=>'required|string|max:30|unique:members,member_no',
            'name'=>'required|string|max:150',
            'nik'=>'nullable|string|max:30',
            'phone'=>'nullable|string|max:30',
            'email'=>'nullable|email|max:150',
            'address'=>'nullable|string',
            'join_date'=>'required|date',
        ]);
        $data['status']='active';
        Member::create($data);
        return redirect()->route('members.index')->with('success','Anggota berhasil ditambahkan.');
    }

    public function show(Member $member) { return view('members.show', compact('member')); }

    public function edit(Member $member) { return view('members.edit', compact('member')); }

    public function update(Request $request, Member $member) {
        $data = $request->validate([
            'member_no'=>'required|string|max:30|unique:members,member_no,'.$member->id,
            'name'=>'required|string|max:150',
            'nik'=>'nullable|string|max:30','phone'=>'nullable|string|max:30',
            'email'=>'nullable|email|max:150','address'=>'nullable|string',
            'join_date'=>'required|date','status'=>'required|in:active,inactive',
            'exit_date'=>'nullable|date|after_or_equal:join_date',
        ]);
        $member->update($data);
        return redirect()->route('members.index')->with('success','Data anggota diperbarui.');
    }

    public function updateStatus(Request $request, Member $member) {
        $data = $request->validate([
            'status'=>'required|in:active,inactive',
            'exit_date'=>'required_if:status,inactive|nullable|date|after_or_equal:join_date',
        ], [
            'exit_date.required_if' => 'Tanggal keluar wajib diisi saat menonaktifkan anggota.',
            'exit_date.after_or_equal' => 'Tanggal keluar tidak boleh sebelum tanggal gabung.',
        ]);

        if ($data['status'] === 'inactive' && $member->hasUnpaidLoans()) {
            return back()->with('error', 'Anggota tidak dapat dinonaktifkan karena masih memiliki pinjaman yang belum lunas (pending/aktif).');
        }

        $member->update([
            'status'    => $data['status'],
            'exit_date' => $data['status'] === 'inactive' ? $data['exit_date'] : null,
        ]);

        $message = $data['status'] === 'inactive'
            ? 'Anggota dinonaktifkan per ' . \Carbon\Carbon::parse($data['exit_date'])->format('d-m-Y') . '.'
            : 'Anggota berhasil diaktifkan kembali.';

        return redirect()->route('members.show', $member)->with('success', $message);
    }

    public function destroy(Member $member) {
        if ($member->hasUnpaidLoans()) {
            return back()->with('error', 'Anggota tidak dapat dinonaktifkan karena masih memiliki pinjaman yang belum lunas (pending/aktif).');
        }

        $member->update(['status'=>'inactive']);
        return back()->with('success','Anggota dinonaktifkan.');
    }
}
