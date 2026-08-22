namespace App\Services;

use App\Models\{User, Transaction, Category, Wallet, Goal, Saving};
use Illuminate\Support\Str;

class WABotService
{
    public function processMessage(array $payload): void
    {
        $entry = $payload['entry'][0] ?? null;
        $message = $entry['changes'][0]['value']['messages'][0] ?? null;
        if (!$message) return;

        $from = $message['from']; // nomor WA pengirim
        $text = Str::lower(trim($message['text']['body'] ?? ''));

        $user = User::where('whatsapp_number', $from)->first();
        if (!$user) {
            $this->reply($from, "❌ Nomor Anda tidak terdaftar. Silakan daftar di aplikasi terlebih dahulu.");
            return;
        }

        $this->handleText($user, $text, $from);
    }

    protected function handleText(User $user, string $text, string $to): void
    {
        // Format: [cmd] [nominal] [keterangan]
        // Contoh: pengeluaran 50000 makan siang
        //         pemasukan 3000000 gaji

        if (preg_match('/^(pengeluaran|keluar)\s+(\d+)\s+(.+)/', $text, $m)) {
            $this->createTransaction($user, 'expense', (int) $m[2], $m[3], $to);
        } elseif (preg_match('/^(pemasukan|masuk)\s+(\d+)\s+(.+)/', $text, $m)) {
            $this->createTransaction($user, 'income', (int) $m[2], $m[3], $to);
        } elseif ($text === 'saldo') {
            $balance = Wallet::where('user_id', $user->id)->sum('balance');
            $this->reply($to, "💰 Saldo Anda: Rp " . number_format($balance, 0, ',', '.'));
        } elseif ($text === 'help' || $text === 'bantuan') {
            $this->reply($to, "📋 *Perintah yang tersedia:*\n"
                . "• *pemasukan [nominal] [keterangan]* → Catat pemasukan\n"
                . "• *pengeluaran [nominal] [keterangan]* → Catat pengeluaran\n"
                . "• *saldo* → Cek saldo\n"
                . "• *goal [nama] [target]* → Buat goal baru\n"
                . "• *help* → Tampilkan bantuan ini");
        } else {
            $this->reply($to, "❓ Perintah tidak dikenal. Ketik *help* untuk daftar perintah.");
        }
    }

    protected function createTransaction(User $user, string $type, int $amount, string $desc, string $to): void
    {
        $wallet = Wallet::where('user_id', $user->id)->where('is_default', true)->first()
                  ?? Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            $this->reply($to, "❌ Tidak ada wallet. Silakan buat wallet terlebih dahulu di aplikasi.");
            return;
        }

        $category = Category::firstOrCreate(['user_id' => $user->id, 'name' => 'WhatsApp', 'type' => $type]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => $type,
            'amount' => $amount,
            'description' => $desc,
            'date' => now(),
        ]);

        // Update wallet balance
        if ($type === 'income') $wallet->increment('balance', $amount);
        else $wallet->decrement('balance', $amount);

        $emoji = $type === 'income' ? '🟢' : '🔴';
        $this->reply($to, "{$emoji} Berhasil mencatat _{$type}_ sebesar *Rp" . number_format($amount, 0, ',', '.') . "* untuk \"{$desc}\"");
    }

    protected function reply(string $to, string $message): void
    {
        // Gunakan WhatsApp Cloud API untuk mengirim pesan
        // Implementasi bisa berbeda tergantung package yang digunakan
        \Illuminate\Support\Facades\Http::withToken(config('services.whatsapp.access_token'))
            ->post("https://graph.facebook.com/v21.0/" . config('services.whatsapp.phone_number_id') . "/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);
    }
}
