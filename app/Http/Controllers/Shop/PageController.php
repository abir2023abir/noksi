<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->is_active, 404);

        return view('shop.page', compact('page'));
    }

    public function about()
    {
        $page = Page::active()->where('slug', 'our-story')->firstOrFail();

        return view('shop.about', ['page' => $page, 'testimonials' => Testimonial::active()->take(3)->get()]);
    }

    public function faq()
    {
        return view('shop.faq', ['faqs' => Faq::active()->get()->groupBy('category')]);
    }

    public function contact()
    {
        return view('shop.contact');
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            'website' => ['prohibited'], // honeypot
        ]);

        ContactMessage::create($data);

        return back()->with('success', __('Thanks — we usually reply within a few hours.'));
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:150'], 'name' => ['nullable', 'string', 'max:100']]);

        $sub = NewsletterSubscriber::firstOrCreate(['email' => strtolower($data['email'])], [
            'name' => $data['name'] ?? null,
            'source' => $request->get('source', 'footer'),
        ]);

        $message = $sub->wasRecentlyCreated
            ? __('You are in. Watch your inbox for something good.')
            : __('You are already on the list — thank you!');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}
