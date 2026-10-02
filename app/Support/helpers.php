<?php

use App\Models\Setting;
use App\Services\CartService;

if (! function_exists('money')) {
    /** Format an amount in the store currency (BDT by default). */
    function money(float|int|string|null $amount, bool $symbol = true): string
    {
        $amount = (float) $amount;
        $formatted = number_format($amount, $amount == floor($amount) ? 0 : 2);
        $sym = setting('currency_symbol', '৳');

        return $symbol ? $sym.$formatted : $formatted;
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::get($key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (! function_exists('cart')) {
    function cart(): CartService
    {
        return app(CartService::class);
    }
}

if (! function_exists('bn_number')) {
    /** Convert western digits to Bangla digits when the locale is bn. */
    function bn_number(string|int|float $value): string
    {
        if (app()->getLocale() !== 'bn') {
            return (string) $value;
        }

        return strtr((string) $value, ['0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯']);
    }
}

if (! function_exists('active_class')) {
    function active_class(string|array $patterns, string $class = 'is-active', string $fallback = ''): string
    {
        return request()->routeIs($patterns) ? $class : $fallback;
    }
}

if (! function_exists('status_badge')) {
    /** Tailwind classes for an order status pill. */
    function status_badge(string $status): string
    {
        return match ($status) {
            'pending', 'unpaid' => 'bg-amber-100 text-amber-800 ring-amber-200',
            'confirmed' => 'bg-sky-100 text-sky-800 ring-sky-200',
            'processing' => 'bg-violet-100 text-violet-800 ring-violet-200',
            'shipped' => 'bg-blue-100 text-blue-800 ring-blue-200',
            'delivered', 'paid', 'approved', 'active', 'published', 'running' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            'cancelled', 'failed', 'rejected', 'inactive', 'disabled' => 'bg-rose-100 text-rose-800 ring-rose-200',
            'refunded', 'draft', 'ended' => 'bg-slate-100 text-slate-700 ring-slate-200',
            'returned', 'scheduled' => 'bg-orange-100 text-orange-800 ring-orange-200',
            default => 'bg-slate-100 text-slate-700 ring-slate-200',
        };
    }
}

if (! function_exists('img')) {
    /** Resolve a stored path or absolute URL to a displayable URL. */
    function img(?string $path, string $fallback = 'images/placeholder.svg'): string
    {
        if (blank($path)) {
            return asset($fallback);
        }

        return str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
    }
}
