<div x-data="quickView()" @quick-view.window="show($event.detail)" x-show="open" x-cloak class="fixed inset-0 z-[65] flex items-end sm:items-center justify-center p-0 sm:p-6" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity.duration.300ms class="absolute inset-0 bg-plum-950/70 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open"
         x-transition:enter="transition ease-out-expo duration-500" x-transition:enter-start="opacity-0 translate-y-10 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition duration-200" x-transition:leave-end="opacity-0 translate-y-6 sm:scale-95"
         class="relative w-full max-w-4xl max-h-[92vh] overflow-y-auto rounded-t-4xl sm:rounded-4xl bg-paper shadow-lift"
         @keydown.escape.window="open = false" x-trap.noscroll="open">
        <button class="icon-btn absolute right-3 top-3 z-10 bg-white/80 backdrop-blur" @click="open = false" aria-label="{{ __('Close') }}"><x-icon name="x" class="h-5 w-5" /></button>
        <div x-html="html" x-effect="if (html) $nextTick(() => window.nkRefreshMotion?.())"></div>
    </div>
</div>
