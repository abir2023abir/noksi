@extends('admin.layouts.app')
@section('title', 'Staff')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Staff</h1><p class="mt-1 text-sm text-ink-500">{{ $staff->total() }} members</p></div>
    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add staff</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($staff as $member)
                <tr>
                    <td class="font-semibold">{{ $member->name }}</td>
                    <td class="text-sm">{{ $member->email }}</td>
                    <td class="text-sm capitalize">{{ $member->roles->pluck('name')->implode(', ') }}</td>
                    <td><span class="pill {{ status_badge($member->is_active ? 'active' : 'inactive') }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-ghost btn-sm">Edit</a>
                        @if($member->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" class="inline" onsubmit="return confirm('Delete staff member?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No staff.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $staff->links() }}</div>
@endsection
