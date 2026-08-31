<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\FinanceService;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index(Request $request)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Log start query
        Log::info('Transaction Index - Start', [
            'user_id' => auth()->id(),
            'filters' => $request->only(['search', 'type', 'start_date', 'end_date']),
            'timestamp' => now()->toDateTimeString()
        ]);

        // Enable query log untuk debugging
        DB::enableQueryLog();

        $queryStart = microtime(true);

        // Inisialisasi Query Builder
        $query = Transaction::with(['wallet', 'category', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', auth()->id());

        // 1. Filter Pencarian (Keterangan atau Kategori)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($qCat) use ($search) {
                      $qCat->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Jenis Transaksi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 3. Filter Rentang Tanggal (Mulai)
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        // 4. Filter Rentang Tanggal (Akhir)
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Eksekusi Pengurutan dan Pagination
        $transactions = $query->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15);

        $queryTime = microtime(true) - $queryStart;

        // Get query log
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Log query performance
        Log::info('Transaction Index - Query Performance', [
            'user_id' => auth()->id(),
            'total_queries' => count($queries),
            'query_time' => round($queryTime * 1000, 2) . 'ms',
            'records_found' => $transactions->total(),
            'queries' => array_map(function($query) {
                return [
                    'sql' => $query['query'],
                    'bindings' => $query['bindings'],
                    'time' => round($query['time'] / 1000, 2) . 'ms'
                ];
            }, $queries)
        ]);

        // Log memory usage
        $endMemory = memory_get_usage();
        $endTime = microtime(true);

        Log::info('Transaction Index - Performance Summary', [
            'user_id' => auth()->id(),
            'total_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
            'memory_used' => round(($endMemory - $startMemory) / 1024, 2) . 'KB',
            'peak_memory' => round(memory_get_peak_usage() / 1024, 2) . 'KB',
            'paginate' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total()
            ]
        ]);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $startTime = microtime(true);

        Log::info('Transaction Create - Start', [
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);

        DB::enableQueryLog();

        // Query wallets
        $walletsQueryStart = microtime(true);
        $wallets = Wallet::where('user_id', auth()->id())->where('is_active', true)->get();
        $walletsQueryTime = microtime(true) - $walletsQueryStart;

        // Query categories
        $categoriesQueryStart = microtime(true);
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();
        $categoriesQueryTime = microtime(true) - $categoriesQueryStart;

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $endTime = microtime(true);

        Log::info('Transaction Create - Performance', [
            'user_id' => auth()->id(),
            'total_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
            'wallets_query_time' => round($walletsQueryTime * 1000, 2) . 'ms',
            'wallets_count' => $wallets->count(),
            'categories_query_time' => round($categoriesQueryTime * 1000, 2) . 'ms',
            'categories_count' => $categories->count(),
            'total_queries' => count($queries),
            'queries' => array_map(function($query) {
                return [
                    'sql' => $query['query'],
                    'time' => round($query['time'] / 1000, 2) . 'ms'
                ];
            }, $queries)
        ]);

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(Request $request)
    {
        $startTime = microtime(true);

        Log::info('Transaction Store - Start', [
            'user_id' => auth()->id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'timestamp' => now()->toDateTimeString()
        ]);

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
            DB::enableQueryLog();

            // Simpan transaksi melalui FinanceService
            $transactionStart = microtime(true);
            $transaction = $this->financeService->createTransaction($validated);
            $transactionTime = microtime(true) - $transactionStart;

            // Susun pesan notifikasi WhatsApp
            $nominal = 'Rp ' . number_format($validated['amount'], 0, ',', '.');
            $desc = $validated['description'] ?? '-';

            // Query for notification data
            $notifStart = microtime(true);
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
            $notifTime = microtime(true) - $notifStart;

            // Kirim notifikasi via Fonnte
            $fonnteStart = microtime(true);
            FonnteService::send($pesan);
            $fonnteTime = microtime(true) - $fonnteStart;

            $queries = DB::getQueryLog();
            DB::disableQueryLog();

            $endTime = microtime(true);

            // Log detailed performance
            Log::info('Transaction Store - Performance', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id ?? null,
                'type' => $validated['type'],
                'total_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
                'finance_service_time' => round($transactionTime * 1000, 2) . 'ms',
                'notification_data_time' => round($notifTime * 1000, 2) . 'ms',
                'fonnte_service_time' => round($fonnteTime * 1000, 2) . 'ms',
                'total_queries' => count($queries),
                'queries' => array_map(function($query) {
                    return [
                        'sql' => $query['query'],
                        'bindings' => $query['bindings'],
                        'time' => round($query['time'] / 1000, 2) . 'ms'
                    ];
                }, $queries)
            ]);

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
        } catch (Exception $e) {
            $endTime = microtime(true);

            Log::error('Transaction Store - Error', [
                'user_id' => auth()->id(),
                'type' => $request->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'time_elapsed' => round(($endTime - $startTime) * 1000, 2) . 'ms'
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        $startTime = microtime(true);

        Log::info('Transaction Edit - Start', [
            'user_id' => auth()->id(),
            'transaction_id' => $transaction->id,
            'timestamp' => now()->toDateTimeString()
        ]);

        DB::enableQueryLog();

        // Query wallets
        $walletsQueryStart = microtime(true);
        $allWallets = Wallet::where('user_id', auth()->id())->get();
        $walletsQueryTime = microtime(true) - $walletsQueryStart;

        // Query categories
        $categoriesQueryStart = microtime(true);
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();
        $categoriesQueryTime = microtime(true) - $categoriesQueryStart;

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $endTime = microtime(true);

        Log::info('Transaction Edit - Performance', [
            'user_id' => auth()->id(),
            'transaction_id' => $transaction->id,
            'total_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
            'wallets_query_time' => round($walletsQueryTime * 1000, 2) . 'ms',
            'wallets_count' => $allWallets->count(),
            'categories_query_time' => round($categoriesQueryTime * 1000, 2) . 'ms',
            'categories_count' => $categories->count(),
            'total_queries' => count($queries),
            'queries' => array_map(function($query) {
                return [
                    'sql' => $query['query'],
                    'time' => round($query['time'] / 1000, 2) . 'ms'
                ];
            }, $queries)
        ]);

        return view('transactions.edit', compact('transaction', 'allWallets', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        $startTime = microtime(true);

        Log::info('Transaction Update - Start', [
            'user_id' => auth()->id(),
            'transaction_id' => $transaction->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'timestamp' => now()->toDateTimeString()
        ]);

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
            DB::enableQueryLog();

            $updateStart = microtime(true);
            $this->financeService->updateTransaction($transaction, $validated);
            $updateTime = microtime(true) - $updateStart;

            $queries = DB::getQueryLog();
            DB::disableQueryLog();

            $endTime = microtime(true);

            Log::info('Transaction Update - Performance', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'total_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
                'finance_service_time' => round($updateTime * 1000, 2) . 'ms',
                'total_queries' => count($queries),
                'queries' => array_map(function($query) {
                    return [
                        'sql' => $query['query'],
                        'bindings' => $query['bindings'],
                        'time' => round($query['time'] / 1000, 2) . 'ms'
                    ];
                }, $queries)
            ]);

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (Exception $e) {
            $endTime = microtime(true);

            Log::error('Transaction Update - Error', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'time_elapsed' => round(($endTime - $startTime) * 1000, 2) . 'ms'
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        $startTime = microtime(true);

        Log::info('Transaction Destroy - Start', [
            'user_id' => auth()->id(),
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            DB::enableQueryLog();

            $deleteStart = microtime(true);
            $this->financeService->deleteTransaction($transaction);
            $deleteTime = microtime(true) - $deleteStart;

            $queries = DB::getQueryLog();
            DB::disableQueryLog();

            $endTime = microtime(true);

            Log::info('Transaction Destroy - Performance', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'total_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
                'finance_service_time' => round($deleteTime * 1000, 2) . 'ms',
                'total_queries' => count($queries),
                'queries' => array_map(function($query) {
                    return [
                        'sql' => $query['query'],
                        'bindings' => $query['bindings'],
                        'time' => round($query['time'] / 1000, 2) . 'ms'
                    ];
                }, $queries)
            ]);

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus dan saldo telah disesuaikan kembali.');
        } catch (Exception $e) {
            $endTime = microtime(true);

            Log::error('Transaction Destroy - Error', [
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'time_elapsed' => round(($endTime - $startTime) * 1000, 2) . 'ms'
            ]);

            return back()->with('error', $e->getMessage());
        }
    }
}
