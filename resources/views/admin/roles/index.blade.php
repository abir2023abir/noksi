@extends('admin.layouts.app')
@section('title', 'Roles')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Roles</h1><p class="mt-1 text-sm text-ink-500">{{ $roles->count() }} roles</p></div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add role</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Name</th><th>Users</th><th>Permissions</th><th></th></tr></thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td class="font-semibold capitalize">{{ $role->name }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-ghost btn-sm">Edit</a>
                        @unless(in_array($role->name, ['super_admin','admin','manager','editor','customer'], true))
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('Delete role?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-10 text-center text-ink-300">No roles.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
