<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CartService
{
    protected ?Cart $cart = null;

    public function current(bool $create = true): ?Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        $sessionId = $this->sessionId();
        $user = auth()->user();

        $query = Cart::with('items', 'coupon');
        $cart = $user
            ? $query->where('user_id', $user->id)->first()
            : $query->where('session_id', $sessionId)->whereNull('user_id')->first();

        // A guest who just logged in: adopt their session cart.
        if ($user && ! $cart) {
            $guest = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
            if ($guest) {
                $guest->update(['user_id' => $user->id]);
                $cart = $guest->load('items', 'coupon');
            }
        } elseif ($user && $cart) {
            $guest = Cart::where('session_id', $sessionId)->whereNull('user_id')->with('items')->first();
            if ($guest && $guest->id !== $cart->id) {
                foreach ($guest->items as $item) {
                    $this->mergeItem($cart, $item);
                }
                $guest->delete();
                $cart->load('items');
            }
        }

        if (! $cart && $create) {
            $cart = Cart::create(['user_id' => $user?->id, 'session_id' => $sessionId]);
            $cart->setRelation('items', collect());
        }

        return $this->cart = $cart;
    }

    protected function sessionId(): string
    {
        if (! session()->has('cart_session_id')) {
            session()->put('cart_session_id', (string) Str::uuid());
        }

        return session('cart_session_id');
    }

    protected function mergeItem(Cart $cart, CartItem $item): void
    {
        $existing = $cart->items()->where('product_id', $item->product_id)
            ->where('product_variant_id', $item->product_variant_id)->first();
        if ($existing) {
            $existing->increment('quantity', $item->quantity);
            $item->delete();
        } else {
            $item->update(['cart_id' => $cart->id]);
        }
    }

    public function add(Product $product, ?ProductVariant $variant, int $qty = 1): CartItem
    {
        if (! $product->is_active) {
            throw new InvalidArgumentException(__('This product is unavailable.'));
        }
        if ($product->has_variants && ! $variant) {
            throw new InvalidArgumentException(__('Please select a size/colour.'));
        }
        if ($variant && $variant->product_id !== $product->id) {
            throw new InvalidArgumentException(__('Invalid variant.'));
        }

        $cart = $this->current();
        $item = $cart->items()->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)->first();

        $newQty = ($item?->quantity ?? 0) + $qty;
        $stock = $product->track_stock ? ($variant ? $variant->stock : $product->stock) : PHP_INT_MAX;
        if ($newQty > $stock) {
            throw new InvalidArgumentException($stock > 0
                ? __('Only :n left in stock.', ['n' => $stock])
                : __('Out of stock.'));
        }

        $price = $this->unitPrice($product, $variant);

        if ($item) {
            $item->update(['quantity' => $newQty, 'price' => $price]);
        } else {
            $item = $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $qty,
                'price' => $price,
            ]);
        }

        $this->refresh();

        return $item;
    }

    public function unitPrice(Product $product, ?ProductVariant $variant): float
    {
        if ($fs = $product->activeFlashSale()) {
            return (float) $fs->sale_price;
        }

        return $variant && $variant->price ? (float) $variant->price : (float) $product->price;
    }

    public function update(int $itemId, int $qty): void
    {
        $item = $this->current()->items()->findOrFail($itemId);
        if ($qty <= 0) {
            $item->delete();
        } else {
            $qty = min($qty, $item->available_stock);
            $item->update(['quantity' => $qty]);
        }
        $this->refresh();
    }

    public function remove(int $itemId): void
    {
        $this->current()->items()->where('id', $itemId)->delete();
        $this->refresh();
    }

    public function clear(): void
    {
        $cart = $this->current();
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);
        $this->refresh();
    }

    public function applyCoupon(string $code): Coupon
    {
        $cart = $this->current();
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();
        if (! $coupon) {
            throw new InvalidArgumentException(__('Coupon code not found.'));
        }
        if (! $coupon->isValidFor($cart->subtotal, auth()->user())) {
            throw new InvalidArgumentException($coupon->validationMessage($cart->subtotal));
        }
        $cart->update(['coupon_id' => $coupon->id]);
        $this->refresh();

        return $coupon;
    }

    public function removeCoupon(): void
    {
        $this->current()->update(['coupon_id' => null]);
        $this->refresh();
    }

    public function refresh(): void
    {
        $this->cart = $this->cart?->fresh(['items', 'coupon']);
    }

    /** Re-validate prices & stock before checkout, returns list of problems. */
    public function validateForCheckout(): array
    {
        $problems = [];
        foreach ($this->current()->items as $item) {
            if (! $item->product || ! $item->product->is_active) {
                $problems[] = __(':name is no longer available.', ['name' => $item->product?->name ?? 'An item']);
                $item->delete();

                continue;
            }
            if ($item->quantity > $item->available_stock) {
                $problems[] = __('Only :n of :name available.', ['n' => $item->available_stock, 'name' => $item->product->name]);
                $item->update(['quantity' => max(0, $item->available_stock)]);
            }
            $price = $this->unitPrice($item->product, $item->variant);
            if ((float) $item->price !== $price) {
                $item->update(['price' => $price]);
            }
        }
        $this->refresh();

        return $problems;
    }

    public function summary(): array
    {
        $cart = $this->current();

        return [
            'count' => $cart->count,
            'subtotal' => $cart->subtotal,
            'discount' => $cart->discount,
            'total' => $cart->total,
            'subtotal_formatted' => money($cart->subtotal),
            'discount_formatted' => money($cart->discount),
            'total_formatted' => money($cart->total),
            'coupon' => $cart->coupon?->code,
        ];
    }
}
