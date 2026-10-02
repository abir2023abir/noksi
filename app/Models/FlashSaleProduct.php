<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleProduct extends Model
{
    protected $guarded = [];

    protected $casts = ['sale_price' => 'decimal:2'];

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountPercentAttribute(): int
    {
        $p = (float) $this->product->price;

        return $p > 0 ? (int) round((($p - (float) $this->sale_price) / $p) * 100) : 0;
    }

    public function getSoldPercentAttribute(): int
    {
        if (! $this->quantity_limit) {
            return 0;
        }

        return (int) min(100, round($this->sold / $this->quantity_limit * 100));
    }
}
