<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $now = Carbon::now();

        // 1. Hitung Total Saldo dari semua dompet (aktif maupun nonaktif)
        $totalBalance = Wallet::where('user_id', $userId)->sum('balance');

        // 2. Hitung Total Pemasukan Bulan Ini
        $incomeThisMonth = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        // 3. Hitung Total Pengeluaran Bulan Ini
        $expenseThisMonth = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        // 4. Ambil 5 Transaksi Terakhir untuk ditampilkan di ringkasan
        $recentTransactions = Transaction::with(['wallet', 'category', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalBalance', 
            'incomeThisMonth', 
            'expenseThisMonth', 
            'recentTransactions'
        ));
    }
}
