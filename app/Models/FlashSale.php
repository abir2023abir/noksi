<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FlashSale extends Model
{
    protected $guarded = [];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(fn (FlashSale $f) => $f->slug = $f->slug ?: Str::slug($f->title).'-'.Str::lower(Str::random(4)));
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class)->with('product.primaryImage');
    }

    public function scopeRunning(Builder $q): Builder
    {
        return $q->where('is_active', true)->where('starts_at', '<=', now())->where('ends_at', '>=', now());
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('is_active', true)->where('starts_at', '>', now());
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->is_active && $this->starts_at->isPast() && $this->ends_at->isFuture();
    }

    public function getStatusAttribute(): string
    {
        if (! $this->is_active) {
            return 'disabled';
        }
        if ($this->ends_at->isPast()) {
            return 'ended';
        }
        if ($this->starts_at->isFuture()) {
            return 'scheduled';
        }

        return 'running';
    }
}
