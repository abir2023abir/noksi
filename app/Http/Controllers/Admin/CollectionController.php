<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::withCount('products')->orderBy('sort_order')->paginate(20);

        return view('admin.collections.index', compact('collections'));
    }

    public function create()
    {
        return view('admin.collections.create', [
            'products' => Product::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $images->store($request->file('image'), 'collections', 1200);
        }

        $collection = Collection::create($data);
        $collection->products()->sync($this->syncMap($request->input('product_ids', [])));

        return redirect()->route('admin.collections.index')->with('success', __('Collection created.'));
    }

    public function edit(Collection $collection)
    {
        $collection->load('products');

        return view('admin.collections.edit', [
            'collection' => $collection,
            'products' => Product::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Collection $collection, ImageService $images)
    {
        $data = $this->validated($request, $collection);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $images->delete($collection->image);
            $data['image'] = $images->store($request->file('image'), 'collections', 1200);
        }

        $collection->update($data);
        $collection->products()->sync($this->syncMap($request->input('product_ids', [])));

        return back()->with('success', __('Collection updated.'));
    }

    public function destroy(Collection $collection, ImageService $images)
    {
        $images->delete($collection->image);
        $collection->delete();

        return redirect()->route('admin.collections.index')->with('success', __('Collection deleted.'));
    }

    protected function validated(Request $request, ?Collection $collection = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('collections', 'slug')->ignore($collection?->id)],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);
    }

    protected function syncMap(array $ids): array
    {
        $map = [];
        foreach (array_values($ids) as $i => $id) {
            $map[$id] = ['sort_order' => $i];
        }

        return $map;
    }
}
