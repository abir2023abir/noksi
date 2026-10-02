<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->withCount('items')
            ->when($request->q, function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $q->where('order_number', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%")
                        ->orWhere('customer_phone', 'like', "%{$term}%")
                        ->orWhere('customer_email', 'like', "%{$term}%");
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->payment_status, fn ($q, $status) => $q->where('payment_status', $status))
            ->when($request->from, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($request->to, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'coupon', 'shippingMethod', 'histories.user']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $orders = Order::query()
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->from, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($request->to, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Date', 'Customer', 'Phone', 'Status', 'Payment', 'Total', 'Items']);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->order_number,
                    $order->created_at?->toDateTimeString(),
                    $order->customer_name,
                    $order->customer_phone,
                    $order->status,
                    $order->payment_status,
                    $order->total,
                    $order->items()->count(),
                ]);
            }
            fclose($out);
        }, 'orders-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function invoice(Order $order)
    {
        $order->load(['items', 'user', 'shippingMethod']);

        return view('admin.orders.invoice', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
            'comment' => ['nullable', 'string', 'max:500'],
            'notify' => ['nullable', 'boolean'],
        ]);

        $order->updateStatus(
            $data['status'],
            $data['comment'] ?? null,
            $request->user(),
            $request->boolean('notify')
        );

        return back()->with('success', __('Order status updated.'));
    }

    public function updatePayment(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_status' => ['required', 'in:unpaid,paid,failed,refunded,partially_refunded'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'in:cod,sslcommerz,bkash,nagad,card'],
        ]);

        if ($data['payment_status'] === 'paid' && ! $order->paid_at) {
            $data['paid_at'] = now();
        }

        $order->update($data);

        return back()->with('success', __('Payment updated.'));
    }

    public function updateTracking(Request $request, Order $order)
    {
        $data = $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'courier' => ['nullable', 'string', 'max:100'],
        ]);

        $order->update($data);

        return back()->with('success', __('Tracking updated.'));
    }

    public function updateNotes(Request $request, Order $order)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $order->update($data);

        return back()->with('success', __('Notes updated.'));
    }
}
