<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'qr_token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Table $table) {
            if (empty($table->qr_token)) {
                $table->qr_token = 'tbl_'.strtolower(Str::random(12));
            }
        });
    }

    public function getQrUrlAttribute(): string
    {
        return url('/order/'.$this->qr_token);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrders(): HasMany
    {
        return $this->hasMany(Order::class)
            ->whereIn('order_status', ['pending_confirmation', 'pending', 'cooking', 'ready', 'served'])
            ->latest('id');
    }

    public function currentSessionOrderRelation(): HasOne
    {
        return $this->hasOne(Order::class)
            ->whereIn('order_status', ['pending_confirmation', 'pending', 'cooking', 'ready', 'served'])
            ->oldestOfMany('id');
    }

    public function currentSessionOrder(): ?Order
    {
        if ($this->relationLoaded('currentSessionOrderRelation')) {
            return $this->currentSessionOrderRelation;
        }

        return $this->currentSessionOrderRelation()->first();
    }
}
