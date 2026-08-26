<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\FinanceService;
use App\Services\FonnteService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FonnteWebhookController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * Endpoint utama penerima Webhook dari Fonnte
     */
    public function handle(Request $request)
    {
        $sender = $request->input('sender');
        $message = trim($request->input('message', ''));

        Log::info("Fonnte Webhook Inbound [{$sender}]: {$message}");

        if (empty($message) || empty($sender)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // 1. Identifikasi User
        $user = $this->findUserByPhone($sender);

        // Pastikan cek keberadaan user dulu!
        if (!$user) {
            $msg = "⚠️ *Nomor Tidak Terdaftar*\n\n"
                . "Nomor WhatsApp Anda ({$sender}) belum terhubung dengan akun *PLMS Finance*.\n"
                . "Silakan daftarkan nomor ini di menu Pengaturan Profil pada aplikasi web.";
            FonnteService::send($msg, $sender);
            return response()->json(['status' => 'unregistered user'], 200);
        }

        auth()->login($user);

        // 2. Siapkan command bersih
        $cleanCommand = strtoupper(trim($message));

        // 3. Pengecekan Khusus Admin (Aman karena $user & $cleanCommand sudah valid)
        if ($user->is_admin) {
            if ($cleanCommand === 'ADMIN' || $cleanCommand === 'STATISTIK') {
                $totalUsers = User::count();
                $todayTransactions = Transaction::whereDate('created_at', today())->count();
                $todayVolume = Transaction::whereDate('created_at', today())->sum('amount');

                $msg = "🛡️ *PLMS SYSTEM MONITORING (ADMIN)*\n\n"
                    . "👥 *Total Pengguna:* {$totalUsers} Akun\n"
                    . "📝 *Transaksi Hari Ini:* {$todayTransactions} Transaksi\n"
                    . "💰 *Volume Perputaran:* Rp " . number_format($todayVolume, 0, ',', '.') . "\n\n"
                    . "_Server Status: Active_";

                FonnteService::send($msg, $sender);
                return response()->json(['status' => 'admin stats sent'], 200);
            }
        }

        // 2. Evaluasi Command Bantu
        $cleanCommand = strtoupper(trim($message));
        switch ($cleanCommand) {
            case 'BANTUAN':
            case 'MENU':
            case 'HELP':
            case 'PANDUAN':
                $this->replyHelp($sender);
                return response()->json(['status' => 'help delivered'], 200);

            case 'SALDO':
            case 'DOMPET':
                $this->replySaldo($user, $sender);
                return response()->json(['status' => 'saldo delivered'], 200);

            case 'REKAP':
            case 'RINGKASAN':
            case 'LAPORAN':
                $this->replyRekap($user, $sender);
                return response()->json(['status' => 'rekap delivered'], 200);

            case 'RIWAYAT':
            case 'HISTORY':
            case 'LOG':
                $this->replyRiwayat($user, $sender);
                return response()->json(['status' => 'riwayat delivered'], 200);
        }

        // 3. Evaluasi Pesan Transaksi
        try {
            // A. Cek apakah format Transfer Saldo
            if (preg_match('/^transfer\s+/i', $message)) {
                $this->processTransfer($user, $message, $sender);
                return response()->json(['status' => 'transfer processed'], 200);
            }

            // B. Cek apakah format Terstruktur Multi-Baris (mengandung ':')
            if (str_contains($message, ':')) {
                $this->processStructuredTransaction($user, $message, $sender);
                return response()->json(['status' => 'structured transaction processed'], 200);
            }

            // C. Format Natural / 1 Baris
            $this->processNaturalTransaction($user, $message, $sender);
            return response()->json(['status' => 'natural transaction processed'], 200);

        } catch (Exception $e) {
            Log::error("Fonnte Transaction Error: " . $e->getMessage());
            FonnteService::send("⚠️ *Gagal Mencatat:* " . $e->getMessage(), $sender);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Helper: Cari user berdasarkan variasi format nomor HP (08xx atau 628xx)
     */
    private function findUserByPhone(string $sender): ?User
    {
        $clean = preg_replace('/[^0-9]/', '', $sender);
        $variants = [$clean];

        if (str_starts_with($clean, '62')) {
            $variants[] = '0' . substr($clean, 2);
        } elseif (str_starts_with($clean, '0')) {
            $variants[] = '62' . substr($clean, 1);
        }

        return User::whereIn('whatsapp_number', $variants)->first();
    }

    /**
     * Command: BANTUAN
     */
    private function replyHelp(string $sender): void
    {
        $text = "🤖 *PANDUAN BOT PLMS FINANCE*\n\n"
              . "📋 *Command Bantu:*\n"
              . "• *SALDO* : Cek saldo semua dompet\n"
              . "• *REKAP* : Rekap pemasukan & pengeluaran bulan ini\n"
              . "• *RIWAYAT* : 5 transaksi terakhir\n"
              . "• *BANTUAN* : Menampilkan menu ini\n\n"
              . "💸 *Format Transaksi 1 Baris (Cepat):*\n"
              . "• *Pengeluaran:* `Makan Siang 25000 GoPay`\n"
              . "• *Pemasukan:* `+ Gaji 5000000 BCA`\n"
              . "• *Transfer:* `Transfer 50000 BCA ke GoPay`\n\n"
              . "📝 *Format Terstruktur:*\n"
              . "Jenis : Pengeluaran\n"
              . "Nominal : 25000\n"
              . "Dompet : Cash\n"
              . "Kategori : Makanan\n"
              . "Keterangan : Nasi Padang";

        FonnteService::send($text, $sender);
    }

    /**
     * Command: SALDO
     */
    private function replySaldo(User $user, string $sender): void
    {
        $wallets = Wallet::where('user_id', $user->id)->where('is_active', true)->get();

        if ($wallets->isEmpty()) {
            FonnteService::send("⚠️ Anda belum memiliki dompet aktif di sistem.", $sender);
            return;
        }

        $total = $wallets->sum('balance');
        $lines = [];
        foreach ($wallets as $w) {
            $lines[] = "• *{$w->name}:* Rp " . number_format($w->balance, 0, ',', '.');
        }

        $msg = "💳 *INFORMASI SALDO DOMPET*\n\n"
             . implode("\n", $lines) . "\n"
             . "────────────────────\n"
             . "💰 *Total Aset:* Rp " . number_format($total, 0, ',', '.') . "\n\n"
             . "_PLMS Finance Management_";

        FonnteService::send($msg, $sender);
    }

    /**
     * Command: REKAP
     */
    private function replyRekap(User $user, string $sender): void
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $income = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $expense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $net = $income - $expense;
        $netSign = $net >= 0 ? '🟢 Surplus' : '🔴 Defisit';

        $monthName = Carbon::now()->translatedFormat('F Y');

        $msg = "📊 *REKAP FINANSIAL ({$monthName})*\n\n"
             . "🟢 *Pemasukan:* Rp " . number_format($income, 0, ',', '.') . "\n"
             . "🔴 *Pengeluaran:* Rp " . number_format($expense, 0, ',', '.') . "\n"
             . "────────────────────\n"
             . "📈 *Cashflow:* Rp " . number_format($net, 0, ',', '.') . " ({$netSign})\n\n"
             . "_PLMS Finance Management_";

        FonnteService::send($msg, $sender);
    }

    /**
     * Command: RIWAYAT
     */
    private function replyRiwayat(User $user, string $sender): void
    {
        $transactions = Transaction::with(['wallet', 'category', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        if ($transactions->isEmpty()) {
            FonnteService::send("Belum ada riwayat transaksi yang tercatat.", $sender);
            return;
        }

        $lines = [];
        foreach ($transactions as $idx => $t) {
            $num = $idx + 1;
            $amt = 'Rp ' . number_format($t->amount, 0, ',', '.');
            $date = Carbon::parse($t->date)->format('d/m');

            if ($t->type === 'transfer') {
                $src = $t->sourceWallet->name ?? 'Asal';
                $dst = $t->destinationWallet->name ?? 'Tujuan';
                $lines[] = "{$num}. [🔄] {$amt} ({$src} ➔ {$dst}) _{$date}_";
            } elseif ($t->type === 'income') {
                $lines[] = "{$num}. [🟢] {$t->description} - {$amt} ({$t->wallet->name}) _{$date}_";
            } else {
                $lines[] = "{$num}. [🔴] {$t->description} - {$amt} ({$t->wallet->name}) _{$date}_";
            }
        }

        $msg = "⏱ *5 TRANSAKSI TERAKHIR*\n\n"
             . implode("\n", $lines) . "\n\n"
             . "_PLMS Finance Management_";

        FonnteService::send($msg, $sender);
    }

    /**
     * Parser: Transfer Antar Dompet
     * Format: Transfer 50000 BCA ke GoPay
     */
    private function processTransfer(User $user, string $message, string $sender): void
    {
        if (!preg_match('/transfer\s+([0-9\.\,kK]+)\s+(.+?)\s+(?:ke|>)\s+(.+)/i', $message, $matches)) {
            throw new Exception("Format transfer tidak dikenali.\nContoh: *Transfer 50000 BCA ke GoPay*");
        }

        $amount = $this->parseAmount($matches[1]);
        $sourceName = trim($matches[2]);
        $destName = trim($matches[3]);

        $sourceWallet = Wallet::where('user_id', $user->id)
            ->where('name', 'ILIKE', "%{$sourceName}%")
            ->where('is_active', true)
            ->first();

        $destWallet = Wallet::where('user_id', $user->id)
            ->where('name', 'ILIKE', "%{$destName}%")
            ->where('is_active', true)
            ->first();

        if (!$sourceWallet || !$destWallet) {
            throw new Exception("Salah satu dompet tidak ditemukan atau tidak aktif.");
        }

        if ($sourceWallet->id === $destWallet->id) {
            throw new Exception("Dompet asal dan tujuan tidak boleh sama.");
        }

        $payload = [
            'user_id' => $user->id,
            'type' => 'transfer',
            'amount' => $amount,
            'date' => now()->toDateString(),
            'description' => "Transfer via WA",
            'source_wallet_id' => $sourceWallet->id,
            'destination_wallet_id' => $destWallet->id,
        ];

        $this->financeService->createTransaction($payload);

        $msg = "🔄 *Transfer Saldo Berhasil!*\n\n"
             . "💰 *Nominal:* Rp " . number_format($amount, 0, ',', '.') . "\n"
             . "📤 *Dari:* {$sourceWallet->name} (Sisa: Rp " . number_format($sourceWallet->fresh()->balance, 0, ',', '.') . ")\n"
             . "📥 *Ke:* {$destWallet->name} (Total: Rp " . number_format($destWallet->fresh()->balance, 0, ',', '.') . ")\n\n"
             . "_PLMS Finance Management_";

        FonnteService::send($msg, $sender);
    }

    /**
     * Parser: Format Terstruktur Multi-Baris
     */
    private function processStructuredTransaction(User $user, string $message, string $sender): void
    {
        $lines = explode("\n", $message);
        $dataMap = [];

        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $val] = explode(':', $line, 2);
                $dataMap[strtolower(trim($key))] = trim($val);
            }
        }

        if (!isset($dataMap['jenis']) || !isset($dataMap['nominal'])) {
            throw new Exception("Format salah. Mohon lengkapi baris *Jenis* dan *Nominal*.");
        }

        $type = str_contains(strtolower($dataMap['jenis']), 'masuk') ? 'income' : 'expense';
        $amount = $this->parseAmount($dataMap['nominal']);

        $wallet = $this->findWalletOrDefault($user->id, $dataMap['dompet'] ?? null);
        $category = $this->findOrCreateCategory($user->id, $type, $dataMap['kategori'] ?? null);

        $payload = [
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'date' => now()->toDateString(),
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'description' => $dataMap['keterangan'] ?? $dataMap['catatan'] ?? $dataMap['ket'] ?? '-',
        ];

        $this->financeService->createTransaction($payload);
        $this->sendTransactionSuccessReceipt($payload, $wallet, $category, $sender);
    }

    /**
     * Parser: Format Natural 1 Baris
     * Contoh: "Makan Siang 25000 GoPay" atau "+ Gaji 5000000 BCA"
     */
    private function processNaturalTransaction(User $user, string $message, string $sender): void
    {
        $tokens = preg_split('/\s+/', $message);
        if (empty($tokens)) {
            throw new Exception("Pesan kosong.");
        }

        $isIncome = false;
        if ($tokens[0] === '+' || strtolower($tokens[0]) === 'masuk') {
            $isIncome = true;
            array_shift($tokens);
        }

        $type = $isIncome ? 'income' : 'expense';

        // Temukan index angka nominal
        $amountIndex = -1;
        $amount = 0;

        foreach ($tokens as $idx => $token) {
            $parsedVal = $this->parseAmount($token, false);
            if ($parsedVal > 0) {
                $amount = $parsedVal;
                $amountIndex = $idx;
                break;
            }
        }

        if ($amountIndex === -1 || $amount <= 0) {
            throw new Exception("Nominal transaksi tidak ditemukan. Contoh: *Makan Siang 25000 GoPay* atau ketik *BANTUAN*.");
        }

        // Teks sebelum angka = Deskripsi
        $descWords = array_slice($tokens, 0, $amountIndex);
        $description = !empty($descWords) ? implode(' ', $descWords) : ($isIncome ? 'Pemasukan' : 'Pengeluaran');

        // Teks setelah angka = Dompet (jika ada)
        $walletWords = array_slice($tokens, $amountIndex + 1);
        $walletQuery = !empty($walletWords) ? implode(' ', $walletWords) : null;

        $wallet = $this->findWalletOrDefault($user->id, $walletQuery);
        $category = $this->findOrCreateCategory($user->id, $type, $description);

        $payload = [
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'date' => now()->toDateString(),
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'description' => $description,
        ];

        $this->financeService->createTransaction($payload);
        $this->sendTransactionSuccessReceipt($payload, $wallet, $category, $sender);
    }

    /**
     * Helper: Kirim Struk Transaksi Sukses
     */
    private function sendTransactionSuccessReceipt(array $payload, Wallet $wallet, Category $category, string $sender): void
    {
        $typeLabel = $payload['type'] === 'income' ? '🟢 Pemasukan' : '🔴 Pengeluaran';
        $nominal = 'Rp ' . number_format($payload['amount'], 0, ',', '.');
        $sisaSaldo = 'Rp ' . number_format($wallet->fresh()->balance, 0, ',', '.');

        $msg = "✅ *Transaksi Berhasil Dicatat!*\n\n"
             . "📌 *Jenis:* {$typeLabel}\n"
             . "📂 *Kategori:* {$category->name}\n"
             . "💰 *Nominal:* {$nominal}\n"
             . "💳 *Dompet:* {$wallet->name} (Sisa: {$sisaSaldo})\n"
             . "📝 *Catatan:* {$payload['description']}\n\n"
             . "_PLMS Finance Management_";

        FonnteService::send($msg, $sender);
    }

    /**
     * Helper: Ekstraksi angka (mendukung '25k', '50.000', '15000')
     */
    private function parseAmount(string $val, bool $strict = true): float
    {
        $val = strtolower(trim($val));
        $multiplier = 1;

        if (str_ends_with($val, 'k')) {
            $multiplier = 1000;
            $val = substr($val, 0, -1);
        }

        $cleaned = preg_replace('/[^0-9]/', '', $val);
        $num = (float) $cleaned * $multiplier;

        if ($strict && $num <= 0) {
            throw new Exception("Nominal harus berupa angka yang valid.");
        }

        return $num;
    }

    /**
     * Helper: Cari dompet berdasarkan nama atau fallback ke dompet default
     */
    private function findWalletOrDefault(int $userId, ?string $query): Wallet
    {
        if (!empty($query)) {
            $wallet = Wallet::where('user_id', $userId)
                ->where('name', 'ILIKE', "%{$query}%")
                ->where('is_active', true)
                ->first();

            if ($wallet) return $wallet;
        }

        $defaultWallet = Wallet::where('user_id', $userId)->where('is_default', true)->first()
                      ?? Wallet::where('user_id', $userId)->where('is_active', true)->first();

        if (!$defaultWallet) {
            throw new Exception("Anda belum memiliki dompet aktif.");
        }

        return $defaultWallet;
    }

    /**
     * Helper: Cari kategori yang mirip atau otomatis buat baru jika belum ada
     */
    private function findOrCreateCategory(int $userId, string $type, ?string $name): Category
    {
        $catName = !empty($name) ? trim($name) : ($type === 'income' ? 'Pemasukan Umum' : 'Pengeluaran Umum');

        $category = Category::where('user_id', $userId)
            ->where('type', $type)
            ->where('name', 'ILIKE', "%{$catName}%")
            ->first();

        if (!$category) {
            $category = Category::create([
                'user_id' => $userId,
                'name' => ucfirst($catName),
                'type' => $type,
                'icon' => $type === 'income' ? 'ti ti-arrow-down-left' : 'ti ti-arrow-up-right',
                'color' => $type === 'income' ? '#10b981' : '#ef4444',
            ]);
        }

        return $category;
    }
}
