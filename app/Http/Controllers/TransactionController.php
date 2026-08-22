<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Exception;

class TransactionController extends Controller
{
    protected $financeService;

    // Menginjeksi FinanceService ke dalam Controller
    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        // Fitur Filter & Search akan ditambahkan di Phase 8. 
        // Saat ini kita ambil daftar transaksi standar.
        $transactions = Transaction::with(['wallet', 'category', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        // Hanya tampilkan dompet yang aktif untuk transaksi baru
        $wallets = Wallet::where('user_id', auth()->id())->where('is_active', true)->get();
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(Request $request)
    {
        // Validasi request sesuai tipe transaksi
        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            
            // Wajib jika bukan transfer
            'wallet_id' => 'required_if:type,income,expense',
            'category_id' => 'required_if:type,income,expense',
            
            // Wajib jika transfer (dan source/destination tidak boleh sama)
            'source_wallet_id' => 'required_if:type,transfer',
            'destination_wallet_id' => 'required_if:type,transfer|different:source_wallet_id',
        ], [
            'destination_wallet_id.different' => 'Dompet tujuan tidak boleh sama dengan dompet asal.',
        ]);

        try {
            // Lempar logika finansial ke Service
            $this->financeService->createTransaction($validated);
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        // Ambil semua dompet (termasuk yang nonaktif, barangkali transaksi lama pakai dompet nonaktif)
        $allWallets = Wallet::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'allWallets', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            
            'wallet_id' => 'required_if:type,income,expense',
            'category_id' => 'required_if:type,income,expense',
            
            'source_wallet_id' => 'required_if:type,transfer',
            'destination_wallet_id' => 'required_if:type,transfer|different:source_wallet_id',
        ]);

        try {
            $this->financeService->updateTransaction($transaction, $validated);
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        try {
            $this->financeService->deleteTransaction($transaction);
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus dan saldo telah disesuaikan kembali.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
