<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ChartDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat akun Admin khusus
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => Carbon::now(),
            ]
        );

        // 2. Buat Dompet Demo (Menambahkan kolom 'type' => 'bank')
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Rekening Demo'],
            [
                'type' => 'bank',
                'balance' => 15000000,
                'color' => '#8b5cf6',
                'is_active' => true,
            ]
        );

        // 3. Buat Kategori Demo
        $catIncome = Category::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Pendapatan Demo', 'type' => 'income'],
            ['icon' => 'ti-cash']
        );

        $catExpense = Category::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Pengeluaran Demo', 'type' => 'expense'],
            ['icon' => 'ti-shopping-cart']
        );

        // 4. Hapus data demo transaksi lama jika ada
        Transaction::where('wallet_id', $wallet->id)->delete();

        // 5. Generate Transaksi Sepanjang Bulan Ini
        $daysInMonth = Carbon::now()->daysInMonth;
        $startOfMonth = Carbon::now()->startOfMonth();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = $startOfMonth->copy()->addDays($i - 1)->format('Y-m-d');
            
            // Pengeluaran Harian
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'category_id' => $catExpense->id,
                'type' => 'expense',
                'amount' => rand(20, 250) * 1000,
                'date' => $currentDate,
                'description' => 'Jajan & Makan tgl ' . $i,
            ]);

            // Pemasukan Berkala
            if (in_array($i, [1, 8, 15, 22, 28])) {
                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'category_id' => $catIncome->id,
                    'type' => 'income',
                    'amount' => rand(1500, 3500) * 1000,
                    'date' => $currentDate,
                    'description' => 'Pemasukan Project tgl ' . $i,
                ]);
            }
        }

        $this->command->info('Data Demo untuk Grafik berhasil disuntikkan!');
        $this->command->info('Akun   : admin@gmail.com');
        $this->command->info('Sandi  : admin123');
    }
}
