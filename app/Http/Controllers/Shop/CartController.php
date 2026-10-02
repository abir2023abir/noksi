<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index(): View
    {
        $cart = $this->cart->current();
        $suggestions = Product::active()->trending()->whereNotIn('id', $cart->items->pluck('product_id'))
            ->with('primaryImage', 'images', 'category')->inRandomOrder()->take(4)->get();

        return view('shop.cart', compact('cart', 'suggestions'));
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $variant = ! empty($data['variant_id']) ? ProductVariant::find($data['variant_id']) : null;

        // Simple products with a single variant: auto-pick it.
        if (! $variant && $product->has_variants && $product->variants()->count() === 1) {
            $variant = $product->variants()->first();
        }

        try {
            $this->cart->add($product, $variant, (int) ($data['quantity'] ?? 1));
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return $this->respond(__(':name added to your bag', ['name' => $product->name]));
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate(['item_id' => ['required', 'integer'], 'quantity' => ['required', 'integer', 'min:0', 'max:20']]);
        $this->cart->update($data['item_id'], $data['quantity']);

        return $this->respond(__('Bag updated'));
    }

    public function remove(Request $request): JsonResponse
    {
        $this->cart->remove((int) $request->validate(['item_id' => ['required', 'integer']])['item_id']);

        return $this->respond(__('Item removed'));
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $code = $request->validate(['code' => ['required', 'string', 'max:40']])['code'];
        try {
            $coupon = $this->cart->applyCoupon($code);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return $this->respond(__('Coupon :code applied — :label', ['code' => $coupon->code, 'label' => $coupon->label]));
    }

    public function removeCoupon(): JsonResponse
    {
        $this->cart->removeCoupon();

        return $this->respond(__('Coupon removed'));
    }

    protected function respond(string $message): JsonResponse
    {
        $cart = $this->cart->current();

        return response()->json([
            'message' => $message,
            'cart' => array_merge($this->cart->summary(), [
                'items' => $cart->items->map(fn ($i) => [
                    'id' => $i->id,
                    'name' => $i->product->name,
                    'url' => $i->product->url,
                    'image' => $i->image_url,
                    'variant' => $i->variant?->label,
                    'quantity' => $i->quantity,
                    'max' => min(20, $i->available_stock),
                    'price' => money($i->price),
                    'total' => money($i->line_total),
                ])->values(),
            ]),
        ]);
    }
}
