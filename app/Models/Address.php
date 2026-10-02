<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $guarded = [];

    protected $casts = ['is_default' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (Address $address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id ?? 0)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address_line, $this->area, $this->city, $this->district, $this->postal_code])
            ->filter()->implode(', ');
    }

    public function toSnapshot(): array
    {
        return $this->only(['name', 'phone', 'email', 'address_line', 'area', 'city', 'district', 'postal_code', 'country']);
    }
}
