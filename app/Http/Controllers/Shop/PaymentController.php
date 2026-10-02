<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected SslCommerzService $ssl, protected OrderService $orders) {}

    public function success(Request $request)
    {
        $order = Order::where('order_number', $request->input('tran_id'))->first();
        if (! $order) {
            return redirect()->route('home')->with('error', __('Order not found.'));
        }

        session(['last_order' => $order->order_number]);

        if ($order->payment_status !== 'paid') {
            try {
                $data = $this->ssl->validate($request->input('val_id'), $order);
                $this->orders->markPaid($order, $data['bank_tran_id'] ?? $request->input('bank_tran_id', $request->input('val_id')), [
                    'card_type' => $data['card_type'] ?? null,
                    'card_issuer' => $data['card_issuer'] ?? null,
                    'val_id' => $request->input('val_id'),
                ]);
            } catch (\Throwable $e) {
                report($e);

                return redirect()->route('checkout.success', $order->order_number)
                    ->with('error', __('We could not verify your payment yet. If money was deducted, it will be confirmed shortly.'));
            }
        }

        return redirect()->route('checkout.success', $order->order_number)->with('success', __('Payment successful!'));
    }

    public function fail(Request $request)
    {
        $order = Order::where('order_number', $request->input('tran_id'))->first();
        if ($order && $order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'failed', 'payment_meta' => ['error' => $request->input('error')]]);
            session(['last_order' => $order->order_number]);
        }

        return redirect()->route('checkout.success', $order?->order_number ?? '')
            ->with('error', __('Payment failed. You can retry or switch to cash on delivery.'));
    }

    public function cancel(Request $request)
    {
        $order = Order::where('order_number', $request->input('tran_id'))->first();
        if ($order) {
            session(['last_order' => $order->order_number]);

            return redirect()->route('checkout.success', $order->order_number)->with('error', __('Payment was cancelled. Your order is saved as unpaid.'));
        }

        return redirect()->route('cart.index');
    }

    public function ipn(Request $request)
    {
        $order = Order::where('order_number', $request->input('tran_id'))->first();
        if ($order && $order->payment_status !== 'paid' && $request->input('status') === 'VALID') {
            try {
                $data = $this->ssl->validate($request->input('val_id'), $order);
                $this->orders->markPaid($order, $data['bank_tran_id'] ?? $request->input('val_id'), ['ipn' => true]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response('OK');
    }
}
