<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\OrderPlaced;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Turn a validated cart into an order (stock deducted, coupon consumed).
     *
     * @param  array  $address  name, phone, email, address_line, area, city, district, postal_code
     */
    public function place(Cart $cart, array $address, ShippingMethod $shipping, string $paymentMethod, ?string $notes, ?User $user): Order
    {
        return DB::transaction(function () use ($cart, $address, $shipping, $paymentMethod, $notes, $user) {
            $subtotal = $cart->subtotal;
            $discount = $cart->discount;
            $shippingCost = $shipping->costFor($subtotal);
            if ($cart->coupon && $cart->coupon->type === 'free_shipping' && $cart->coupon->isValidFor($subtotal, $user)) {
                $shippingCost = 0;
            }
            $total = max(0, $subtotal - $discount + $shippingCost);

            $order = Order::create([
                'user_id' => $user?->id,
                'coupon_id' => $cart->coupon_id,
                'shipping_method_id' => $shipping->id,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'currency' => setting('currency_code', 'BDT'),
                'customer_name' => $address['name'],
                'customer_email' => $address['email'] ?? $user?->email,
                'customer_phone' => $address['phone'],
                'shipping_address' => $address,
                'notes' => $notes,
                'ip_address' => request()->ip(),
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;
                $variant = $item->variant;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'name' => $product->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'image' => $variant?->image ?? $product->primaryImage?->path,
                    'size' => $variant?->size,
                    'color' => $variant?->color,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                if ($product->track_stock) {
                    if ($variant) {
                        $variant->decrement('stock', $item->quantity);
                        $product->syncStockFromVariants();
                    } else {
                        $product->decrement('stock', $item->quantity);
                    }
                    StockMovement::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'user_id' => $user?->id,
                        'quantity' => -$item->quantity,
                        'type' => 'sale',
                        'reference' => $order->order_number,
                    ]);
                }
                $product->increment('sold_count', $item->quantity);
                if ($fs = $product->activeFlashSale()) {
                    $fs->increment('sold', $item->quantity);
                }
            }

            if ($cart->coupon && $discount > 0 || ($cart->coupon?->type === 'free_shipping')) {
                $cart->coupon->increment('used_count');
                CouponUsage::create([
                    'coupon_id' => $cart->coupon_id,
                    'user_id' => $user?->id,
                    'order_id' => $order->id,
                    'discount' => $discount,
                ]);
            }

            $order->histories()->create(['status' => 'pending', 'comment' => 'Order placed']);

            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            try {
                if ($order->customer_email) {
                    $order->notify(new OrderPlaced($order));
                }
            } catch (\Throwable) {
                // Mail failures must never block checkout.
            }

            return $order->load('items');
        });
    }

    public function markPaid(Order $order, string $transactionId, array $meta = []): void
    {
        $order->update([
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'payment_meta' => $meta,
            'paid_at' => now(),
            'status' => $order->status === 'pending' ? 'confirmed' : $order->status,
        ]);
        $order->histories()->create(['status' => 'confirmed', 'comment' => 'Payment received via '.$order->payment_label]);
    }

    public function restock(Order $order): void
    {
        foreach ($order->items as $item) {
            if (! $item->product || ! $item->product->track_stock) {
                continue;
            }
            if ($item->variant) {
                $item->variant->increment('stock', $item->quantity);
                $item->product->syncStockFromVariants();
            } else {
                $item->product->increment('stock', $item->quantity);
            }
            StockMovement::create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'user_id' => auth()->id(),
                'quantity' => $item->quantity,
                'type' => 'return',
                'reference' => $order->order_number,
            ]);
        }
    }
}
