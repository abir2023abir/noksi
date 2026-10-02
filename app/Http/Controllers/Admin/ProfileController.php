<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request, ImageService $images)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if ($request->hasFile('avatar')) {
            $images->delete($user->avatar);
            $data['avatar'] = $images->store($request->file('avatar'), 'avatars', 400);
        }

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        unset($data['password'], $data['current_password'], $data['password_confirmation']);
        $user->fill($data)->save();

        return back()->with('success', __('Profile updated.'));
    }
}
