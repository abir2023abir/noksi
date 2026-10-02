<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['category', 'author'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'categories' => BlogCategory::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.posts.create', [
            'categories' => BlogCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $data['is_featured'] = $request->boolean('is_featured');
        $data['tags'] = $this->parseTags($request->input('tags'));

        if ($request->hasFile('image')) {
            $data['image'] = $images->store($request->file('image'), 'posts', 1400);
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', __('Post created.'));
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => BlogCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post, ImageService $images)
    {
        $data = $this->validated($request, $post);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['tags'] = $this->parseTags($request->input('tags'));

        if ($request->hasFile('image')) {
            $images->delete($post->image);
            $data['image'] = $images->store($request->file('image'), 'posts', 1400);
        }

        $post->update($data);

        return back()->with('success', __('Post updated.'));
    }

    public function destroy(Post $post, ImageService $images)
    {
        $images->delete($post->image);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', __('Post deleted.'));
    }

    protected function validated(Request $request, ?Post $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($post?->id)],
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'tags' => ['nullable'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    protected function parseTags(mixed $tags): ?array
    {
        if (is_array($tags)) {
            return array_values(array_filter(array_map('trim', $tags)));
        }
        if (is_string($tags) && $tags !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        return null;
    }
}
