<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create', [
            'permissions' => Permission::where('guard_name', 'web')->orderBy('name')->get(),
            'known' => RoleSeeder::PERMISSIONS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name', 'not_in:customer'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', __('Role created.'));
    }

    public function edit(Role $role)
    {
        abort_if($role->name === 'customer', 404);

        return view('admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::where('guard_name', 'web')->orderBy('name')->get(),
            'known' => RoleSeeder::PERMISSIONS,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'customer', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'not_in:customer', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if ($role->name === 'super_admin') {
            // Keep name stable for the top role; still allow permission sync.
        } else {
            $role->update(['name' => $data['name']]);
        }

        $role->syncPermissions($data['permissions'] ?? []);

        return back()->with('success', __('Role updated.'));
    }

    public function destroy(Role $role)
    {
        abort_if(in_array($role->name, ['super_admin', 'admin', 'manager', 'editor', 'customer'], true), 403, 'Cannot delete system roles.');
        abort_if($role->users()->exists(), 403, 'Role has assigned users.');

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted.'));
    }
}
