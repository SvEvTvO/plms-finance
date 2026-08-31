<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\FinanceService;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        protected FinanceService $financeService
    ) {}

    public function index(Request $request): View
    {
        $userId = auth()->id();

        $query = Transaction::with(['wallet', 'category', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', $userId);

        // 1. Filter Pencarian (Keterangan / Kategori)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($qCat) use ($search) {
                      $qCat->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Jenis Transaksi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 3. Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        $userId = auth()->id();

        $wallets = Wallet::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = auth()->id();

        $validated = $request->validate([
            'type'                  => 'required|in:income,expense,transfer',
            'amount'                => 'required|numeric|min:1',
            'date'                  => 'required|date',
            'description'           => 'nullable|string|max:255',
            'wallet_id'             => [
                'required_if:type,income,expense',
                'nullable',
                Rule::exists('wallets', 'id')->where('user_id', $userId),
            ],
            'category_id'           => [
                'required_if:type,income,expense',
                'nullable',
                Rule::exists('categories', 'id')->where('user_id', $userId),
            ],
            'source_wallet_id'      => [
                'required_if:type,transfer',
                'nullable',
                Rule::exists('wallets', 'id')->where('user_id', $userId),
            ],
            'destination_wallet_id' => [
                'required_if:type,transfer',
                'nullable',
                'different:source_wallet_id',
                Rule::exists('wallets', 'id')->where('user_id', $userId),
            ],
        ], [
            'destination_wallet_id.different' => 'Dompet tujuan tidak boleh sama dengan dompet asal.',
        ]);

        try {
            $transaction = $this->financeService->createTransaction($validated);

            // Pengiriman Notifikasi WhatsApp (Non-blocking terhadap transaksi utama)
            $this->sendTransactionNotification($validated);

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
        } catch (Throwable $e) {
            Log::error('Transaction Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Transaction $transaction): View
    {
        $userId = auth()->id();
        abort_if($transaction->user_id !== $userId, 403);

        $allWallets = Wallet::where('user_id', $userId)->orderBy('name')->get();
        $categories = Category::where('user_id', $userId)->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'allWallets', 'categories'));
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $userId = auth()->id();
        abort_if($transaction->user_id !== $userId, 403);

        $validated = $request->validate([
            'type'                  => 'required|in:income,expense,transfer',
            'amount'                => 'required|numeric|min:1',
            'date'                  => 'required|date',
            'description'           => 'nullable|string|max:255',
            'wallet_id'             => [
                'required_if:type,income,expense',
                'nullable',
                Rule::exists('wallets', 'id')->where('user_id', $userId),
            ],
            'category_id'           => [
                'required_if:type,income,expense',
                'nullable',
                Rule::exists('categories', 'id')->where('user_id', $userId),
            ],
            'source_wallet_id'      => [
                'required_if:type,transfer',
                'nullable',
                Rule::exists('wallets', 'id')->where('user_id', $userId),
            ],
            'destination_wallet_id' => [
                'required_if:type,transfer',
                'nullable',
                'different:source_wallet_id',
                Rule::exists('wallets', 'id')->where('user_id', $userId),
            ],
        ], [
            'destination_wallet_id.different' => 'Dompet tujuan tidak boleh sama dengan dompet asal.',
        ]);

        try {
            $this->financeService->updateTransaction($transaction, $validated);
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (Throwable $e) {
            Log::error('Transaction Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        try {
            $this->financeService->deleteTransaction($transaction);
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus dan saldo telah disesuaikan kembali.');
        } catch (Throwable $e) {
            Log::error('Transaction Destroy Error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    private function sendTransactionNotification(array $validated): void
    {
        try {
            $nominal = 'Rp ' . number_format($validated['amount'], 0, ',', '.');
            $desc = $validated['description'] ?? '-';

            if ($validated['type'] === 'transfer') {
                $source = Wallet::find($validated['source_wallet_id'])->name ?? 'Dompet Asal';
                $dest   = Wallet::find($validated['destination_wallet_id'])->name ?? 'Dompet Tujuan';

                $pesan = "🔄 *Transfer Saldo Berhasil!*\n\n"
                       . "💰 *Nominal:* {$nominal}\n"
                       . "📤 *Dari:* {$source}\n"
                       . "📥 *Ke:* {$dest}\n"
                       . "📝 *Catatan:* {$desc}\n\n"
                       . "_PLMS Finance Management_";
            } else {
                $typeLabel    = $validated['type'] === 'income' ? '🟢 Pemasukan' : '🔴 Pengeluaran';
                $walletName   = Wallet::find($validated['wallet_id'])->name ?? 'Dompet Utama';
                $categoryName = Category::find($validated['category_id'])->name ?? 'Umum';

                $pesan = "🔔 *Transaksi Baru Tercatat!*\n\n"
                       . "📌 *Jenis:* {$typeLabel}\n"
                       . "📂 *Kategori:* {$categoryName}\n"
                       . "💰 *Nominal:* {$nominal}\n"
                       . "💳 *Dompet:* {$walletName}\n"
                       . "📝 *Catatan:* {$desc}\n\n"
                       . "_PLMS Finance Management_";
            }

            FonnteService::send($pesan);
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim notifikasi WhatsApp: ' . $e->getMessage());
        }
    }
}
