<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(30);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create', [
            'parents' => Category::root()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['show_in_menu'] = $request->boolean('show_in_menu', true);

        if ($request->hasFile('image')) {
            $data['image'] = $images->store($request->file('image'), 'categories', 800);
        }
        if ($request->hasFile('banner')) {
            $data['banner'] = $images->store($request->file('banner'), 'categories', 1600);
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', __('Category created.'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => Category::root()->where('id', '!=', $category->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category, ImageService $images)
    {
        $data = $this->validated($request, $category);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['show_in_menu'] = $request->boolean('show_in_menu');

        if ($request->hasFile('image')) {
            $images->delete($category->image);
            $data['image'] = $images->store($request->file('image'), 'categories', 800);
        }
        if ($request->hasFile('banner')) {
            $images->delete($category->banner);
            $data['banner'] = $images->store($request->file('banner'), 'categories', 1600);
        }

        $category->update($data);

        return back()->with('success', __('Category updated.'));
    }

    public function destroy(Category $category, ImageService $images)
    {
        $images->delete($category->image);
        $images->delete($category->banner);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', __('Category deleted.'));
    }

    public function reorder(Request $request)
    {
        $data = $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);
        foreach ($data['order'] as $sort => $id) {
            Category::where('id', $id)->update(['sort_order' => $sort]);
        }

        return response()->json(['ok' => true]);
    }

    protected function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_bn' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'banner' => ['nullable', 'image', 'max:5120'],
        ]);
    }
}
