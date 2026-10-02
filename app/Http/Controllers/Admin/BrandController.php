<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->latest()->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('logo')) {
            $data['logo'] = $images->store($request->file('logo'), 'brands', 600);
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', __('Brand created.'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand, ImageService $images)
    {
        $data = $this->validated($request, $brand);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('logo')) {
            $images->delete($brand->logo);
            $data['logo'] = $images->store($request->file('logo'), 'brands', 600);
        }

        $brand->update($data);

        return back()->with('success', __('Brand updated.'));
    }

    public function destroy(Brand $brand, ImageService $images)
    {
        $images->delete($brand->logo);
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', __('Brand deleted.'));
    }

    protected function validated(Request $request, ?Brand $brand = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('brands', 'slug')->ignore($brand?->id)],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
