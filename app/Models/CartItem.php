<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $guarded = [];

    protected $casts = ['price' => 'decimal:2'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getLineTotalAttribute(): float
    {
        return (float) $this->price * $this->quantity;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->variant?->image_url ?? $this->product->image_url;
    }

    public function getAvailableStockAttribute(): int
    {
        if (! $this->product->track_stock) {
            return 999;
        }

        return $this->variant ? $this->variant->stock : $this->product->stock;
    }
}
