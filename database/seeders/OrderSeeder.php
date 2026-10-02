<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/** Demo orders so the admin dashboard has something to chart. */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Order::count() > 0) {
            return;
        }

        $customers = User::role('customer')->get();
        $names = ['Mehedi Hasan', 'Sumaiya Akter', 'Arif Chowdhury', 'Nabila Islam', 'Rakib Khan', 'Priya Das', 'Imran Sarker', 'Lamia Haque'];
        $cities = [['Dhaka', 'Dhaka'], ['Chattogram', 'Chattogram'], ['Sylhet', 'Sylhet'], ['Khulna', 'Khulna'], ['Rajshahi', 'Rajshahi'], ['Dhaka', 'Dhaka'], ['Dhaka', 'Dhaka']];
        $methods = ShippingMethod::all();
        $products = Product::with('variants', 'primaryImage')->get();

        for ($i = 0; $i < 40; $i++) {
            $createdAt = now()->subDays(rand(0, 45))->subMinutes(rand(0, 1400));
            $user = rand(0, 3) === 0 ? null : $customers->random();
            $name = $user?->name ?? Arr::random($names);
            [$city, $district] = Arr::random($cities);
            $status = Arr::random(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'delivered', 'delivered', 'cancelled']);
            $payment = Arr::random(['cod', 'cod', 'sslcommerz']);
            $paid = $status === 'delivered' || ($payment === 'sslcommerz' && $status !== 'cancelled');
            $method = $methods->random();

            $items = $products->random(rand(1, 3));
            $subtotal = 0;
            $lines = [];
            foreach ($items as $p) {
                $variant = $p->variants->random();
                $qty = rand(1, 2);
                $price = (float) $p->price;
                $subtotal += $price * $qty;
                $lines[] = compact('p', 'variant', 'qty', 'price');
            }
            $shipping = $method->costFor($subtotal);

            $order = Order::create([
                'user_id' => $user?->id,
                'shipping_method_id' => $method->id,
                'status' => $status,
                'payment_method' => $payment,
                'payment_status' => $paid ? 'paid' : ($status === 'cancelled' ? 'failed' : 'unpaid'),
                'transaction_id' => $payment === 'sslcommerz' && $paid ? 'TXN'.strtoupper(str()->random(10)) : null,
                'subtotal' => $subtotal,
                'discount' => 0,
                'shipping_cost' => $shipping,
                'total' => $subtotal + $shipping,
                'customer_name' => $name,
                'customer_email' => $user?->email ?? str($name)->slug('.').'@example.com',
                'customer_phone' => '017'.rand(10000000, 99999999),
                'shipping_address' => [
                    'name' => $name, 'phone' => '017'.rand(10000000, 99999999),
                    'address_line' => 'House '.rand(1, 99).', Road '.rand(1, 30), 'area' => Arr::random(['Banani', 'Mirpur', 'Agrabad', 'Zindabazar', 'Uttara', 'Dhanmondi']),
                    'city' => $city, 'district' => $district, 'postal_code' => (string) rand(1000, 9400), 'country' => 'Bangladesh',
                ],
                'paid_at' => $paid ? $createdAt->copy()->addHours(rand(1, 30)) : null,
                'shipped_at' => in_array($status, ['shipped', 'delivered']) ? $createdAt->copy()->addDay() : null,
                'delivered_at' => $status === 'delivered' ? $createdAt->copy()->addDays(2) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($lines as ['p' => $p, 'variant' => $variant, 'qty' => $qty, 'price' => $price]) {
                $order->items()->create([
                    'product_id' => $p->id, 'product_variant_id' => $variant->id,
                    'name' => $p->name, 'sku' => $variant->sku, 'image' => $p->primaryImage?->path,
                    'size' => $variant->size, 'color' => $variant->color,
                    'quantity' => $qty, 'price' => $price, 'total' => $price * $qty,
                ]);
            }
            $order->histories()->create(['status' => 'pending', 'comment' => 'Order placed', 'created_at' => $createdAt]);
            if ($status !== 'pending') {
                $order->histories()->create(['status' => $status, 'comment' => 'Status updated', 'created_at' => $createdAt->copy()->addHours(5)]);
            }
        }
    }
}
