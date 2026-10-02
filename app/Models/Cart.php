<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class)->with(['product.primaryImage', 'variant']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn (CartItem $i) => $i->price * $i->quantity);
    }

    public function getCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function getDiscountAttribute(): float
    {
        if (! $this->coupon || ! $this->coupon->isValidFor($this->subtotal)) {
            return 0;
        }

        return $this->coupon->calculateDiscount($this->subtotal);
    }

    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - $this->discount);
    }
}
