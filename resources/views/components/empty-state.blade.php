@props(['icon' => 'bag', 'title', 'text' => null, 'action' => null, 'actionText' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-20']) }}>
    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-plum-100 text-plum-700 mb-5"><x-icon :name="$icon" class="h-9 w-9" /></div>
    <p class="font-display text-2xl md:text-3xl">{{ $title }}</p>
    @if($text)<p class="mt-2 text-ink-500 max-w-sm">{{ $text }}</p>@endif
    @if($action)<a href="{{ $action }}" class="btn btn-primary btn-md mt-6">{{ $actionText ?? __('Continue shopping') }}</a>@endif
    {{ $slot }}
</div>
