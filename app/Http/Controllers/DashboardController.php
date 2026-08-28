<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // 1. Data untuk Kartu Ringkasan (Summary Cards)
        $totalBalance = Wallet::where('user_id', $userId)->where('is_active', true)->sum('balance');

        $incomeThisMonth = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('date', $today->month)
            ->sum('amount');

        $expenseThisMonth = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $today->month)
            ->sum('amount');

        // 2. Data Grafik: 7 Hari Terakhir
        $chartDates = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
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

        // 3. Transaksi Terakhir (Untuk tabel di sebelah grafik)
        $recentTransactions = Transaction::with(['wallet', 'category'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBalance', 'incomeThisMonth', 'expenseThisMonth',
            'chartDates', 'chartIncome', 'chartExpense', 'recentTransactions'
        ));
    }
}
