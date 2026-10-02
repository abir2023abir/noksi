<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->latest()->paginate(20);

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['image'] = $images->store($request->file('image'), 'banners', 1920);

        if ($request->hasFile('mobile_image')) {
            $data['mobile_image'] = $images->store($request->file('mobile_image'), 'banners', 900);
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', __('Banner created.'));
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner, ImageService $images)
    {
        $data = $this->validated($request, false);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $images->delete($banner->image);
            $data['image'] = $images->store($request->file('image'), 'banners', 1920);
        }
        if ($request->hasFile('mobile_image')) {
            $images->delete($banner->mobile_image);
            $data['mobile_image'] = $images->store($request->file('mobile_image'), 'banners', 900);
        }

        $banner->update($data);

        return back()->with('success', __('Banner updated.'));
    }

    public function destroy(Banner $banner, ImageService $images)
    {
        $images->delete($banner->image);
        $images->delete($banner->mobile_image);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', __('Banner deleted.'));
    }

    protected function validated(Request $request, bool $requireImage = true): array
    {
        return $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'max:5120'],
            'mobile_image' => ['nullable', 'image', 'max:4096'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:50'],
            'bg_color' => ['nullable', 'string', 'max:20'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
    }
}
