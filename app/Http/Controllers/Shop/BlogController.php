<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()->with('category', 'author');
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$request->q}%")->orWhere('excerpt', 'like', "%{$request->q}%"));
        }

        return view('shop.blog.index', [
            'featured' => ! $request->filled('category') && ! $request->filled('q') ? Post::published()->where('is_featured', true)->first() : null,
            'posts' => $query->paginate(9)->withQueryString(),
            'categories' => BlogCategory::withCount(['posts' => fn ($q) => $q->published()])->get(),
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->status === 'published' || auth()->user()?->isStaff(), 404);
        $post->increment('views');

        return view('shop.blog.show', [
            'post' => $post->load('category', 'author'),
            'related' => Post::published()->where('id', '!=', $post->id)->where('blog_category_id', $post->blog_category_id)->take(3)->get(),
            'shopPicks' => Product::active()->featured()->with('primaryImage', 'images', 'category')->inRandomOrder()->take(4)->get(),
        ]);
    }
}
