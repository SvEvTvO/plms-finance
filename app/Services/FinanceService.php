<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Exception;

class FinanceService
{
    /**
     * Membuat transaksi baru (Income, Expense, Transfer) secara atomic.
     */
    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $type = $data['type'];
            $amount = $data['amount'];

            // Validasi kepemilikan Wallet & Category
            $this->validateOwnership($data);

            if ($type === 'income') {
                $wallet = Wallet::findOrFail($data['wallet_id']);
                // Tambah saldo wallet
                $wallet->balance += $amount;
                $wallet->save();

            } elseif ($type === 'expense') {
                $wallet = Wallet::findOrFail($data['wallet_id']);

                // Aturan Bisnis: Expense tidak boleh membuat saldo negatif (Kecuali diizinkan)
                if ($wallet->balance < $amount) {
                    throw new Exception("Saldo dompet '{$wallet->name}' tidak mencukupi untuk pengeluaran ini.");
                }

                // Kurangi saldo wallet
                $wallet->balance -= $amount;
                $wallet->save();

            } elseif ($type === 'transfer') {
                $sourceWallet = Wallet::findOrFail($data['source_wallet_id']);
                $destWallet = Wallet::findOrFail($data['destination_wallet_id']);

                if ($sourceWallet->id === $destWallet->id) {
                    throw new Exception("Dompet sumber dan tujuan transfer tidak boleh sama.");
                }

                if ($sourceWallet->balance < $amount) {
                    throw new Exception("Saldo dompet sumber '{$sourceWallet->name}' tidak mencukupi untuk transfer.");
                }

                // Kurangi dompet sumber, tambah dompet tujuan
                $sourceWallet->balance -= $amount;
                $sourceWallet->save();

                $destWallet->balance += $amount;
                $destWallet->save();
            }

            // Simpan catatan transaksi
            return Transaction::create([
                'user_id' => auth()->id(),
                'type' => $type,
                'amount' => $amount,
                'description' => $data['description'] ?? null,
                'date' => $data['date'],
                'wallet_id' => $data['wallet_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'source_wallet_id' => $data['source_wallet_id'] ?? null,
                'destination_wallet_id' => $data['destination_wallet_id'] ?? null,
            ]);
        });
    }

    /**
     * Mengubah transaksi yang sudah ada dengan melakukan reverse efek lama lalu menerapkan efek baru.
     */
    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // 1. Revert efek finansial transaksi lama
            $this->revertTransactionEffect($transaction);

            // 2. Validasi ownership data baru
            $this->validateOwnership($data);

            $type = $data['type'];
            $amount = $data['amount'];

            // 3. Terapkan efek finansial transaksi baru
            if ($type === 'income') {
                $wallet = Wallet::findOrFail($data['wallet_id']);
                $wallet->balance += $amount;
                $wallet->save();
            } elseif ($type === 'expense') {
                $wallet = Wallet::findOrFail($data['wallet_id']);
                if ($wallet->balance < $amount) {
                    throw new Exception("Saldo dompet '{$wallet->name}' tidak mencukupi.");
                }
                $wallet->balance -= $amount;
                $wallet->save();
            } elseif ($type === 'transfer') {
                $sourceWallet = Wallet::findOrFail($data['source_wallet_id']);
                $destWallet = Wallet::findOrFail($data['destination_wallet_id']);

                if ($sourceWallet->id === $destWallet->id) {
                    throw new Exception("Dompet sumber dan tujuan transfer tidak boleh sama.");
                }
                if ($sourceWallet->balance < $amount) {
                    throw new Exception("Saldo dompet sumber '{$sourceWallet->name}' tidak mencukupi.");
                }

                $sourceWallet->balance -= $amount;
                $sourceWallet->save();
                $destWallet->balance += $amount;
                $destWallet->save();
            }

            // 4. Update data transaksi
            $transaction->update([
                'type' => $type,
                'amount' => $amount,
                'description' => $data['description'] ?? null,
                'date' => $data['date'],
                'wallet_id' => $data['wallet_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'source_wallet_id' => $data['source_wallet_id'] ?? null,
                'destination_wallet_id' => $data['destination_wallet_id'] ?? null,
            ]);

            return $transaction;
        });
    }

    /**
     * Menghapus transaksi dan mengembalikan saldo dompet seperti semula.
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $this->revertTransactionEffect($transaction);
            return $transaction->delete();
        });
    }

    /**
     * Membatalkan efek finansial dari sebuah transaksi (Helper internal).
     */
    private function revertTransactionEffect(Transaction $transaction): void
    {
        if ($transaction->type === 'income') {
            $wallet = Wallet::find($transaction->wallet_id);
            if ($wallet) {
                $wallet->balance -= $transaction->amount;
                $wallet->save();
            }
        } elseif ($transaction->type === 'expense') {
            $wallet = Wallet::find($transaction->wallet_id);
            if ($wallet) {
                $wallet->balance += $transaction->amount;
                $wallet->save();
            }
        } elseif ($transaction->type === 'transfer') {
            $sourceWallet = Wallet::find($transaction->source_wallet_id);
            if ($sourceWallet) {
                $sourceWallet->balance += $transaction->amount;
                $sourceWallet->save();
            }

            $destWallet = Wallet::find($transaction->destination_wallet_id);
            if ($destWallet) {
                $destWallet->balance -= $transaction->amount;
                $destWallet->save();
            }
        }
    }

    /**
     * Validasi kepemilikan resource agar aman dari eksploitasi lintas user.
     */
    private function validateOwnership(array $data): void
    {
        $userId = auth()->id();

        if (isset($data['wallet_id'])) {
            $walletExists = Wallet::where('id', $data['wallet_id'])->where('user_id', $userId)->exists();
            if (!$walletExists) throw new Exception("Wallet tidak valid atau bukan milik Anda.");
        }

        if (isset($data['source_wallet_id'])) {
            $sourceExists = Wallet::where('id', $data['source_wallet_id'])->where('user_id', $userId)->exists();
            if (!$sourceExists) throw new Exception("Wallet sumber transfer tidak valid.");
        }

        if (isset($data['destination_wallet_id'])) {
            $destExists = Wallet::where('id', $data['destination_wallet_id'])->where('user_id', $userId)->exists();
            if (!$destExists) throw new Exception("Wallet tujuan transfer tidak valid.");
        }
    }

