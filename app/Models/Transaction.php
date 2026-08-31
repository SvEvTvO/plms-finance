<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'date',
        'wallet_id',
        'category_id',
        'source_wallet_id',
        'destination_wallet_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id'               => 'integer',
            'wallet_id'             => 'integer',
            'category_id'           => 'integer',
            'source_wallet_id'      => 'integer',
            'destination_wallet_id' => 'integer',
            'amount'                => 'decimal:2',
            'date'                  => 'date',
        ];
    }

    // --- QUERY SCOPES ---

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    public function scopeTransfer(Builder $query): Builder
    {
        return $query->where('type', 'transfer');
    }

    // --- RELATIONSHIPS ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sourceWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'source_wallet_id');
    }

    public function destinationWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'destination_wallet_id');
    }
}
