@extends('admin.layouts.app')
@section('title', 'Settings')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Settings</h1>
        <p class="mt-1 text-sm text-ink-500">Store configuration</p>
    </div>
    <form method="POST" action="{{ route('admin.settings.cache') }}">@csrf<button class="btn btn-secondary">Clear cache</button></form>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @forelse($settings as $group => $items)
        <div class="adm-card">
            <div class="adm-card-head"><h2 class="adm-card-title capitalize">{{ str_replace('_', ' ', $group ?: 'general') }}</h2></div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                @foreach($items as $setting)
                    <div class="{{ in_array($setting->type, ['textarea', 'json']) ? 'sm:col-span-2' : '' }}">
                        <label class="adm-label">{{ str_replace('_', ' ', $setting->key) }}</label>
                        <input type="hidden" name="groups[{{ $setting->key }}]" value="{{ $setting->group }}">
                        <input type="hidden" name="types[{{ $setting->key }}]" value="{{ $setting->type }}">
                        @if($setting->type === 'boolean' || $setting->type === 'bool')
                            <label class="adm-checkbox mt-2">
                                <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" @checked($setting->value == '1' || $setting->value === true || $setting->value === 'true')>
                                Enabled
                            </label>
                        @elseif($setting->type === 'image' || $setting->type === 'file')
                            @if($setting->value)<img src="{{ img($setting->value) }}" class="mb-2 h-16 w-auto rounded-lg object-contain bg-paper-100">@endif
                            <input type="file" name="files[{{ $setting->key }}]" accept="image/*" class="adm-input">
                            <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                        @elseif($setting->type === 'textarea' || $setting->type === 'json')
                            <textarea name="settings[{{ $setting->key }}]" rows="3" class="adm-input">{{ $setting->value }}</textarea>
                        @else
                            <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="adm-input">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="adm-card py-12 text-center text-ink-300">No settings found. Seed the database first.</div>
    @endforelse
    @if($settings->isNotEmpty())
        <div class="flex justify-end">
            <button class="btn btn-primary btn-lg">Save settings</button>
        </div>
    @endif
</form>
@endsection
