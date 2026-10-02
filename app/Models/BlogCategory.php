<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(fn (BlogCategory $c) => $c->slug = $c->slug ?: Str::slug($c->name));
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
