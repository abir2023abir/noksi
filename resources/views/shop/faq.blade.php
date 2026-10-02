<x-layouts.app :title="__('FAQ')">
    <section class="relative overflow-hidden bg-teal-900 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-12 md:py-16">
            <x-breadcrumb :items="[__('FAQ') => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
            <h1 class="mt-4 font-display text-display-sm md:text-display-md" data-split>{{ __('Questions, answered') }}</h1>
            <p class="mt-3 text-paper/70 max-w-xl">{{ __('Shipping, sizing, returns — the things people ask us most.') }}</p>
        </div>
    </section>

    <section class="container py-12 md:py-16" x-data="{ q: '', open: null }">
        <div class="mx-auto max-w-2xl mb-10">
            <input type="search" x-model="q" class="input" placeholder="{{ __('Search FAQs…') }}">
        </div>

        <div class="mx-auto max-w-2xl space-y-10">
            @forelse($faqs as $category => $items)
                <div x-show="!q || {{ Js::from($items->pluck('question')->implode(' ').' '.$items->pluck('answer')->implode(' ')) }}.toLowerCase().includes(q.toLowerCase())">
                    <h2 class="font-display text-2xl mb-4">{{ $category ?: __('General') }}</h2>
                    <div class="divide-y divide-ink/10 border-y border-ink/10 rounded-none">
                        @foreach($items as $faq)
                            <div x-show="!q || @js(strtolower($faq->question.' '.$faq->answer)).includes(q.toLowerCase())" x-data>
                                <button type="button" class="flex w-full items-center justify-between gap-4 py-5 text-left font-semibold" @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}">
                                    <span>{{ $faq->question }}</span>
                                    <x-icon name="plus" class="h-5 w-5 shrink-0 transition" ::class="{ 'rotate-45': open === {{ $faq->id }} }" />
                                </button>
                                <div x-show="open === {{ $faq->id }}" x-collapse x-cloak>
                                    <div class="pb-5 text-ink-500 leading-relaxed prose-nokshi">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-center text-ink-500">{{ __('No FAQs yet.') }}</p>
            @endforelse
        </div>

        <div class="mx-auto max-w-2xl mt-14 rounded-3xl bg-plum-950 text-paper p-8 text-center">
            <h3 class="font-display text-2xl">{{ __('Still stuck?') }}</h3>
            <p class="mt-2 text-paper/70">{{ __('Message us — we usually reply within a few hours.') }}</p>
            <a href="{{ route('contact') }}" class="btn btn-white btn-md mt-6">{{ __('Contact us') }}</a>
        </div>
    </section>
</x-layouts.app>
