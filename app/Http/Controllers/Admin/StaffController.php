<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::role(['super_admin', 'admin', 'manager', 'editor'])
            ->with('roles')
            ->latest()
            ->paginate(20);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create', [
            'roles' => Role::whereIn('name', ['super_admin', 'admin', 'manager', 'editor'])->get(),
        ]);
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:super_admin,admin,manager,editor'],
            'is_active' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $images->store($request->file('avatar'), 'avatars', 400);
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $role = $data['role'];
        unset($data['role']);

        $user = User::create($data);
        $user->syncRoles([$role]);

        return redirect()->route('admin.staff.index')->with('success', __('Staff member created.'));
    }

    public function edit(User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        return view('admin.staff.edit', [
            'staff' => $staff,
            'roles' => Role::whereIn('name', ['super_admin', 'admin', 'manager', 'editor'])->get(),
        ]);
    }

    public function update(Request $request, User $staff, ImageService $images)
    {
        abort_unless($staff->isStaff(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($staff->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:super_admin,admin,manager,editor'],
            'is_active' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $images->delete($staff->avatar);
            $data['avatar'] = $images->store($request->file('avatar'), 'avatars', 400);
        }

        $data['is_active'] = $request->boolean('is_active');
        $role = $data['role'];
        unset($data['role']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $staff->update($data);
        $staff->syncRoles([$role]);

        return back()->with('success', __('Staff member updated.'));
    }

    public function destroy(User $staff)
    {
        abort_unless($staff->isStaff(), 404);
        abort_if($staff->id === auth()->id(), 403, 'You cannot delete your own account.');
        abort_if($staff->hasRole('super_admin') && User::role('super_admin')->count() <= 1, 403, 'Cannot delete the last super admin.');

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', __('Staff member deleted.'));
    }
}
