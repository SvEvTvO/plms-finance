<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Services\FinanceService;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FonnteWebhookController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function handle(Request $request)
    {
        // Fonnte mengirimkan 'sender' dan 'message'
        $sender = $request->input('sender');
        $message = trim($request->input('message', ''));

        Log::info("Fonnte Incoming [{$sender}]: {$message}");

        if (empty($message)) {
            return response()->json(['status' => 'empty message'], 200);
        }

        // 1. Ambil user (default ke user pertama jika single-user)
        $user = User::first();
        if (!$user) {
            return response()->json(['status' => 'no user'], 200);
        }

        // 2. Parse format teks (Mendukung format baris maupun format singkat)
        $parsed = $this->parseTransactionText($message, $user->id);

        if (!$parsed['success']) {
            FonnteService::send($parsed['error_message'], $sender);
            return response()->json(['status' => 'format invalid'], 200);
        }

        try {
            // 3. Simpan transaksi via FinanceService
            auth()->login($user); // Set sesi user untuk service
            $this->financeService->createTransaction($parsed['data']);

            // 4. Kirim balasan struk ke WhatsApp
            $nominal = 'Rp ' . number_format($parsed['data']['amount'], 0, ',', '.');
            $typeLabel = $parsed['data']['type'] === 'income' ? '🟢 Pemasukan' : '🔴 Pengeluaran';
            $wallet = Wallet::find($parsed['data']['wallet_id']);

            $reply = "✅ *Transaksi Berhasil Dicatat!*\n\n"
                   . "📌 *Jenis:* {$typeLabel}\n"
                   . "📂 *Kategori:* {$parsed['category_name']}\n"
                   . "💰 *Nominal:* {$nominal}\n"
                   . "💳 *Dompet:* {$wallet->name} (Sisa: Rp " . number_format($wallet->fresh()->balance, 0, ',', '.') . ")\n"
                   . "📝 *Keterangan:* " . ($parsed['data']['description'] ?? '-') . "\n\n"
                   . "_PLMS Finance Management_";

            FonnteService::send($reply, $sender);

        } catch (\Exception $e) {
            Log::error('Gagal mencatat transaksi WA: ' . $e->getMessage());
            FonnteService::send("⚠️ Gagal mencatat: " . $e->getMessage(), $sender);
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function parseTransactionText(string $text, int $userId): array
    {
        $lines = explode("\n", $text);
        $dataMap = [];

        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $val] = explode(':', $line, 2);
                $dataMap[strtolower(trim($key))] = trim($val);
            }
        }

        // Validasi ketersediaan field kunci
        if (!isset($dataMap['jenis']) || !isset($dataMap['nominal'])) {
            return [
                'success' => false,
                'error_message' => "⚠️ Format pesan belum sesuai. Contoh format:\n\n"
                    . "Jenis : Pemasukan\n"
                    . "Kategori : Uang Saku\n"
                    . "Nominal : 15000\n"
                    . "Dompet : GoPay\n"
                    . "Keterangan : Uang saku minggu ini"
            ];
        }

        // 1. Tipe Transaksi
        $typeInput = strtolower($dataMap['jenis']);
        $type = str_contains($typeInput, 'masuk') ? 'income' : 'expense';

        // 2. Nominal
        $amount = (float) preg_replace('/[^0-9]/', '', $dataMap['nominal']);
        if ($amount <= 0) {
            return ['success' => false, 'error_message' => '⚠️ Nominal angka tidak valid.'];
        }

        // 3. Dompet
        $walletQuery = $dataMap['dompet'] ?? '';
        $wallet = Wallet::where('user_id', $userId)
            ->where('name', 'ILIKE', "%{$walletQuery}%")
            ->first() ?? Wallet::where('user_id', $userId)->where('is_default', true)->first()
            ?? Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return ['success' => false, 'error_message' => '⚠️ Dompet tidak ditemukan di sistem.'];
        }

        // 4. Kategori
        $categoryName = $dataMap['kategori'] ?? ($type === 'income' ? 'Pemasukan Lainnya' : 'Pengeluaran Umum');
        $category = Category::where('user_id', $userId)
            ->where('type', $type)
            ->where('name', 'ILIKE', "%{$categoryName}%")
            ->first();

        if (!$category) {
            $category = Category::create([
                'user_id' => $userId,
                'name' => $categoryName,
                'type' => $type,
                'icon' => $type === 'income' ? 'ti ti-arrow-down-left' : 'ti ti-arrow-up-right',
                'color' => $type === 'income' ? '#10b981' : '#ef4444'
            ]);
        }

        return [
            'success' => true,
            'category_name' => $category->name,
            'data' => [
                'user_id' => $userId,
                'type' => $type,
                'amount' => $amount,
                'date' => now()->toDateString(),
                'wallet_id' => $wallet->id,
                'category_id' => $category->id,
                'description' => $dataMap['keterangan'] ?? $dataMap['catatan'] ?? '-',
            ]
        ];
    }
}
