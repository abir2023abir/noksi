<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — NOKSHI Admin</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @stack('head')
</head>
<body class="min-h-screen" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-plum-950 text-paper transition-transform duration-300 lg:static lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-marigold-400 font-display text-lg text-plum-950">ন</span>
                <div>
                    <p class="font-display text-lg leading-none tracking-wide">NOKSHI</p>
                    <p class="text-[10px] uppercase tracking-widest text-paper/40">Admin</p>
                </div>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 pb-6">
                @php
                    $can = fn ($p) => auth()->user()->can($p) || auth()->user()->hasRole('super_admin');
                    $nav = [
                        ['Dashboard', 'admin.dashboard', 'chart', null],
                        ['Catalog', null, null, [
                            ['Products', 'admin.products.index', 'shirt', 'products.view'],
                            ['Categories', 'admin.categories.index', 'layers', 'categories.manage'],
                            ['Brands', 'admin.brands.index', 'tag', 'brands.manage'],
                            ['Attributes', 'admin.attributes.index', 'sliders', 'attributes.manage'],
                            ['Collections', 'admin.collections.index', 'grid', 'collections.manage'],
                            ['Inventory', 'admin.inventory.index', 'box', 'inventory.manage'],
                        ]],
                        ['Sales', null, null, [
                            ['Orders', 'admin.orders.index', 'package', 'orders.view'],
                            ['Customers', 'admin.customers.index', 'users', 'customers.view'],
                            ['Coupons', 'admin.coupons.index', 'percent', 'coupons.manage'],
                            ['Flash sales', 'admin.flash-sales.index', 'zap', 'flash-sales.manage'],
                            ['Reviews', 'admin.reviews.index', 'star', 'reviews.manage'],
                        ]],
                        ['Content', null, null, [
                            ['Banners', 'admin.banners.index', 'image', 'banners.manage'],
                            ['Testimonials', 'admin.testimonials.index', 'message', 'testimonials.manage'],
                            ['Blog', 'admin.posts.index', 'file-text', 'blog.manage'],
                            ['Pages', 'admin.pages.index', 'file-text', 'pages.manage'],
                            ['FAQs', 'admin.faqs.index', 'help', 'faqs.manage'],
                        ]],
                        ['Ops', null, null, [
                            ['Shipping', 'admin.shipping.index', 'truck', 'shipping.manage'],
                            ['Newsletter', 'admin.newsletter.index', 'mail', 'newsletter.manage'],
                            ['Messages', 'admin.messages.index', 'inbox', 'messages.manage'],
                            ['Reports', 'admin.reports.index', 'trending-up', 'reports.view'],
                        ]],
                        ['System', null, null, [
                            ['Settings', 'admin.settings.index', 'settings', 'settings.manage'],
                            ['Staff', 'admin.staff.index', 'users', 'staff.manage'],
                            ['Roles', 'admin.roles.index', 'key', 'staff.manage'],
                            ['Activity', 'admin.logs.index', 'activity', 'logs.view'],
                        ]],
                    ];
                @endphp
                @foreach($nav as $block)
                    @if($block[3] === null)
                        @if(! $block[1] || ($can($block[3] ?? 'x') || true))
                            <a href="{{ route($block[1]) }}" class="nav-item mt-2 {{ request()->routeIs($block[1]) ? 'is-active' : '' }}">
                                <x-icon :name="$block[2]" class="h-4.5 w-4.5" /> {{ $block[0] }}
                            </a>
                        @endif
                    @else
                        @php
                            $visible = collect($block[3])->filter(fn ($i) => ! $i[3] || $can($i[3]));
                        @endphp
                        @if($visible->isNotEmpty())
                            <p class="nav-group">{{ $block[0] }}</p>
                            @foreach($visible as [$label, $route, $icon, $perm])
                                <a href="{{ route($route) }}" class="nav-item {{ request()->routeIs(str_replace('.index', '.*', $route)) || request()->routeIs($route) ? 'is-active' : '' }}">
                                    <x-icon :name="$icon" class="h-4.5 w-4.5" /> {{ $label }}
                                </a>
                            @endforeach
                        @endif
                    @endif
                @endforeach
            </nav>
            <div class="border-t border-white/10 p-4">
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-white/5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-paper/10 text-sm font-bold">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-paper/40">{{ auth()->user()->getRoleNames()->first() }}</p>
                    </div>
                </a>
            </div>
        </aside>

        <div class="fixed inset-0 z-30 bg-plum-950/50 lg:hidden" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

        {{-- Main --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-ink/5 bg-white/80 px-4 backdrop-blur md:px-6">
                <button type="button" class="icon-btn lg:hidden" @click="sidebarOpen = true"><x-icon name="menu" class="h-5 w-5" /></button>
                <div class="flex-1" x-data="{ q: '', open: false, results: { products: [], orders: [], customers: [] } }">
                    <div class="relative max-w-md">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-300" />
                        <input type="search" x-model="q" @input.debounce.300ms="
                            if (q.length < 2) { results = {products:[],orders:[],customers:[]}; open=false; return }
                            fetch('{{ route('admin.search') }}?q='+encodeURIComponent(q), {headers:{Accept:'application/json'}})
                              .then(r=>r.json()).then(d=>{ results=d; open=true })
                        " @focus="open = q.length >= 2" @click.outside="open=false"
                               class="adm-input !rounded-full !pl-10 !h-10" placeholder="Search products, orders, customers…">
                        <div x-show="open" x-cloak class="absolute left-0 right-0 top-full mt-2 max-h-96 overflow-y-auto rounded-2xl bg-white p-2 shadow-lift ring-1 ring-ink/5">
                            <template x-for="p in results.products" :key="'p'+p.id">
                                <a :href="`{{ url('admin/products') }}/${p.id}`" class="block rounded-xl px-3 py-2 text-sm hover:bg-paper" x-text="p.name"></a>
                            </template>
                            <template x-for="o in results.orders" :key="'o'+o.id">
                                <a :href="`{{ url('admin/orders') }}/${o.id}`" class="block rounded-xl px-3 py-2 text-sm hover:bg-paper" x-text="o.order_number + ' — ' + o.customer_name"></a>
                            </template>
                            <template x-for="c in results.customers" :key="'c'+c.id">
                                <a :href="`{{ url('admin/customers') }}/${c.id}`" class="block rounded-xl px-3 py-2 text-sm hover:bg-paper" x-text="c.name"></a>
                            </template>
                            <p class="px-3 py-2 text-xs text-ink-300" x-show="!results.products.length && !results.orders.length && !results.customers.length">No results</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-secondary btn-sm hidden sm:inline-flex"><x-icon name="external" class="h-4 w-4" /> Storefront</a>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="icon-btn" title="Logout"><x-icon name="logout" class="h-5 w-5" /></button></form>
            </header>

            <main class="flex-1 p-4 md:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-5 flex items-start gap-3 rounded-2xl bg-teal-50 px-4 py-3 text-sm text-teal-800 ring-1 ring-teal-200" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                        <x-icon name="check-circle" class="h-5 w-5 shrink-0" /> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-5 flex items-start gap-3 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-200">
                        <x-icon name="alert" class="h-5 w-5 shrink-0" /> {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <div class="pointer-events-none fixed bottom-5 right-5 z-50 flex flex-col gap-2" x-data>
        <template x-for="t in $store.ui.toasts" :key="t.id">
            <div class="toast pointer-events-auto" x-text="t.message"></div>
        </template>
    </div>
    @stack('scripts')
</body>
</html>
