@props(['title' => null, 'metaDescription' => null, 'ogImage' => null, 'bodyClass' => ''])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — '.setting('site_name', 'NOKSHI') : setting('meta_title', setting('site_name', 'NOKSHI')) }}</title>
    <meta name="description" content="{{ $metaDescription ?? setting('meta_description') }}">
    <meta property="og:title" content="{{ $title ?? setting('site_name', 'NOKSHI') }}">
    <meta property="og:description" content="{{ $metaDescription ?? setting('meta_description') }}">
    <meta property="og:image" content="{{ $ogImage ?? img(setting('og_image'), 'images/og-default.jpg') }}">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#1B0E2B">
    <link rel="icon" href="{{ setting('favicon') ? img(setting('favicon')) : asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @php
        $__cartItems = cart()->current()->items->map(fn ($i) => [
            'id' => $i->id, 'name' => $i->product->name, 'url' => $i->product->url, 'image' => $i->image_url,
            'variant' => $i->variant?->label, 'quantity' => $i->quantity, 'max' => min(20, $i->available_stock),
            'price' => money($i->price), 'total' => money($i->line_total),
        ])->values();
        $__cartPayload = cart()->summary() + ['items' => $__cartItems];
        $__wishlist = auth()->check() ? auth()->user()->wishlists()->pluck('product_id') : [];
    @endphp
    <script>
        window.__cart = {!! json_encode($__cartPayload) !!};
        window.__wishlist = {!! json_encode($__wishlist) !!};
    </script>
    @if(setting('google_analytics'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics') }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ setting('google_analytics') }}');</script>
    @endif
</head>
<body class="min-h-screen flex flex-col {{ $bodyClass ?? '' }}" x-data :class="{ 'overflow-hidden': $store.ui.cartOpen || $store.ui.menuOpen || $store.ui.searchOpen }">

    @include('partials.header')

    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.cart-drawer')
    @include('partials.search-overlay')
    @include('partials.mobile-menu')
    @include('partials.toasts')
    @include('partials.quick-view-modal')

    @if(session('success') || session('error') || session('info'))
        <script>
            document.addEventListener('alpine:initialized', () => {
                @if(session('success')) Alpine.store('ui').toast(@json(session('success')), 'success'); @endif
                @if(session('error')) Alpine.store('ui').toast(@json(session('error')), 'error'); @endif
                @if(session('info')) Alpine.store('ui').toast(@json(session('info')), 'info'); @endif
            });
        </script>
    @endif

    @if(setting('contact_whatsapp'))
        <a href="https://wa.me/{{ setting('contact_whatsapp') }}" target="_blank" rel="noopener"
           class="fixed bottom-5 right-5 z-40 hidden md:flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lift transition hover:scale-105"
           aria-label="Chat on WhatsApp">
            <span class="absolute inset-0 rounded-full bg-[#25D366] animate-pulse-ring"></span>
            <x-icon name="whatsapp" class="relative h-7 w-7" />
        </a>
    @endif

    @stack('scripts')
</body>
</html>
