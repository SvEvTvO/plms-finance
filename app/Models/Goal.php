<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        'is_completed',
        'purchase_link',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'user_id'        => 'integer',
            'target_amount'  => 'decimal:2',
            'current_amount' => 'decimal:2',
            'deadline'       => 'date',
            'is_completed'   => 'boolean',
        ];
    }

    // --- ACCESSORS (Untuk mempermudah tampilan Blade) ---

    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->target_amount <= 0) return 0;
                $percent = ($this->current_amount / $this->target_amount) * 100;
                return min(100, round($percent, 1));
            }
        );
    }

    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->target_amount - $this->current_amount)
        );
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
