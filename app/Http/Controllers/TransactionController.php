<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\FinanceService;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Exception;

class TransactionController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        $transactions = Transaction::with(['wallet', 'category', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $wallets = Wallet::where('user_id', auth()->id())->where('is_active', true)->get();
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'wallet_id' => 'required_if:type,income,expense',
            'category_id' => 'required_if:type,income,expense',
            'source_wallet_id' => 'required_if:type,transfer',
            'destination_wallet_id' => 'required_if:type,transfer|different:source_wallet_id',
        ], [
            'destination_wallet_id.different' => 'Dompet tujuan tidak boleh sama dengan dompet asal.',
        ]);

        try {
            // Simpan transaksi melalui FinanceService
            $transaction = $this->financeService->createTransaction($validated);

            // Susun pesan notifikasi WhatsApp
            $nominal = 'Rp ' . number_format($validated['amount'], 0, ',', '.');
            $desc = $validated['description'] ?? '-';

            if ($validated['type'] === 'transfer') {
                $source = Wallet::find($validated['source_wallet_id'])->name ?? 'Dompet Asal';
                $dest = Wallet::find($validated['destination_wallet_id'])->name ?? 'Dompet Tujuan';

                $pesan = "🔄 *Transfer Saldo Berhasil!*\n\n"
                       . "💰 *Nominal:* {$nominal}\n"
                       . "📤 *Dari:* {$source}\n"
                       . "📥 *Ke:* {$dest}\n"
                       . "📝 *Catatan:* {$desc}\n\n"
                       . "_PLMS Finance Management_";
            } else {
                $typeLabel = $validated['type'] === 'income' ? '🟢 Pemasukan' : '🔴 Pengeluaran';
                $walletName = Wallet::find($validated['wallet_id'])->name ?? 'Dompet Utama';
                $categoryName = Category::find($validated['category_id'])->name ?? 'Umum';

                $pesan = "🔔 *Transaksi Baru Tercatat!*\n\n"
                       . "📌 *Jenis:* {$typeLabel}\n"
                       . "📂 *Kategori:* {$categoryName}\n"
                       . "💰 *Nominal:* {$nominal}\n"
                       . "💳 *Dompet:* {$walletName}\n"
                       . "📝 *Catatan:* {$desc}\n\n"
                       . "_PLMS Finance Management_";
            }

            // Kirim notifikasi via Fonnte
            FonnteService::send($pesan);

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

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
