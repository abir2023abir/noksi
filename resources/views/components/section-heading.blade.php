@props(['eyebrow' => null, 'title', 'subtitle' => null, 'link' => null, 'linkText' => null, 'align' => 'left', 'dark' => false])
<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 md:flex-row md:items-end md:justify-between reveal '.($align === 'center' ? 'text-center md:flex-col md:items-center' : '')]) }}>
    <div class="max-w-2xl">
        @if($eyebrow)
            <p class="eyebrow mb-3 {{ $dark ? 'text-marigold-400' : 'text-plum-700' }}">{{ $eyebrow }}</p>
        @endif
        <h2 class="font-display text-display-sm md:text-display-md text-balance {{ $dark ? 'text-paper' : 'text-ink' }}">{{ $title }}</h2>
        @if($subtitle)
            <p class="mt-3 text-lg {{ $dark ? 'text-paper/70' : 'text-ink-500' }} text-pretty">{{ $subtitle }}</p>
        @endif
    </div>
    @if($link)
        <a href="{{ $link }}" class="group inline-flex items-center gap-2 font-semibold shrink-0 {{ $dark ? 'text-paper' : 'text-ink' }}">
            <span class="link-underline">{{ $linkText ?? __('View all') }}</span>
            <span class="flex h-9 w-9 items-center justify-center rounded-full border transition group-hover:bg-ink group-hover:text-paper {{ $dark ? 'border-paper/30 group-hover:bg-paper group-hover:text-ink' : 'border-ink/15' }}"><x-icon name="arrow-right" class="h-4 w-4" /></span>
        </a>
    @endif
    {{ $slot }}
</div>
