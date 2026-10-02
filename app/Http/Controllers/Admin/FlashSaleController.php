<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::withCount('items')->latest()->paginate(20);

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        return view('admin.flash-sales.create');
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $images->store($request->file('image'), 'flash-sales', 1200);
        }

        $sale = FlashSale::create($data);

        return redirect()->route('admin.flash-sales.edit', $sale)->with('success', __('Flash sale created.'));
    }

    public function show(FlashSale $flash_sale)
    {
        $flash_sale->load('items.product.primaryImage');

        return view('admin.flash-sales.show', ['flashSale' => $flash_sale]);
    }

    public function edit(FlashSale $flash_sale)
    {
        $flash_sale->load('items.product.primaryImage');

        return view('admin.flash-sales.edit', [
            'flashSale' => $flash_sale,
            'products' => Product::active()->orderBy('name')->get(['id', 'name', 'price']),
        ]);
    }

    public function update(Request $request, FlashSale $flash_sale, ImageService $images)
    {
        $data = $this->validated($request, $flash_sale);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $images->delete($flash_sale->image);
            $data['image'] = $images->store($request->file('image'), 'flash-sales', 1200);
        }

        $flash_sale->update($data);
        Product::forgetFlashSaleCache();

        return back()->with('success', __('Flash sale updated.'));
    }

    public function destroy(FlashSale $flash_sale, ImageService $images)
    {
        $images->delete($flash_sale->image);
        $flash_sale->delete();
        Product::forgetFlashSaleCache();

        return redirect()->route('admin.flash-sales.index')->with('success', __('Flash sale deleted.'));
    }

    public function addProduct(Request $request, FlashSale $flash_sale)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id', Rule::unique('flash_sale_products')->where('flash_sale_id', $flash_sale->id)],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'quantity_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $flash_sale->items()->create($data);
        Product::forgetFlashSaleCache();

        return back()->with('success', __('Product added to flash sale.'));
    }

    public function removeProduct(FlashSale $flash_sale, FlashSaleProduct $item)
    {
        abort_unless($item->flash_sale_id === $flash_sale->id, 404);
        $item->delete();
        Product::forgetFlashSaleCache();

        return back()->with('success', __('Product removed.'));
    }

    protected function validated(Request $request, ?FlashSale $flashSale = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('flash_sales', 'slug')->ignore($flashSale?->id)],
            'color' => ['nullable', 'string', 'max:20'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
