<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(fn (Coupon $c) => $c->code = strtoupper(trim($c->code)));
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
    }

    public function isValidFor(float $subtotal, ?User $user = null): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }
        if ($this->min_order && $subtotal < (float) $this->min_order) {
            return false;
        }
        if ($user && $this->per_user_limit) {
            if ($this->usages()->where('user_id', $user->id)->count() >= $this->per_user_limit) {
                return false;
            }
        }

        return true;
    }

    public function validationMessage(float $subtotal): string
    {
        if (! $this->is_active || ($this->expires_at && $this->expires_at->isPast())) {
            return __('This coupon has expired.');
        }
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return __('This coupon has reached its usage limit.');
        }
        if ($this->min_order && $subtotal < (float) $this->min_order) {
            return __('Minimum order of :amount required.', ['amount' => money($this->min_order)]);
        }

        return __('This coupon is not valid.');
    }

    public function calculateDiscount(float $subtotal): float
    {
        $discount = match ($this->type) {
            'fixed' => (float) $this->value,
            'percent' => $subtotal * ((float) $this->value / 100),
            default => 0,
        };
        if ($this->max_discount) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return round(min($discount, $subtotal), 2);
    }

    public function getLabelAttribute(): string
    {
        return match ($this->type) {
            'fixed' => money($this->value).' off',
            'percent' => rtrim(rtrim($this->value, '0'), '.').'% off',
            'free_shipping' => 'Free shipping',
        };
    }
}
