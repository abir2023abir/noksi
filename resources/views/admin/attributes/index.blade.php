@extends('admin.layouts.app')
@section('title', 'Attributes')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Attributes</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $attributes->total() }} attributes</p>
    </div>
    <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add attribute</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Name</th><th>Type</th><th>Values</th><th>Filterable</th><th></th></tr></thead>
        <tbody>
            @forelse($attributes as $attribute)
                <tr>
                    <td class="font-semibold">{{ $attribute->name }}</td>
                    <td class="text-sm capitalize">{{ $attribute->type }}</td>
                    <td class="text-sm">{{ $attribute->values->pluck('value')->take(6)->implode(', ') }}{{ $attribute->values->count() > 6 ? '…' : '' }}</td>
                    <td>{{ $attribute->is_filterable ? 'Yes' : 'No' }}</td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.attributes.destroy', $attribute) }}" class="inline" onsubmit="return confirm('Delete this attribute?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No attributes.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $attributes->links() }}</div>
@endsection
