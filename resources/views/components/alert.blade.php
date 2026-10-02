@props(['type' => 'info'])
@php
$styles = [
    'success' => 'bg-teal-50 text-teal-800 ring-teal-200',
    'error' => 'bg-rose-50 text-rose-800 ring-rose-200',
    'warning' => 'bg-amber-50 text-amber-800 ring-amber-200',
    'info' => 'bg-sky-50 text-sky-800 ring-sky-200',
];
$icons = ['success' => 'check-circle', 'error' => 'alert', 'warning' => 'alert', 'info' => 'info'];
@endphp
<div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-2xl px-4 py-3.5 text-sm ring-1 ring-inset '.$styles[$type]]) }} role="alert">
    <x-icon :name="$icons[$type]" class="h-5 w-5 shrink-0 mt-0.5" />
    <div class="flex-1">{{ $slot }}</div>
</div>
