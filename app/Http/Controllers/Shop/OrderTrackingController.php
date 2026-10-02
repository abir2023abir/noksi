<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('number')) {
            return redirect()->route('order.track', strtoupper(trim($request->number)));
        }

        return view('shop.track', ['order' => null]);
    }

    public function show(string $number)
    {
        $order = Order::with('items', 'histories', 'shippingMethod')->where('order_number', strtoupper($number))->first();

        if (! $order) {
            return view('shop.track', ['order' => null, 'notFound' => $number]);
        }

        return view('shop.track', compact('order'));
    }
}
