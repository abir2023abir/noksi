<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('sort_order');
    }

    public function scopePosition(Builder $q, string $position): Builder
    {
        return $q->where('position', $position);
    }

    public function getImageUrlAttribute(): string
    {
        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
    }

    public function getMobileImageUrlAttribute(): string
    {
        if (! $this->mobile_image) {
            return $this->image_url;
        }

        return str_starts_with($this->mobile_image, 'http') ? $this->mobile_image : asset('storage/'.$this->mobile_image);
    }
}
