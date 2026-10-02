<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('account.dashboard', [
            'user' => $user,
            'recentOrders' => $user->orders()->with('items')->take(3)->get(),
            'ordersCount' => $user->orders()->count(),
            'wishlistCount' => $user->wishlists()->count(),
            'address' => $user->defaultAddress,
            'picks' => Product::active()->featured()->with('primaryImage', 'images', 'category')->inRandomOrder()->take(4)->get(),
        ]);
    }

    public function reviews()
    {
        return view('account.reviews', [
            'reviews' => auth()->user()->reviews()->with('product.primaryImage')->latest()->paginate(10),
        ]);
    }
}
