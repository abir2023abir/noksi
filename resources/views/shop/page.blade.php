<x-layouts.app :title="$page->meta_title ?: $page->title" :metaDescription="$page->meta_description">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-12 md:py-16 max-w-3xl">
            <x-breadcrumb :items="[$page->title => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
            <h1 class="mt-4 font-display text-display-sm md:text-display-md" data-split>{{ $page->title }}</h1>
        </div>
    </section>
    <section class="container py-12 md:py-16">
        <div class="mx-auto max-w-2xl prose-nokshi prose-lg">
            {!! $page->body !!}
        </div>
    </section>
</x-layouts.app>
