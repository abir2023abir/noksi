<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'payment_meta' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded', 'returned'];

    public const STATUS_COLORS = [
        'pending' => 'amber',
        'confirmed' => 'sky',
        'processing' => 'violet',
        'shipped' => 'blue',
        'delivered' => 'emerald',
        'cancelled' => 'rose',
        'refunded' => 'slate',
        'returned' => 'orange',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (blank($order->order_number)) {
                $order->order_number = static::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'NK'.now()->format('ymd').strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function scopeStatus(Builder $q, string $status): Builder
    {
        return $q->where('status', $status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'slate';
    }

    public function getItemsCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function getCanCancelAttribute(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function getPaymentLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cod' => 'Cash on Delivery',
            'sslcommerz' => 'SSLCommerz',
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            default => ucfirst($this->payment_method),
        };
    }

    public function getProgressAttribute(): int
    {
        return match ($this->status) {
            'pending' => 1,
            'confirmed' => 2,
            'processing' => 3,
            'shipped' => 4,
            'delivered' => 5,
            default => 0,
        };
    }

    public function updateStatus(string $status, ?string $comment = null, ?User $by = null, bool $notify = false): void
    {
        $this->status = $status;
        match ($status) {
            'shipped' => $this->shipped_at = now(),
            'delivered' => $this->delivered_at = now(),
            'cancelled' => $this->cancelled_at = now(),
            default => null,
        };
        if ($status === 'delivered' && $this->payment_method === 'cod') {
            $this->payment_status = 'paid';
            $this->paid_at = now();
        }
        $this->save();

        $this->histories()->create([
            'status' => $status,
            'comment' => $comment,
            'user_id' => $by?->id,
            'notify_customer' => $notify,
        ]);
    }
}
