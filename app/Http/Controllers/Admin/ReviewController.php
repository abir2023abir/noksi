<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['product', 'user'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('body', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function status(Request $request, Review $review)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $review->update($data);

        return back()->with('success', __('Review status updated.'));
    }

    public function reply(Request $request, Review $review)
    {
        $data = $request->validate([
            'admin_reply' => ['required', 'string', 'max:2000'],
        ]);

        $review->update($data);

        return back()->with('success', __('Reply saved.'));
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', __('Review deleted.'));
    }
}
