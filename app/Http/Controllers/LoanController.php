<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    public function index() {
        $loans = Loan::with('member')->latest()->paginate(15);
        return view('loans.index', compact('loans'));
    }

    public function create() {
        $members = Member::where('status','active')->orderBy('name')->get();
        return view('loans.create', compact('members'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'member_id'=>'required|exists:members,id',
            'principal'=>'required|numeric|min:1',
            'interest_rate'=>'required|numeric|min:0',
            'tenor'=>'required|integer|min:1|max:120',
            'start_date'=>'required|date',
            'notes'=>'nullable|string|max:500'
        ]);

        // Flat interest: bunga total = pokok x rate x tenor / 100
        $interest = $data['principal'] * ($data['interest_rate'] / 100) * $data['tenor'];
        $total = $data['principal'] + $interest;
        $monthly = $total / $data['tenor'];

        $loan = Loan::create([
            ...$data,
            'loan_no' => 'PJ-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
            'monthly_payment' => round($monthly, 2),
            'total_payment' => round($total, 2),
            'remaining_balance' => round($total, 2),
            'status' => 'active',
        ]);

        return redirect()->route('loans.show',$loan)->with('success','Pinjaman berhasil dibuat.');
    }

    public function show(Loan $loan) {
        $loan->load('member','installments');
        return view('loans.show', compact('loan'));
    }

    public function update(Request $request, Loan $loan) {
        $request->validate(['status'=>'required|in:active,paid,cancelled']);
        $loan->update(['status'=>$request->status]);
        return back()->with('success','Status pinjaman diperbarui.');
    }
}
