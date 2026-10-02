<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Minimal SSLCommerz hosted-checkout integration.
 * Docs: https://developer.sslcommerz.com/doc/v4/
 */
class SslCommerzService
{
    protected string $storeId;

    protected string $storePassword;

    protected bool $sandbox;

    public function __construct()
    {
        $this->storeId = setting('sslcommerz_store_id') ?: config('services.sslcommerz.store_id');
        $this->storePassword = setting('sslcommerz_store_password') ?: config('services.sslcommerz.store_password');
        $this->sandbox = filter_var(setting('sslcommerz_sandbox', config('services.sslcommerz.sandbox')), FILTER_VALIDATE_BOOL);
    }

    protected function baseUrl(): string
    {
        return $this->sandbox ? 'https://sandbox.sslcommerz.com' : 'https://securepay.sslcommerz.com';
    }

    /** Create a session and return the gateway redirect URL. */
    public function createSession(Order $order): string
    {
        $addr = $order->shipping_address;

        $payload = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => number_format((float) $order->total, 2, '.', ''),
            'currency' => 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => route('payment.sslcommerz.success'),
            'fail_url' => route('payment.sslcommerz.fail'),
            'cancel_url' => route('payment.sslcommerz.cancel'),
            'ipn_url' => route('payment.sslcommerz.ipn'),
            'cus_name' => $order->customer_name,
            'cus_email' => $order->customer_email ?: 'customer@example.com',
            'cus_add1' => $addr['address_line'] ?? 'N/A',
            'cus_city' => $addr['city'] ?? 'Dhaka',
            'cus_postcode' => $addr['postal_code'] ?? '1000',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $order->customer_phone,
            'shipping_method' => 'YES',
            'ship_name' => $addr['name'] ?? $order->customer_name,
            'ship_add1' => $addr['address_line'] ?? 'N/A',
            'ship_city' => $addr['city'] ?? 'Dhaka',
            'ship_postcode' => $addr['postal_code'] ?? '1000',
            'ship_country' => 'Bangladesh',
            'product_name' => $order->items->pluck('name')->take(3)->implode(', '),
            'product_category' => 'Clothing',
            'product_profile' => 'physical-goods',
            'num_of_item' => $order->items->sum('quantity'),
            'value_a' => $order->id,
        ];

        $response = Http::asForm()->timeout(30)->post($this->baseUrl().'/gwprocess/v4/api.php', $payload);
        $data = $response->json();

        if (! $response->successful() || ($data['status'] ?? '') !== 'SUCCESS' || empty($data['GatewayPageURL'])) {
            throw new RuntimeException($data['failedreason'] ?? 'Could not initiate SSLCommerz payment.');
        }

        $order->update(['payment_meta' => ['sessionkey' => $data['sessionkey'] ?? null]]);

        return $data['GatewayPageURL'];
    }

    /** Validate a transaction against SSLCommerz to prevent tampering. */
    public function validate(string $valId, Order $order): array
    {
        $response = Http::timeout(30)->get($this->baseUrl().'/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json',
        ]);
        $data = $response->json() ?? [];

        $ok = in_array($data['status'] ?? '', ['VALID', 'VALIDATED'])
            && ($data['tran_id'] ?? null) === $order->order_number
            && abs((float) ($data['amount'] ?? 0) - (float) $order->total) < 1
            && strtoupper($data['currency'] ?? '') === 'BDT';

        if (! $ok) {
            throw new RuntimeException('Payment validation failed.');
        }

        return $data;
    }
}
