<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingMethod extends Model
{
    protected $guarded = [];

    protected $casts = ['cost' => 'decimal:2', 'free_above' => 'decimal:2', 'is_active' => 'boolean'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public function costFor(float $subtotal): float
    {
        if ($this->free_above !== null && $subtotal >= (float) $this->free_above) {
            return 0;
        }

        return (float) $this->cost;
    }

    /** Methods available for a given district (zone match or zone-less/global). */
    public static function availableFor(?string $district): \Illuminate\Support\Collection
    {
        return static::active()->with('zone')->get()->filter(function (ShippingMethod $m) use ($district) {
            if (! $m->zone) {
                return true;
            }

            return $m->zone->is_active && $m->zone->covers($district);
        })->values();
    }
}
