<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $guarded = [];

    protected $casts = ['districts' => 'array', 'is_active' => 'boolean'];

    public function methods(): HasMany
    {
        return $this->hasMany(ShippingMethod::class)->orderBy('sort_order');
    }

    public function covers(?string $district): bool
    {
        if (empty($this->districts)) {
            return true;
        }

        return in_array(strtolower(trim((string) $district)), array_map('strtolower', $this->districts));
    }
}
