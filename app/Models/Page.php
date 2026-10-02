<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'show_in_footer' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(fn (Page $p) => $p->slug = $p->slug ?: Str::slug($p->title));
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }
}
