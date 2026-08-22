<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        $wallets = Wallet::where('user_id', auth()->id())->get();
        $recentTransactions = Transaction::where('user_id', auth()->id())
            ->with('wallet', 'category')->latest('date')->paginate(15);
        return view('finance.index', compact('wallets', 'recentTransactions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|integer|min:1',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $this->financeService->createTransaction($validated + ['user_id' => auth()->id()]);
        return back()->with('success', 'Transaksi berhasil ditambahkan.');
    }

    // update, destroy, tracking ...
}
