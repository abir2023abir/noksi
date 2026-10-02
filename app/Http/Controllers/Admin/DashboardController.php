<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $month = now()->startOfMonth();

        return view('admin.dashboard', [
            'stats' => [
                'revenue_today' => (float) Order::where('created_at', '>=', $today)->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
                'revenue_month' => (float) Order::where('created_at', '>=', $month)->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
                'orders_today' => Order::where('created_at', '>=', $today)->count(),
                'orders_pending' => Order::whereIn('status', ['pending', 'confirmed'])->count(),
                'customers' => User::role('customer')->count(),
                'products' => Product::count(),
                'low_stock' => Product::where('track_stock', true)->where('stock', '<=', 5)->where('stock', '>', 0)->count(),
                'unread_messages' => ContactMessage::where('is_read', false)->count(),
                'pending_reviews' => Review::where('status', 'pending')->count(),
            ],
            'recentOrders' => Order::with('items')->latest()->take(8)->get(),
            'lowStock' => Product::with('primaryImage')->where('track_stock', true)->where('stock', '<=', 5)->orderBy('stock')->take(6)->get(),
            'topProducts' => Product::orderByDesc('sold_count')->take(5)->get(),
        ]);
    }

    public function chart(Request $request)
    {
        $days = min(90, max(7, (int) $request->get('days', 30)));
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = Order::query()
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', $start)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $labels = $revenue = $orders = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $labels[] = $start->copy()->addDays($i)->format('d M');
            $revenue[] = (float) ($rows[$d]->revenue ?? 0);
            $orders[] = (int) ($rows[$d]->orders ?? 0);
        }

        return response()->json(compact('labels', 'revenue', 'orders'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['products' => [], 'orders' => [], 'customers' => []]);
        }

        return response()->json([
            'products' => Product::where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%")->take(5)->get(['id', 'name', 'sku', 'slug']),
            'orders' => Order::where('order_number', 'like', "%{$q}%")->orWhere('customer_phone', 'like', "%{$q}%")->orWhere('customer_name', 'like', "%{$q}%")->take(5)->get(['id', 'order_number', 'customer_name', 'total', 'status']),
            'customers' => User::role('customer')->where(fn ($b) => $b->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"))->take(5)->get(['id', 'name', 'email', 'phone']),
        ]);
    }
}
