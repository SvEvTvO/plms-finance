<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $today = Carbon::today();
        $lastMonth = $today->copy()->subMonth();

        /*
        |--------------------------------------------------------------------------
        | 1. SUMMARY CARDS
        |--------------------------------------------------------------------------
        */

        // Total saldo dari seluruh dompet aktif
        $totalBalance = Wallet::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('balance');


        // Pemasukan bulan ini
        $incomeThisMonth = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->sum('amount');


        // Pengeluaran bulan ini
        $expenseThisMonth = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | 2. PERBANDINGAN DENGAN BULAN LALU
        |--------------------------------------------------------------------------
        */

        $incomeLastMonth = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('date', $lastMonth->month)
            ->whereYear('date', $lastMonth->year)
            ->sum('amount');


        $expenseLastMonth = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $lastMonth->month)
            ->whereYear('date', $lastMonth->year)
            ->sum('amount');


        // Perubahan pemasukan
        $incomeChange = $incomeLastMonth > 0
            ? (($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100
            : ($incomeThisMonth > 0 ? 100 : 0);


        // Perubahan pengeluaran
        $expenseChange = $expenseLastMonth > 0
            ? (($expenseThisMonth - $expenseLastMonth) / $expenseLastMonth) * 100
            : ($expenseThisMonth > 0 ? 100 : 0);


        /*
        |--------------------------------------------------------------------------
        | 3. RINGKASAN ARUS KAS
        |--------------------------------------------------------------------------
        */

        // Arus kas bersih
        $netCashFlow = $incomeThisMonth - $expenseThisMonth;


        /*
        |--------------------------------------------------------------------------
        | 4. RASIO PENGELUARAN
        |--------------------------------------------------------------------------
        |
        | Contoh:
        | Pemasukan    = Rp157.174
        | Pengeluaran  = Rp116.000
        |
        | Expense Ratio = 73.8%
        |
        */

        $expenseRatio = $incomeThisMonth > 0
            ? ($expenseThisMonth / $incomeThisMonth) * 100
            : 0;

        // Batasi agar progress bar tidak lebih dari 100%
        $expenseRatio = min($expenseRatio, 100);


        // Persentase pemasukan yang masih tersisa
        $remainingRatio = $incomeThisMonth > 0
            ? ($netCashFlow / $incomeThisMonth) * 100
            : 0;

        $remainingRatio = max(0, min($remainingRatio, 100));


        /*
        |--------------------------------------------------------------------------
        | 5. STATUS KEUANGAN
        |--------------------------------------------------------------------------
        */

        if ($netCashFlow > 0) {

            $financialStatus = 'surplus';

            $financialStatusText = 'Arus kas masih positif bulan ini';

        } elseif ($netCashFlow < 0) {

            $financialStatus = 'deficit';

            $financialStatusText = 'Pengeluaran lebih besar dari pemasukan';

        } else {

            $financialStatus = 'balanced';

            $financialStatusText = 'Pemasukan dan pengeluaran seimbang';

        }


        /*
        |--------------------------------------------------------------------------
        | 6. DATA GRAFIK - 7 HARI TERAKHIR
        |--------------------------------------------------------------------------
        */

        $chartDates = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = $today->copy()->subDays($i);

            $chartDates[] = $date->format('d M');


            $chartIncome[] = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereDate('date', $date)
                ->sum('amount');


            $chartExpense[] = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereDate('date', $date)
                ->sum('amount');
        }


        /*
        |--------------------------------------------------------------------------
        | 7. TRANSAKSI TERAKHIR
        |--------------------------------------------------------------------------
        */

        $recentTransactions = Transaction::with([
                'wallet',
                'category'
            ])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 8. KIRIM DATA KE DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(

            // Summary
            'totalBalance',
            'incomeThisMonth',
            'expenseThisMonth',

            // Comparison
            'incomeChange',
            'expenseChange',

            // Cash flow
            'netCashFlow',
            'expenseRatio',
            'remainingRatio',

            // Financial status
            'financialStatus',
            'financialStatusText',

            // Chart
            'chartDates',
            'chartIncome',
            'chartExpense',

            // Transactions
            'recentTransactions'

        ));
    }
}
