<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user pertama sebagai pemilik dompet
        $user = User::first();

        if (!$user) {
            $this->command->error('Tidak ada user di database. Silakan register user di browser terlebih dahulu.');
            return;
        }

        // Hapus dompet lama milik user ini agar bersih (Force delete karena kita pakai softDeletes)
        Wallet::where('user_id', $user->id)->forceDelete();

        $wallets = [
            [
                'name' => 'Dompet',
                'type' => 'Cash',
                'balance' => 100000,
                'color' => '#ef4444', // Merah
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'KrediVo',
                'type' => 'Other',
                'balance' => 100000,
                'color' => '#38bdf8', // Biru Muda (Berdasarkan gambar: Nonaktif)
                'is_active' => false,
                'is_default' => false,
            ],
            [
                'name' => 'Ovo',
                'type' => 'E-Wallet',
                'balance' => 100000,
                'color' => '#a855f7', // Ungu
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'ShopeePay',
                'type' => 'E-Wallet',
                'balance' => 100000,
                'color' => '#f97316', // Oranye
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'ABC',
                'type' => 'Bank',
                'balance' => 100000,
                'color' => '#3b82f6', // Biru
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'GoPay',
                'type' => 'E-Wallet',
                'balance' => 100000,
                'color' => '#06b6d4', // Cyan
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($wallets as $wallet) {
            Wallet::create(array_merge($wallet, ['user_id' => $user->id]));
        }

        $this->command->info('✅ 6 Dompet (Saldo Rp 100.000) berhasil di-seed untuk user: ' . $user->name);
    }
}
