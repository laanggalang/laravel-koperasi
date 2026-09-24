<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public function index(Request $request) {
        $allowedSorts = [
            'loan_no'        => 'loans.loan_no',
            'member'         => 'members.name',
            'installment_no' => 'installments.installment_no',
            'paid_date'      => 'installments.paid_date',
            'amount'         => 'installments.amount',
        ];

        $sortBy  = $allowedSorts[$request->query('sort_by')] ?? $allowedSorts['paid_date'];
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $installments = Installment::query()
            ->join('loans', 'loans.id', '=', 'installments.loan_id')
            ->join('members', 'members.id', '=', 'loans.member_id')
            ->select('installments.*')
            ->with('loan.member')
            ->when($request->query('q'), function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('loans.loan_no', 'like', "%{$term}%")
                      ->orWhere('members.name', 'like', "%{$term}%");
                });
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate(20)
            ->withQueryString();

        return view('installments.index', [
            'installments' => $installments,
            'sortBy'  => array_search($sortBy, $allowedSorts),
            'sortDir' => $sortDir,
        ]);
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

            $installment = Installment::create([
                ...$data,
                'installment_no'=>$number,
                'amount'=>$amount
            ]);

            // 💡 Update saldo & status pinjaman setelah angsuran dicatat
            $remaining = max(0, (float)$loan->remaining_balance - $amount);
            $loan->update([
                'remaining_balance'=>$remaining,
                'status'=>$remaining <= 0 ? 'paid' : 'active'
            ]);

            return $installment;
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
