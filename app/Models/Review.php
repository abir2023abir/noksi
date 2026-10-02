<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $guarded = [];

    protected $casts = ['images' => 'array', 'is_verified' => 'boolean'];

    protected static function booted(): void
    {
        $recalc = fn (Review $r) => $r->product?->recalculateRating();
        static::saved($recalc);
        static::deleted($recalc);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->name ?? 'Anonymous';
    }
}
