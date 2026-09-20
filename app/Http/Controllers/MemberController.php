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
            'join_date'=>'required|date','status'=>'required|in:active,inactive'
        ]);
        $member->update($data);
        return redirect()->route('members.index')->with('success','Data anggota diperbarui.');
    }

    public function destroy(Member $member) {
        $member->update(['status'=>'inactive']);
        return back()->with('success','Anggota dinonaktifkan.');
    }
}
