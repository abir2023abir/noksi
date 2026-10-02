@props(['value' => 0, 'size' => 'h-4 w-4'])
@php $v = (float) $value; @endphp
<span class="stars" aria-label="{{ number_format($v, 1) }} out of 5">
    @for($i = 1; $i <= 5; $i++)
        @if($v >= $i)
            <x-icon name="star-fill" :class="$size" />
        @elseif($v >= $i - 0.5)
            <x-icon name="star-half" :class="$size" />
        @else
            <x-icon name="star" :class="$size.' text-ink-300/50'" />
        @endif
    @endfor
</span>
