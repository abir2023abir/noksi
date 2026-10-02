<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['variants', 'primaryImage', 'category'])
            ->where('track_stock', true)
            ->when($request->q, fn ($q, $term) => $q->search($term))
            ->when($request->stock === 'low', fn ($q) => $q->where('stock', '<=', 5)->where('stock', '>', 0))
            ->when($request->stock === 'out', fn ($q) => $q->where('stock', '<=', 0))
            ->orderBy('stock')
            ->paginate(30)
            ->withQueryString();

        return view('admin.inventory.index', compact('products'));
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'type' => ['required', 'in:adjustment,restock,damage,return,sale'],
            'note' => ['nullable', 'string', 'max:500'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $qty = (int) $data['quantity'];

            if (! empty($data['product_variant_id'])) {
                $variant = ProductVariant::lockForUpdate()
                    ->where('product_id', $product->id)
                    ->where('id', $data['product_variant_id'])
                    ->firstOrFail();
                $variant->update(['stock' => max(0, $variant->stock + $qty)]);
                $product->syncStockFromVariants();
            } else {
                $product->update(['stock' => max(0, $product->stock + $qty)]);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'user_id' => auth()->id(),
                'quantity' => $qty,
                'type' => $data['type'],
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
            ]);
        });

        return back()->with('success', __('Stock adjusted.'));
    }

    public function movements(Request $request)
    {
        $movements = StockMovement::with(['product', 'variant', 'user'])
            ->when($request->product_id, fn ($q, $id) => $q->where('product_id', $id))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.inventory.movements', compact('movements'));
    }
}
