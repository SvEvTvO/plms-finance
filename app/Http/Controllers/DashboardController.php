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
        | 1. TOTAL SALDO
        |--------------------------------------------------------------------------
        */
        $totalBalance = Wallet::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('balance');

        /*
        |--------------------------------------------------------------------------
        | 2. REKAP BULANAN (Bulan Ini & Bulan Lalu dalam 1 Query)
        |--------------------------------------------------------------------------
        */
        $startOfLastMonth = $lastMonth->copy()->startOfMonth()->format('Y-m-d');
        $endOfThisMonth   = $today->copy()->endOfMonth()->format('Y-m-d');

        $monthlyData = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$startOfLastMonth, $endOfThisMonth])
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw('EXTRACT(YEAR FROM date) as yr, EXTRACT(MONTH FROM date) as mo, type, sum(amount) as total')
            ->groupByRaw('EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date), type')
            ->get();

        // Helper untuk ekstrak nilai dari collection
        $getMonthlySum = function ($year, $month, $type) use ($monthlyData) {
            $item = $monthlyData->first(function ($d) use ($year, $month, $type) {
                return $d->yr == $year && $d->mo == $month && $d->type === $type;
            });
            return $item ? (float) $item->total : 0;
        };

        $incomeThisMonth  = $getMonthlySum($today->year, $today->month, 'income');
        $expenseThisMonth = $getMonthlySum($today->year, $today->month, 'expense');
        $incomeLastMonth  = $getMonthlySum($lastMonth->year, $lastMonth->month, 'income');
        $expenseLastMonth = $getMonthlySum($lastMonth->year, $lastMonth->month, 'expense');

        /*
        |--------------------------------------------------------------------------
        | 3. PERBANDINGAN, ARUS KAS & STATUS
        |--------------------------------------------------------------------------
        */
        $incomeChange = $incomeLastMonth > 0
            ? (($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100
            : ($incomeThisMonth > 0 ? 100 : 0);

        $expenseChange = $expenseLastMonth > 0
            ? (($expenseThisMonth - $expenseLastMonth) / $expenseLastMonth) * 100
            : ($expenseThisMonth > 0 ? 100 : 0);

        $netCashFlow = $incomeThisMonth - $expenseThisMonth;

        $expenseRatio = $incomeThisMonth > 0
            ? min(($expenseThisMonth / $incomeThisMonth) * 100, 100)
            : 0;

        $remainingRatio = $incomeThisMonth > 0
            ? max(0, min(($netCashFlow / $incomeThisMonth) * 100, 100))
            : 0;

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
        | 4. DATA GRAFIK - 7 HARI TERAKHIR (Cukup 1 Query)
        |--------------------------------------------------------------------------
        */
        $chartStartDate = $today->copy()->subDays(6)->format('Y-m-d');

        $chartDataRaw = Transaction::where('user_id', $userId)
            ->whereDate('date', '>=', $chartStartDate)
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw('date, type, sum(amount) as total')
            ->groupBy('date', 'type')
            ->get();

        $chartDates = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateObj = $today->copy()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');

            $chartDates[] = $dateObj->format('d M');

            $inc = $chartDataRaw->first(function ($item) use ($dateStr) {
                return Carbon::parse($item->date)->format('Y-m-d') === $dateStr && $item->type === 'income';
            });
            $chartIncome[] = $inc ? (float) $inc->total : 0;

            $exp = $chartDataRaw->first(function ($item) use ($dateStr) {
                return Carbon::parse($item->date)->format('Y-m-d') === $dateStr && $item->type === 'expense';
            });
            $chartExpense[] = $exp ? (float) $exp->total : 0;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. TRANSAKSI TERAKHIR
        |--------------------------------------------------------------------------
        */
        $recentTransactions = Transaction::with(['wallet', 'category'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBalance',
            'incomeThisMonth',
            'expenseThisMonth',
            'incomeChange',
            'expenseChange',
            'netCashFlow',
            'expenseRatio',
            'remainingRatio',
            'financialStatus',
            'financialStatusText',
            'chartDates',
            'chartIncome',
            'chartExpense',
            'recentTransactions'
        ));
    }
}
