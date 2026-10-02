<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $guarded = [];

    protected $casts = ['tags' => 'array', 'is_featured' => 'boolean', 'published_at' => 'datetime'];

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            $post->slug = $post->slug ?: Str::slug($post->title);
            $post->reading_time = max(1, (int) ceil(Str::wordCount(strip_tags($post->body)) / 200));
            if ($post->status === 'published' && ! $post->published_at) {
                $post->published_at = now();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')->where('published_at', '<=', now())->latest('published_at');
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/placeholder.svg');
        }

        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
    }
}
