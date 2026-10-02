@props(['items' => []])
<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'text-sm text-ink-500']) }}>
    <ol class="flex flex-wrap items-center gap-1.5">
        <li><a href="{{ route('home') }}" class="hover:text-ink">{{ __('Home') }}</a></li>
        @foreach($items as $label => $url)
            <li class="text-ink-300">/</li>
            <li>
                @if($url && !$loop->last)
                    <a href="{{ $url }}" class="hover:text-ink">{{ $label }}</a>
                @else
                    <span class="text-ink font-medium">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
