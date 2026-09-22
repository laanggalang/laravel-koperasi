<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\Installment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $savingsTrend = $this->monthlyTotals(
            (new Saving)->getTable(),
            'amount',
            'transaction_date'
        );
        $installmentsTrend = $this->monthlyTotals(
            (new Installment)->getTable(),
            'amount',
            'paid_date'
        );

        return view('dashboard', [
            'memberCount'       => Member::where('status', 'active')->count(),
            'savingTotal'       => Saving::sum('amount'),
            'loanTotal'         => Loan::sum('principal'),
            'loanOutstanding'   => Loan::where('status', 'active')->sum('remaining_balance'),
            'installmentTotal'  => Installment::sum('amount'),
            'chartLabels'       => $savingsTrend->pluck('month')->values(),
            'chartSavings'      => $savingsTrend->pluck('total')->values(),
            'chartInstallments' => $installmentsTrend->pluck('total')->values(),
        ]);
    }

    /**
     * Total per bulan 6 bulan terakhir, termasuk bulan kosong (diisi 0).
     */
    private function monthlyTotals(string $table, string $column, string $dateColumn)
    {
        $start = now()->subMonths(5)->startOfMonth()->toDateString();

        $rows = DB::table($table)
            ->selectRaw("DATE_FORMAT({$dateColumn}, '%Y-%m') as ym, SUM({$column}) as total")
            ->where($dateColumn, '>=', $start)
            ->groupBy('ym')
            ->pluck('total', 'ym');

        return collect(range(5, 0))->map(function ($i) use ($rows) {
            $ym = now()->subMonths($i)->format('Y-m');
            return (object) [
                'month' => now()->subMonths($i)->translatedFormat('M Y'),
                'total' => (float) ($rows[$ym] ?? 0),
            ];
        });
    }
}
