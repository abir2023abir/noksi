<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shop\CheckoutController;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function index()
    {
        return view('account.addresses', [
            'addresses' => auth()->user()->addresses()->orderByDesc('is_default')->get(),
            'districts' => CheckoutController::DISTRICTS,
        ]);
    }

    public function store(Request $request)
    {
        auth()->user()->addresses()->create($this->validated($request));

        return back()->with('success', __('Address added.'));
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->update($this->validated($request));

        return back()->with('success', __('Address updated.'));
    }

    public function destroy(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->delete();

        return back()->with('success', __('Address removed.'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'email' => ['nullable', 'email'],
            'address_line' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', Rule::in(CheckoutController::DISTRICTS)],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
