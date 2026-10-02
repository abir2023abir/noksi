<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_trending' => 'boolean',
        'is_active' => 'boolean',
        'has_variants' => 'boolean',
        'track_stock' => 'boolean',
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name);
                $i = 1;
                while (static::where('slug', $product->slug)->where('id', '!=', $product->id ?? 0)->exists()) {
                    $product->slug = Str::slug($product->name).'-'.$i++;
                }
            }
            if (blank($product->sku)) {
                $product->sku = 'NK-'.strtoupper(Str::random(6));
            }
        });
    }

    /* ---------- Relationships ---------- */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }

    public function related(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved')->latest();
    }

    public function flashSaleProducts(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    public function wishlistedBy(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    /* ---------- Scopes ---------- */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function scopeNewArrivals(Builder $q): Builder
    {
        return $q->where('is_new', true)->latest();
    }

    public function scopeTrending(Builder $q): Builder
    {
        return $q->where('is_trending', true);
    }

    public function scopeInStock(Builder $q): Builder
    {
        return $q->where(fn ($q) => $q->where('track_stock', false)->orWhere('stock', '>', 0));
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (blank($term)) {
            return $q;
        }
        $term = trim($term);

        return $q->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('name_bn', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%")
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$term}%"))
                ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$term}%"));
        });
    }

    /* ---------- Accessors / helpers ---------- */

    public function getImageUrlAttribute(): string
    {
        $img = $this->relationLoaded('primaryImage') ? $this->primaryImage : $this->primaryImage()->first();

        return $img ? $img->url : asset('images/placeholder.svg');
    }

    public function getHoverImageUrlAttribute(): ?string
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->count() > 1 ? $images[1]->url : null;
    }

    public function getUrlAttribute(): string
    {
        return route('product.show', $this->slug);
    }

    /** Active flash sale entry, if any (resolved once per request for all products). */
    public function activeFlashSale(): ?FlashSaleProduct
    {
        if (! app()->bound('nokshi.flash_map')) {
            app()->instance('nokshi.flash_map', FlashSaleProduct::whereHas('flashSale', fn ($q) => $q->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now()))
                ->with('flashSale')->get()->keyBy('product_id'));
        }

        return app('nokshi.flash_map')->get($this->id);
    }

    public static function forgetFlashSaleCache(): void
    {
        app()->forgetInstance('nokshi.flash_map');
    }

    public function getCurrentPriceAttribute(): float
    {
        if ($fs = $this->activeFlashSale()) {
            return (float) $fs->sale_price;
        }

        return (float) $this->price;
    }

    public function getOriginalPriceAttribute(): ?float
    {
        if ($this->activeFlashSale()) {
            return (float) $this->price;
        }

        return $this->compare_price ? (float) $this->compare_price : null;
    }

    public function getDiscountPercentAttribute(): int
    {
        $orig = $this->original_price;
        if (! $orig || $orig <= $this->current_price) {
            return 0;
        }

        return (int) round((($orig - $this->current_price) / $orig) * 100);
    }

    /** `stock` is kept in sync with the sum of variant stock, so no extra query is needed here. */
    public function getIsInStockAttribute(): bool
    {
        return ! $this->track_stock || $this->stock > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->track_stock && $this->stock > 0 && $this->stock <= $this->low_stock_threshold;
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->stock;
    }

    /** Recompute cached stock from variants (call after any variant change). */
    public function syncStockFromVariants(): void
    {
        if ($this->has_variants) {
            $this->forceFill(['stock' => (int) $this->variants()->where('is_active', true)->sum('stock')])->saveQuietly();
        }
    }

    public function availableSizes(): array
    {
        return $this->variants()->where('is_active', true)->whereNotNull('size')
            ->distinct()->orderByRaw("FIELD(size,'XXS','XS','S','M','L','XL','XXL','3XL','4XL'), size")
            ->pluck('size')->all();
    }

    public function availableColors(): array
    {
        return $this->variants()->where('is_active', true)->whereNotNull('color')
            ->select('color', 'color_code')->distinct()->get()->map(fn ($v) => [
                'name' => $v->color, 'code' => $v->color_code,
            ])->unique('name')->values()->all();
    }

    public function recalculateRating(): void
    {
        $reviews = $this->reviews()->where('status', 'approved');
        $this->forceFill([
            'rating_avg' => round((float) $reviews->avg('rating'), 2),
            'rating_count' => $reviews->count(),
        ])->saveQuietly();
    }
}
