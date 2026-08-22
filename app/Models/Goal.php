<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        // 'priority', // abaikan jika belum digunakan
        // 'priority_order', // abaikan jika belum digunakan
        'is_completed',
        // Tambahan kolom baru:
        'purchase_link',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'deadline' => 'date',
            'is_completed' => 'boolean',
            'priority_order' => 'integer',
        ];
    }

    // --- RELATIONSHIPS ---
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }
}
