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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteWebhookController extends Controller
{
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return response()->json(['status' => 'success', 'message' => 'Webhook Active!'], 200);
        }

        try {
            $rawSender = $request->input('sender', '');
            $sender = preg_replace('/[^0-9]/', '', $rawSender);

            if (str_starts_with($sender, '62')) {
                $sender = '0' . substr($sender, 2);
            }

            $message = trim($request->input('message') ?? $request->input('text') ?? '');

            Log::info("Fonnte INBOUND [{$sender}]: {$message}");

            if (empty($message) || empty($sender)) {
                return response()->json(['status' => 'ignored_empty'], 200);
            }

            // 1. Identifikasi User
            $user = $this->findUserByPhone($sender);

            if (!$user) {
                FonnteService::send("⚠️ Nomor WhatsApp Anda ({$sender}) belum terdaftar di akun PLMS Finance.", $sender);
                return response()->json(['status' => 'ignored_unregistered'], 200);
            }

            $cleanCommand = strtoupper(trim($message));
            $validCommands = ['BANTUAN', 'MENU', 'PANDUAN', 'SALDO', 'REKAP', 'RIWAYAT'];

            // 2. Evaluasi Command Cepat
            if (in_array($cleanCommand, $validCommands)) {
                match ($cleanCommand) {
                    'BANTUAN', 'MENU', 'PANDUAN' => $this->replyHelp($sender),
                    'SALDO'   => $this->replySaldo($user, $sender),
                    'REKAP'   => $this->replyRekap($user, $sender),
                    'RIWAYAT' => $this->replyRiwayat($user, $sender),
                };
                return response()->json(['status' => 'command processed'], 200);
            }

            // 3. Evaluasi Format Transfer
            if (preg_match('/^transfer\s+/i', $message)) {
                $this->processTransfer($user, $message, $sender);
                return response()->json(['status' => 'transfer processed'], 200);
            }

            // 4. Evaluasi Format Transaksi Terstruktur
            if (preg_match('/jenis\s*:/i', $message) && preg_match('/nominal\s*:/i', $message)) {
                $this->processStructuredTransaction($user, $message, $sender);
                return response()->json(['status' => 'transaction processed'], 200);
            }

            return response()->json(['status' => 'ignored_normal_chat'], 200);

        } catch (Throwable $e) {
            Log::error("Fonnte Error: " . $e->getMessage() . " at line " . $e->getLine());
            if (!empty($sender)) {
                FonnteService::send("⚠️ Gagal memproses: " . $e->getMessage(), $sender);
            }
            return response()->json(['status' => 'error_handled_gracefully'], 200);
        }
    }

    private function findUserByPhone(string $sender): ?User
    {
        $lastDigits = substr($sender, -9);
        return User::where('whatsapp_number', 'LIKE', "%{$lastDigits}%")->first();
    }

    private function replyHelp(string $sender): void
    {
        $text = "🤖 *PANDUAN BOT PLMS FINANCE*\n\n"
              . "📋 *Perintah Cepat:*\n"
              . "• *SALDO* : Cek saldo dompet\n"
              . "• *REKAP* : Rekap arus kas bulan ini\n"
              . "• *RIWAYAT* : 5 transaksi terakhir\n"
              . "• *BANTUAN* : Panduan format pesan\n\n"
              . "📝 *Catat Transaksi:*\n"
              . "Jenis : Pengeluaran\n"
              . "Kategori : Makanan\n"
              . "Nominal : 25000\n"
              . "Dompet : Cash\n"
              . "Keterangan : Nasi Padang\n\n"
              . "🔄 *Transfer Saldo:*\n"
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
        $lines = $wallets->map(fn($w) => "• *{$w->name}:* Rp " . number_format($w->balance, 0, ',', '.'))->toArray();

        $msg = "💳 *INFORMASI SALDO*\n\n" . implode("\n", $lines) . "\n────────────────\n💰 *Total Aset:* Rp " . number_format($total, 0, ',', '.');
        FonnteService::send($msg, $sender);
    }

    private function replyRekap(User $user, string $sender): void
    {
        $start = Carbon::now()->startOfMonth()->toDateString();
        $end   = Carbon::now()->endOfMonth()->toDateString();

        // 1 Query agregasi untuk income dan expense
        $recap = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw('type, sum(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $income  = (float) ($recap['income'] ?? 0);
        $expense = (float) ($recap['expense'] ?? 0);
        $net     = $income - $expense;

        $msg = "📊 *REKAP BULAN INI*\n\n"
             . "🟢 *Masuk:* Rp " . number_format($income, 0, ',', '.') . "\n"
             . "🔴 *Keluar:* Rp " . number_format($expense, 0, ',', '.') . "\n"
             . "────────────────\n"
             . "📈 *Sisa:* Rp " . number_format($net, 0, ',', '.');

        FonnteService::send($msg, $sender);
    }

    private function replyRiwayat(User $user, string $sender): void
    {
        $transactions = Transaction::with(['wallet', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

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
            throw new Exception("Format salah. Pastikan baris *Jenis* dan *Nominal* terisi.");
        }

        $type = str_contains(strtolower($dataMap['jenis']), 'masuk') ? 'income' : 'expense';
        $amount = $this->parseAmount($dataMap['nominal']);

        $wallet = $this->findWalletOrDefault($user->id, $dataMap['dompet'] ?? null);
        $category = $this->findOrCreateCategory($user->id, $type, $dataMap['kategori'] ?? null);
        $description = $dataMap['keterangan'] ?? '-';

        DB::transaction(function () use ($user, $type, $amount, $wallet, $category, $description) {
            Transaction::create([
                'user_id'     => $user->id,
                'type'        => $type,
                'amount'      => $amount,
                'date'        => now()->toDateString(),
                'wallet_id'   => $wallet->id,
                'category_id' => $category->id,
                'description' => $description,
            ]);

            if ($type === 'income') {
                $wallet->increment('balance', $amount);
            } else {
                $wallet->decrement('balance', $amount);
            }
        });

        $wallet->refresh();
        $this->sendSuccess($type, $amount, $wallet, $category, $description, $sender);
    }

    private function processTransfer(User $user, string $message, string $sender): void
    {
        if (!preg_match('/transfer\s+([0-9\.\,kK]+)\s+(.+?)\s+(?:ke|>)\s+(.+)/i', $message, $matches)) {
            throw new Exception("Format transfer salah.\nContoh: *Transfer 50000 BCA ke GoPay*");
        }

        $amount = $this->parseAmount($matches[1]);
        $sourceWallet = $this->findWalletOrDefault($user->id, trim($matches[2]));
        $destWallet   = $this->findWalletOrDefault($user->id, trim($matches[3]));

        if ($sourceWallet->id === $destWallet->id) {
            throw new Exception("Dompet asal dan dompet tujuan tidak boleh sama.");
        }

        DB::transaction(function () use ($user, $amount, $sourceWallet, $destWallet) {
            $sourceWallet->decrement('balance', $amount);
            $destWallet->increment('balance', $amount);

            Transaction::create([
                'user_id'               => $user->id,
                'type'                  => 'transfer',
                'amount'                => $amount,
                'date'                  => now()->toDateString(),
                'description'           => "Transfer antar dompet",
                'source_wallet_id'      => $sourceWallet->id,
                'destination_wallet_id' => $destWallet->id,
            ]);
        });

        $sourceWallet->refresh();
        $destWallet->refresh();

        $msg = "🔄 *Transfer Berhasil!*\n\n"
             . "💰 *Nominal:* Rp " . number_format($amount, 0, ',', '.') . "\n"
             . "📤 *Dari:* {$sourceWallet->name} (Sisa: Rp " . number_format($sourceWallet->balance, 0, ',', '.') . ")\n"
             . "📥 *Ke:* {$destWallet->name} (Total: Rp " . number_format($destWallet->balance, 0, ',', '.') . ")";

        FonnteService::send($msg, $sender);
    }

    private function sendSuccess(string $type, float $amount, Wallet $wallet, Category $category, string $desc, string $sender): void
    {
        $icon = $type === 'income' ? '🟢' : '🔴';
        $typeLabel = $type === 'income' ? 'Pemasukan' : 'Pengeluaran';

        $msg = "✅ *Transaksi Dicatat!*\n\n"
             . "📌 *Jenis:* {$icon} {$typeLabel}\n"
             . "📂 *Kategori:* {$category->name}\n"
             . "💰 *Nominal:* Rp " . number_format($amount, 0, ',', '.') . "\n"
             . "💳 *Dompet:* {$wallet->name} (Sisa: Rp " . number_format($wallet->balance, 0, ',', '.') . ")\n"
             . "📝 *Catatan:* {$desc}";

        FonnteService::send($msg, $sender);
    }

    private function parseAmount(string $val): float
    {
        $val = strtolower(trim($val));
        $multiplier = str_ends_with($val, 'k') ? 1000 : 1;
        $num = (float) preg_replace('/[^0-9]/', '', $val) * $multiplier;

        if ($num <= 0) {
            throw new Exception("Nominal transaksi tidak valid.");
        }

        return $num;
    }

    private function findWalletOrDefault(int $userId, ?string $query): Wallet
    {
        if (!empty($query)) {
            $cleaned = strtolower(trim($query));
            $wallet = Wallet::where('user_id', $userId)
                ->whereRaw('LOWER(name) LIKE ?', ["%{$cleaned}%"])
                ->first();

            if ($wallet) {
                return $wallet;
            }
        }

        $wallet = Wallet::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->first();

        if (!$wallet) {
            throw new Exception("Dompet tidak ditemukan.");
        }

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
