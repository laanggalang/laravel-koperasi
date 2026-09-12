<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\Installment;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'memberCount' => Member::where('status','active')->count(),
            'savingTotal' => Saving::sum('amount'),
            'loanTotal' => Loan::sum('principal'),
            'loanOutstanding' => Loan::where('status','active')->sum('remaining_balance'),
            'installmentTotal' => Installment::sum('amount'),
        ]);
    }
}
