<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Services\ImageService;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->latest()->paginate(20);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $images->store($request->file('avatar'), 'testimonials', 400);
        }

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', __('Testimonial created.'));
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            $images->delete($testimonial->avatar);
            $data['avatar'] = $images->store($request->file('avatar'), 'testimonials', 400);
        }

        $testimonial->update($data);

        return back()->with('success', __('Testimonial updated.'));
    }

    public function destroy(Testimonial $testimonial, ImageService $images)
    {
        $images->delete($testimonial->avatar);
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', __('Testimonial deleted.'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'max:100'],
            'body' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
