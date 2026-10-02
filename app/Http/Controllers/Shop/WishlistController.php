<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        if (! auth()->check()) {
            return redirect()->route('login', ['redirect' => route('wishlist.index')]);
        }

        $products = Product::whereIn('id', auth()->user()->wishlists()->pluck('product_id'))
            ->with('primaryImage', 'images', 'category')->get();

        return view('account.wishlist', compact('products'));
    }

    public function toggle(Request $request): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['message' => __('Please login to save items.')], 401);
        }

        $productId = (int) $request->validate(['product_id' => ['required', 'exists:products,id']])['product_id'];
        $user = auth()->user();

        $existing = Wishlist::where('user_id', $user->id)->where('product_id', $productId)->first();
        if ($existing) {
            $existing->delete();
            $message = __('Removed from wishlist');
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $productId]);
            $message = __('Saved to wishlist');
        }

        return response()->json([
            'message' => $message,
            'ids' => $user->wishlists()->pluck('product_id')->values(),
        ]);
    }
}
