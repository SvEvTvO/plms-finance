<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\FonnteService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteWebhookController extends Controller
{
    // KITA HAPUS __construct FinanceService agar tidak Crash

    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return response()->json(['status' => 'success', 'message' => 'Webhook Active!'], 200);
        }

        try {
            // 1. BERSIHKAN NOMOR SEJAK AWAL (Buang karakter aneh seperti @c.us dari Fonnte)
            $rawSender = $request->input('sender', '');
            $sender = preg_replace('/[^0-9]/', '', $rawSender);

            // Antisipasi API Fonnte (Bisa pakai key 'message' atau 'text')
            $message = trim($request->input('message') ?? $request->input('text') ?? '');

            Log::info("Fonnte INBOUND [{$sender}]: {$message}");

            if (empty($message) || empty($sender)) {
                return response()->json(['status' => 'ignored'], 200);
            }

            // 2. Identifikasi User
            $user = $this->findUserByPhone($sender);

            if (!$user) {
                // Jika nomor tidak terdaftar, abaikan saja agar tidak spam ke nomor orang lain
                Log::info("Fonnte: Nomor {$sender} tidak terdaftar.");
                return response()->json(['status' => 'unregistered'], 200);
            }

            $cleanCommand = strtoupper(trim($message));

            // 3. Evaluasi Command Bantu Interaktif
            switch ($cleanCommand) {
                case 'BANTUAN':
                case 'MENU':
                case 'PANDUAN':
                    $this->replyHelp($sender);
                    return response()->json(['status' => 'help delivered'], 200);

                case 'SALDO':
                    $this->replySaldo($user, $sender);
                    return response()->json(['status' => 'saldo delivered'], 200);

                case 'REKAP':
                    $this->replyRekap($user, $sender);
                    return response()->json(['status' => 'rekap delivered'], 200);

                case 'RIWAYAT':
                    $this->replyRiwayat($user, $sender);
                    return response()->json(['status' => 'riwayat delivered'], 200);
            }

            // 4. Evaluasi Format Transaksi
            if (preg_match('/^transfer\s+/i', $message)) {
                $this->processTransfer($user, $message, $sender);
                return response()->json(['status' => 'transfer processed'], 200);
            }

            if (str_contains($message, ':')) {
                $this->processStructuredTransaction($user, $message, $sender);
                return response()->json(['status' => 'structured transaction processed'], 200);
            }

            // Jika bukan format di atas, kirim error format tidak dikenali
            FonnteService::send("⚠️ *Perintah Tidak Dikenali*\nKetik *BANTUAN* untuk melihat format yang benar.", $sender);
            return response()->json(['status' => 'unknown command'], 200);

        } catch (Throwable $e) {
            Log::error("Fonnte Crash: " . $e->getMessage() . " at Line " . $e->getLine());
            if (!empty($sender)) {
                FonnteService::send("⚠️ *Gagal Memproses:* " . $e->getMessage(), $sender);
            }
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function findUserByPhone(string $sender): ?User
    {
        // Ambil 9 digit terakhir untuk menghindari error format 08/62/dst
        $lastDigits = substr($sender, -9);
        return User::where('whatsapp_number', 'LIKE', "%{$lastDigits}%")->first();
    }

    private function replyHelp(string $sender): void
    {
        $text = "🤖 *PANDUAN BOT PLMS FINANCE*\n\n"
              . "📋 *Command Cepat:*\n"
              . "• *SALDO* : Cek saldo dompet\n"
              . "• *REKAP* : Rekap bulan ini\n"
              . "• *RIWAYAT* : 5 transaksi terakhir\n"
              . "• *BANTUAN* : Menampilkan menu ini\n\n"
              . "📝 *Format Catat Transaksi:*\n"
              . "Jenis : Pengeluaran\n"
              . "Kategori : Makanan\n"
              . "Nominal : 25000\n"
              . "Dompet : Cash\n"
              . "Keterangan : Nasi Padang\n\n"
              . "🔄 *Format Transfer:*\n"
              . "Transfer 50000 BCA ke GoPay";

        FonnteService::send($text, $sender);
    }

    private function replySaldo(User $user, string $sender): void
    {
        $wallets = Wallet::where('user_id', $user->id)->where('is_active', true)->get();

        if ($wallets->isEmpty()) {
            FonnteService::send("⚠️ Anda belum memiliki dompet aktif.", $sender);
            return;
        }

        $total = $wallets->sum('balance');
        $lines = [];
        foreach ($wallets as $w) {
            $lines[] = "• *{$w->name}:* Rp " . number_format($w->balance, 0, ',', '.');
        }

        $msg = "💳 *INFORMASI SALDO*\n\n" . implode("\n", $lines) . "\n────────────────\n💰 *Total Aset:* Rp " . number_format($total, 0, ',', '.');
        FonnteService::send($msg, $sender);
    }

    private function replyRekap(User $user, string $sender): void
    {
        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->toDateString();

        $income = Transaction::where('user_id', $user->id)->where('type', 'income')->whereBetween('date', [$start, $end])->sum('amount');
        $expense = Transaction::where('user_id', $user->id)->where('type', 'expense')->whereBetween('date', [$start, $end])->sum('amount');
        $net = $income - $expense;

        $msg = "📊 *REKAP BULAN INI*\n\n🟢 *Masuk:* Rp " . number_format($income, 0, ',', '.') . "\n🔴 *Keluar:* Rp " . number_format($expense, 0, ',', '.') . "\n────────────────\n📈 *Sisa:* Rp " . number_format($net, 0, ',', '.');
        FonnteService::send($msg, $sender);
    }

    private function replyRiwayat(User $user, string $sender): void
    {
        $transactions = Transaction::with(['wallet', 'sourceWallet', 'destinationWallet'])->where('user_id', $user->id)->orderByDesc('date')->orderByDesc('id')->take(5)->get();
        if ($transactions->isEmpty()) {
            FonnteService::send("Belum ada riwayat transaksi.", $sender);
            return;
        }
        $lines = [];
        foreach ($transactions as $idx => $t) {
            $amt = 'Rp ' . number_format($t->amount, 0, ',', '.');
            if ($t->type === 'transfer') {
                $lines[] = ($idx + 1) . ". [🔄] {$amt} ({$t->sourceWallet->name} ➔ {$t->destinationWallet->name})";
            } else {
                $icon = $t->type === 'income' ? '🟢' : '🔴';
                $lines[] = ($idx + 1) . ". [{$icon}] {$t->description} - {$amt}";
            }
        }
        FonnteService::send("⏱ *5 TRANSAKSI TERAKHIR*\n\n" . implode("\n\n", $lines), $sender);
    }

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
            throw new Exception("Format salah. Pastikan baris *Jenis* dan *Nominal* sudah benar.");
        }

        $type = str_contains(strtolower($dataMap['jenis']), 'masuk') ? 'income' : 'expense';
        $amount = $this->parseAmount($dataMap['nominal']);

        $wallet = $this->findWalletOrDefault($user->id, $dataMap['dompet'] ?? null);
        $category = $this->findOrCreateCategory($user->id, $type, $dataMap['kategori'] ?? null);

        // 1. Simpan Transaksi
        Transaction::create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'date' => now()->toDateString(),
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'description' => $dataMap['keterangan'] ?? '-',
        ]);

        // 2. Update Saldo Dompet
        if ($type === 'income') {
            $wallet->increment('balance', $amount);
        } else {
            $wallet->decrement('balance', $amount);
        }

        $this->sendSuccess($type, $amount, $wallet, $category, $dataMap['keterangan'] ?? '-', $sender);
    }

    private function processTransfer(User $user, string $message, string $sender): void
    {
        if (!preg_match('/transfer\s+([0-9\.\,kK]+)\s+(.+?)\s+(?:ke|>)\s+(.+)/i', $message, $matches)) {
            throw new Exception("Format transfer salah.\nContoh: *Transfer 50000 BCA ke GoPay*");
        }

        $amount = $this->parseAmount($matches[1]);
        $sourceWallet = $this->findWalletOrDefault($user->id, trim($matches[2]));
        $destWallet = $this->findWalletOrDefault($user->id, trim($matches[3]));

        if ($sourceWallet->id === $destWallet->id) {
            throw new Exception("Dompet asal dan tujuan tidak boleh sama.");
        }

        // 1. Update Saldo (Kurangi asal, tambah tujuan)
        $sourceWallet->decrement('balance', $amount);
        $destWallet->increment('balance', $amount);

        // 2. Simpan Transaksi
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'transfer',
            'amount' => $amount,
            'date' => now()->toDateString(),
            'description' => "Transfer antar dompet",
            'source_wallet_id' => $sourceWallet->id,
            'destination_wallet_id' => $destWallet->id,
        ]);

        $msg = "🔄 *Transfer Berhasil!*\n\n💰 *Nominal:* Rp " . number_format($amount, 0, ',', '.') . "\n📤 *Dari:* {$sourceWallet->name} (Sisa: Rp " . number_format($sourceWallet->balance, 0, ',', '.') . ")\n📥 *Ke:* {$destWallet->name} (Total: Rp " . number_format($destWallet->balance, 0, ',', '.') . ")";
        FonnteService::send($msg, $sender);
    }

    private function sendSuccess(string $type, float $amount, Wallet $wallet, Category $category, string $desc, string $sender): void
    {
        $icon = $type === 'income' ? '🟢' : '🔴';
        $typeLabel = $type === 'income' ? 'Pemasukan' : 'Pengeluaran';

        $msg = "✅ *Transaksi Dicatat!*\n\n📌 *Jenis:* {$icon} {$typeLabel}\n📂 *Kategori:* {$category->name}\n💰 *Nominal:* Rp " . number_format($amount, 0, ',', '.') . "\n💳 *Dompet:* {$wallet->name} (Sisa: Rp " . number_format($wallet->balance, 0, ',', '.') . ")\n📝 *Catatan:* {$desc}";
        FonnteService::send($msg, $sender);
    }

    private function parseAmount(string $val): float
    {
        $val = strtolower(trim($val));
        $multiplier = str_ends_with($val, 'k') ? 1000 : 1;
        $num = (float) preg_replace('/[^0-9]/', '', $val) * $multiplier;

        if ($num <= 0) throw new Exception("Nominal tidak valid.");
        return $num;
    }

    private function findWalletOrDefault(int $userId, ?string $query): Wallet
    {
        if (!empty($query)) {
            $wallet = Wallet::where('user_id', $userId)->where('name', 'ILIKE', "%{$query}%")->first();
            if ($wallet) return $wallet;
        }
        $wallet = Wallet::where('user_id', $userId)->where('is_default', true)->first() ?? Wallet::where('user_id', $userId)->first();
        if (!$wallet) throw new Exception("Dompet tidak ditemukan.");
        return $wallet;
    }

    private function findOrCreateCategory(int $userId, string $type, ?string $name): Category
    {
        $catName = !empty($name) ? trim($name) : 'Umum';
        return Category::firstOrCreate(
            ['user_id' => $userId, 'type' => $type, 'name' => ucfirst($catName)],
            ['icon' => 'ti ti-tag', 'color' => '#64748b']
        );
    }
}
