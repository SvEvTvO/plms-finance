<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Saving extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'goal_id',
        'wallet_id',
        'amount',
        'date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'user_id'   => 'integer',
            'goal_id'   => 'integer',
            'wallet_id' => 'integer',
            'amount'    => 'decimal:2',
            'date'      => 'date',
        ];
    }

    // --- RELATIONSHIPS ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
