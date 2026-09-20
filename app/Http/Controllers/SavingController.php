<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\Models\Member;
use Illuminate\Http\Request;

class SavingController extends Controller
{
    public function index(Request $request) {
        $allowedSorts = [
            'id'               => 'savings.id',
            'member'           => 'members.name',
            'transaction_date' => 'savings.transaction_date',
            'type'             => 'savings.type',
            'amount'           => 'savings.amount',
        ];

        $sortBy  = $allowedSorts[$request->query('sort_by')] ?? $allowedSorts['transaction_date'];
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $savings = Saving::query()
            ->join('members', 'members.id', '=', 'savings.member_id')
            ->select('savings.*')
            ->with('member')
            ->orderBy($sortBy, $sortDir)
            ->paginate(20)
            ->withQueryString();

        return view('savings.index', [
            'savings' => $savings,
            'sortBy'  => array_search($sortBy, $allowedSorts),
            'sortDir' => $sortDir,
        ]);
    }

    public function create() {
        $members = Member::where('status','active')->orderBy('name')->get();
        return view('savings.create', compact('members'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'saving_no'=>'required|string|max:255',
            'member_id'=>'required|exists:members,id',
            'type'=>'required|in:pokok,wajib,sukarela',
            'amount'=>'required|numeric|min:1',
            'transaction_date'=>'required|date',
            'description'=>'nullable|string|max:255'
        ]);
        Saving::create($data);
        return redirect()->route('savings.index')->with('success','Simpanan berhasil dicatat.');
    }

    public function show(Saving $saving) {
        $saving->load('member');
        return view('savings.show', compact('saving'));
    }
}
