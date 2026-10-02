<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::role('customer')
            ->withCount('orders')
            ->when($request->q, function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        abort_unless($customer->hasRole('customer'), 404);

        $customer->load(['addresses', 'orders' => fn ($q) => $q->latest()->take(20), 'wishlists.product']);

        return view('admin.customers.show', compact('customer'));
    }

    public function toggle(User $customer)
    {
        abort_unless($customer->hasRole('customer'), 404);

        $customer->update(['is_active' => ! $customer->is_active]);

        return back()->with('success', __('Customer status updated.'));
    }
}
