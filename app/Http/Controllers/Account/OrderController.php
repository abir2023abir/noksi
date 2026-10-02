<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->orders()->with('items');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('account.orders', ['orders' => $query->paginate(8)->withQueryString()]);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        return view('account.order', ['order' => $order->load('items.product', 'histories', 'shippingMethod')]);
    }

    public function cancel(Order $order, OrderService $orders)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        if (! $order->can_cancel) {
            return back()->with('error', __('This order can no longer be cancelled.'));
        }

        $order->updateStatus('cancelled', __('Cancelled by customer'), auth()->user());
        $orders->restock($order);

        return back()->with('success', __('Order cancelled.'));
    }

    public function invoice(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        return view('shop.invoice', ['order' => $order->load('items')]);
    }
}
