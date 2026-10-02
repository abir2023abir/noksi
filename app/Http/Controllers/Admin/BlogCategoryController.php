<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogCategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:blog_categories,slug'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        BlogCategory::create($data);

        return back()->with('success', __('Blog category created.'));
    }

    public function update(Request $request, BlogCategory $blog_category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('blog_categories', 'slug')->ignore($blog_category->id)],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $blog_category->update($data);

        return back()->with('success', __('Blog category updated.'));
    }

    public function destroy(BlogCategory $blog_category)
    {
        $blog_category->delete();

        return back()->with('success', __('Blog category deleted.'));
    }
}
