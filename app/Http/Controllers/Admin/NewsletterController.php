<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->q, fn ($q, $term) => $q->where('email', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function export(): StreamedResponse
    {
        $subscribers = NewsletterSubscriber::latest()->get();

        return response()->streamDownload(function () use ($subscribers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Name', 'Active', 'Source', 'Subscribed At']);
            foreach ($subscribers as $s) {
                fputcsv($out, [
                    $s->email,
                    $s->name,
                    $s->is_active ? 'yes' : 'no',
                    $s->source,
                    $s->created_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, 'newsletter-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', __('Subscriber removed.'));
    }
}
