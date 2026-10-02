<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from') ?? now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?? now()->endOfDay();

        $base = Order::whereBetween('created_at', [$from, $to])->whereNotIn('status', ['cancelled', 'refunded']);

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'stats' => [
                'revenue' => (float) (clone $base)->sum('total'),
                'orders' => (clone $base)->count(),
                'avg_order' => (float) (clone $base)->avg('total'),
                'customers' => User::role('customer')->whereBetween('created_at', [$from, $to])->count(),
            ],
        ]);
    }

    public function sales(Request $request)
    {
        $from = $request->date('from') ?? now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?? now()->endOfDay();

        $rows = Order::query()
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(discount) as discounts')
            )
            ->whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day')
            ->get();

        return view('admin.reports.sales', compact('rows', 'from', 'to'));
    }

    public function products(Request $request)
    {
        $from = $request->date('from') ?? now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?? now()->endOfDay();

        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereNotIn('orders.status', ['cancelled', 'refunded'])
            ->select(
                'order_items.product_id',
                'order_items.name',
                DB::raw('SUM(order_items.quantity) as qty_sold'),
                DB::raw('SUM(order_items.total) as revenue')
            )
            ->groupBy('order_items.product_id', 'order_items.name')
            ->orderByDesc('qty_sold')
            ->limit(50)
            ->get();

        $lowStock = Product::where('track_stock', true)->where('stock', '<=', 5)->orderBy('stock')->take(20)->get();

        return view('admin.reports.products', compact('rows', 'lowStock', 'from', 'to'));
    }

    public function customers(Request $request)
    {
        $from = $request->date('from') ?? now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?? now()->endOfDay();

        $rows = User::role('customer')
            ->withCount(['orders as orders_count' => fn ($q) => $q->whereBetween('created_at', [$from, $to])->whereNotIn('status', ['cancelled', 'refunded'])])
            ->withSum(['orders as revenue' => fn ($q) => $q->whereBetween('created_at', [$from, $to])->whereNotIn('status', ['cancelled', 'refunded'])], 'total')
            ->orderByDesc('revenue')
            ->take(50)
            ->get();

        return view('admin.reports.customers', compact('rows', 'from', 'to'));
    }
}