/**
     * Menyimpan uang ke Target Keuangan (Goal).
     * Mengurangi saldo dompet dan menambah current_amount pada Goal.
     */
    public function createSaving(array $data): \App\Models\Saving
    {
        return DB::transaction(function () use ($data) {
            $wallet = Wallet::findOrFail($data['wallet_id']);
            $goal = \App\Models\Goal::findOrFail($data['goal_id']);
            $amount = $data['amount'];

            // 1. Validasi Kepemilikan
            if ($wallet->user_id !== auth()->id() || $goal->user_id !== auth()->id()) {
                throw new Exception("Akses ditolak. Resource bukan milik Anda.");
            }

            // 2. Validasi Saldo Dompet
            if ($wallet->balance < $amount) {
                throw new Exception("Saldo dompet '{$wallet->name}' tidak mencukupi untuk menabung sebesar Rp " . number_format($amount, 0, ',', '.'));
            }

            // 3. Kurangi Saldo Dompet
            $wallet->balance -= $amount;
            $wallet->save();

            // 4. Tambah Saldo Goal
            $goal->current_amount += $amount;
            
            // 5. Cek apakah goal sudah tercapai
            if ($goal->current_amount >= $goal->target_amount) {
                $goal->is_completed = true;
            }
            $goal->save();

            // 6. Buat Record Saving
            return \App\Models\Saving::create([
                'user_id' => auth()->id(),
                'goal_id' => $goal->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
            ]);
        });
    }

    /**
     * Menghapus riwayat tabungan (Menarik uang kembali ke dompet).
     */
    public function deleteSaving(\App\Models\Saving $saving): bool
    {
        return DB::transaction(function () use ($saving) {
            $wallet = Wallet::find($saving->wallet_id);
            $goal = \App\Models\Goal::find($saving->goal_id);

            // 1. Kembalikan uang ke dompet (jika dompet masih ada)
            if ($wallet) {
                $wallet->balance += $saving->amount;
                $wallet->save();
            }

            // 2. Kurangi current_amount pada Goal (jika goal masih ada)
            if ($goal) {
                $goal->current_amount -= $saving->amount;
                
                // Jika setelah dikurangi ternyata di bawah target, batalkan status completed
                if ($goal->current_amount < $goal->target_amount) {
                    $goal->is_completed = false;
                }
                $goal->save();
            }

            // 3. Hapus Record
            return $saving->delete();
        });
    }
}
