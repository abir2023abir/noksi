<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    protected static ?array $memo = null;

    protected static function booted(): void
    {
        $flush = function () {
            Cache::forget('settings.all');
            static::$memo = null;
        };
        static::saved($flush);
        static::deleted($flush);
    }

    public static function all_cached(): array
    {
        return static::$memo ??= Cache::rememberForever('settings.all', fn () => static::pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all_cached()[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group, 'type' => $type]);
    }
}
