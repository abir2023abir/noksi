<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = ContactMessage::query()
            ->when($request->status === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($request->status === 'read', fn ($q) => $q->where('is_read', true))
            ->when($request->status === 'replied', fn ($q) => $q->where('is_replied', true))
            ->when($request->q, function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('subject', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function reply(Request $request, ContactMessage $message)
    {
        $data = $request->validate([
            'reply' => ['required', 'string', 'max:5000'],
        ]);

        $payload = ['is_replied' => true, 'is_read' => true];

        if (Schema::hasColumn('contact_messages', 'reply')) {
            $payload['reply'] = $data['reply'];
        } elseif (Schema::hasColumn('contact_messages', 'admin_reply')) {
            $payload['admin_reply'] = $data['reply'];
        }

        $message->update($payload);

        try {
            Mail::raw($data['reply'], function ($mail) use ($message) {
                $mail->to($message->email, $message->name)
                    ->subject('Re: '.($message->subject ?: 'Your message to NOKSHI'));
            });
        } catch (\Throwable) {
            // Reply is persisted even if mail transport fails.
        }

        return back()->with('success', __('Reply sent.'));
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', __('Message deleted.'));
    }
}
