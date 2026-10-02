<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        return view('auth.register', ['redirect' => $request->get('redirect')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], ['phone.regex' => __('Enter a valid Bangladeshi mobile number.')]);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'],
            'password' => $data['password'],
        ]);
        $user->assignRole('customer');

        Auth::login($user);
        $request->session()->regenerate();
        cart()->current();

        $redirect = $request->input('redirect');
        if ($redirect && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            return redirect($redirect)->with('success', __('Welcome to NOKSHI, :name!', ['name' => $user->name]));
        }

        return redirect()->route('account.dashboard')->with('success', __('Welcome to NOKSHI, :name!', ['name' => $user->name]));
    }
}
