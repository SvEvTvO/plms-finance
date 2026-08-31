<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Saving;
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
            $userId = $data['user_id'] ?? auth()->id();
            $type   = $data['type'];
            $amount = (float) $data['amount'];

            if ($type === 'income') {
                $wallet = $this->getUserWallet($data['wallet_id'], $userId);
                $wallet->increment('balance', $amount);

            } elseif ($type === 'expense') {
                $wallet = $this->getUserWallet($data['wallet_id'], $userId);

                if ($wallet->balance < $amount) {
                    throw new Exception("Saldo dompet '{$wallet->name}' tidak mencukupi untuk pengeluaran ini.");
                }

                $wallet->decrement('balance', $amount);

            } elseif ($type === 'transfer') {
                $sourceWallet = $this->getUserWallet($data['source_wallet_id'], $userId);
                $destWallet   = $this->getUserWallet($data['destination_wallet_id'], $userId);

                if ($sourceWallet->id === $destWallet->id) {
                    throw new Exception("Dompet sumber dan tujuan transfer tidak boleh sama.");
                }

                if ($sourceWallet->balance < $amount) {
                    throw new Exception("Saldo dompet sumber '{$sourceWallet->name}' tidak mencukupi untuk transfer.");
                }

                $sourceWallet->decrement('balance', $amount);
                $destWallet->increment('balance', $amount);
            }

            return Transaction::create([
                'user_id'               => $userId,
                'type'                  => $type,
                'amount'                => $amount,
                'description'           => $data['description'] ?? null,
                'date'                  => $data['date'],
                'wallet_id'             => $data['wallet_id'] ?? null,
                'category_id'           => $data['category_id'] ?? null,
                'source_wallet_id'      => $data['source_wallet_id'] ?? null,
                'destination_wallet_id' => $data['destination_wallet_id'] ?? null,
            ]);
        });
    }

    /**
     * Mengubah transaksi dengan me-revert efek saldo lama lalu menerapkan data baru.
     */
    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $userId = $transaction->user_id;

            // 1. Revert efek finansial transaksi lama
            $this->revertTransactionEffect($transaction, $userId);

            $type   = $data['type'];
            $amount = (float) $data['amount'];

            // 2. Terapkan efek transaksi baru
            if ($type === 'income') {
                $wallet = $this->getUserWallet($data['wallet_id'], $userId);
                $wallet->increment('balance', $amount);

            } elseif ($type === 'expense') {
                $wallet = $this->getUserWallet($data['wallet_id'], $userId);

                if ($wallet->balance < $amount) {
                    throw new Exception("Saldo dompet '{$wallet->name}' tidak mencukupi.");
                }

                $wallet->decrement('balance', $amount);

            } elseif ($type === 'transfer') {
                $sourceWallet = $this->getUserWallet($data['source_wallet_id'], $userId);
                $destWallet   = $this->getUserWallet($data['destination_wallet_id'], $userId);

                if ($sourceWallet->id === $destWallet->id) {
                    throw new Exception("Dompet sumber dan tujuan transfer tidak boleh sama.");
                }

                if ($sourceWallet->balance < $amount) {
                    throw new Exception("Saldo dompet sumber '{$sourceWallet->name}' tidak mencukupi.");
                }

                $sourceWallet->decrement('balance', $amount);
                $destWallet->increment('balance', $amount);
            }

            // 3. Update catatan transaksi
            $transaction->update([
                'type'                  => $type,
                'amount'                => $amount,
                'description'           => $data['description'] ?? null,
                'date'                  => $data['date'],
                'wallet_id'             => $data['wallet_id'] ?? null,
                'category_id'           => $data['category_id'] ?? null,
                'source_wallet_id'      => $data['source_wallet_id'] ?? null,
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
            $this->revertTransactionEffect($transaction, $transaction->user_id);
            return (bool) $transaction->delete();
        });
    }

    /**
     * Menyimpan uang ke Target Keuangan (Goal).
     */
    public function createSaving(array $data): Saving
    {
        return DB::transaction(function () use ($data) {
            $userId = $data['user_id'] ?? auth()->id();
            $wallet = $this->getUserWallet($data['wallet_id'], $userId);

            $goal = Goal::where('id', $data['goal_id'])
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if (!$goal) {
                throw new Exception("Target keuangan tidak ditemukan atau bukan milik Anda.");
            }

            $amount = (float) $data['amount'];

            if ($wallet->balance < $amount) {
                throw new Exception("Saldo dompet '{$wallet->name}' tidak mencukupi untuk menabung sebesar Rp " . number_format($amount, 0, ',', '.'));
            }

            $wallet->decrement('balance', $amount);

            $goal->current_amount += $amount;
            if ($goal->current_amount >= $goal->target_amount) {
                $goal->is_completed = true;
            }
            $goal->save();

            return Saving::create([
                'user_id'     => $userId,
                'goal_id'     => $goal->id,
                'wallet_id'   => $wallet->id,
                'amount'      => $amount,
                'date'        => $data['date'],
                'description' => $data['description'] ?? null,
            ]);
        });
    }

    /**
     * Menghapus riwayat tabungan (Menarik uang kembali ke dompet).
     */
    public function deleteSaving(Saving $saving): bool
    {
        return DB::transaction(function () use ($saving) {
            $userId = $saving->user_id;

            $wallet = Wallet::where('id', $saving->wallet_id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            $goal = Goal::where('id', $saving->goal_id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->increment('balance', $saving->amount);
            }

            if ($goal) {
                $goal->current_amount -= $saving->amount;
                if ($goal->current_amount < $goal->target_amount) {
                    $goal->is_completed = false;
                }
                $goal->save();
            }

            return (bool) $saving->delete();
        });
    }

    /**
     * Helper internal untuk mengambil dan mengunci dompet milik user.
     */
    private function getUserWallet(int $walletId, int $userId): Wallet
    {
        $wallet = Wallet::where('id', $walletId)
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();

        if (!$wallet) {
            throw new Exception("Dompet tidak ditemukan atau bukan milik Anda.");
        }

        return $wallet;
    }

    /**
     * Membatalkan efek finansial dari transaksi.
     */
    private function revertTransactionEffect(Transaction $transaction, int $userId): void
    {
        $amount = (float) $transaction->amount;

        if ($transaction->type === 'income' && $transaction->wallet_id) {
            $wallet = Wallet::where('id', $transaction->wallet_id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->decrement('balance', $amount);
            }

        } elseif ($transaction->type === 'expense' && $transaction->wallet_id) {
            $wallet = Wallet::where('id', $transaction->wallet_id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $wallet->increment('balance', $amount);
            }

        } elseif ($transaction->type === 'transfer') {
            if ($transaction->source_wallet_id) {
                $source = Wallet::where('id', $transaction->source_wallet_id)
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if ($source) {
                    $source->increment('balance', $amount);
                }
            }

            if ($transaction->destination_wallet_id) {
                $dest = Wallet::where('id', $transaction->destination_wallet_id)
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if ($dest) {
                    $dest->decrement('balance', $amount);
                }
            }
        }
    }
}
