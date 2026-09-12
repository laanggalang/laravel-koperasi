<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public function index() {
        $installments = Installment::with('loan.member')->latest('paid_date')->paginate(20);
        return view('installments.index', compact('installments'));
    }

    public function create() {
        $loans = Loan::where('status','active')->with('member')->orderBy('loan_no')->get();
        return view('installments.create', compact('loans'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'loan_id'=>'required|exists:loans,id',
            'amount'=>'required|numeric|min:1',
            'paid_date'=>'required|date',
            'notes'=>'nullable|string|max:255'
        ]);
    
        // 💡 Tampung hasil DB::transaction ke dalam sebuah variabel
        $installment = DB::transaction(function () use ($data) {
            $loan = Loan::lockForUpdate()->findOrFail($data['loan_id']);
            $number = $loan->installments()->count() + 1;
            $amount = min((float)$data['amount'], (float)$loan->remaining_balance);
    
            // 💡 Tambahkan kata 'return' sebelum Installment::create
            return Installment::create([
                ...$data,
                'installment_no'=>$number,
                'amount'=>$amount
            ]);
    
            $remaining = max(0, (float)$loan->remaining_balance - $amount);
            $loan->update([
                'remaining_balance'=>$remaining,
                'status'=>$remaining <= 0 ? 'paid' : 'active'
            ]);
        });
    
        // 💡 Sekarang variabel $installment sudah aman dilemparkan ke rute show
        return redirect()->route('installments.show', $installment)->with('success','Angsuran berhasil dicatat.');
    }
    
    public function show(Installment $installment) {
        // Memuat data relasi pinjaman dan anggota agar bisa tampil di kwitansi
        $installment->load('loan.member');
        return view('installments.show', compact('installment'));
    }

}
