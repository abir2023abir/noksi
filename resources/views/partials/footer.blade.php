<footer class="relative mt-24 bg-plum-950 text-paper overflow-hidden">
    <div class="absolute inset-0 grain pointer-events-none"></div>

    {{-- Newsletter band --}}
    <div class="relative border-b border-white/10">
        <div class="container py-14 md:py-20 grid gap-10 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="eyebrow text-marigold-400 mb-4">{{ __('The Nokshi letter') }}</p>
                <h2 class="font-display text-display-md text-balance">{{ __('New drops, weaver stories and the occasional secret sale.') }}</h2>
            </div>
            <form action="{{ route('newsletter') }}" method="POST" class="flex flex-col sm:flex-row gap-3 lg:justify-end" x-data="{ email: '', sending: false, done: false }"
                  @submit.prevent="sending = true; nkRequest('{{ route('newsletter') }}', { body: { email, source: 'footer' } }).then(r => { done = true; $store.ui.toast(r.message) }).catch(e => $store.ui.toast(e.message, 'error')).finally(() => sending = false)">
                @csrf
                <input type="email" name="email" x-model="email" required placeholder="{{ __('your@email.com') }}"
                       class="h-14 flex-1 min-w-0 sm:min-w-[320px] rounded-full border border-white/15 bg-white/5 px-6 text-paper placeholder:text-paper/40 focus:border-marigold-400 focus:outline-none focus:ring-4 focus:ring-marigold-400/20">
                <button class="btn btn-accent btn-lg" :disabled="sending || done" x-text="done ? '{{ __('Subscribed') }}' : '{{ __('Subscribe') }}'">{{ __('Subscribe') }}</button>
            </form>
        </div>
    </div>

    {{-- Links --}}
    <div class="relative container py-14 grid gap-10 md:grid-cols-2 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-marigold-400 text-plum-950 font-bangla text-xl font-bold">ন</span>
                <span class="font-display text-[26px] font-semibold tracking-tight">NOKSHI</span>
            </a>
            <p class="mt-5 max-w-sm text-paper/70 leading-relaxed">{{ setting('site_description') }}</p>
            <div class="mt-6 flex gap-2">
                @foreach(['facebook', 'instagram', 'youtube'] as $s)
                    @if(setting("social_$s"))
                        <a href="{{ setting("social_$s") }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 text-paper/80 transition hover:bg-marigold-400 hover:text-plum-950 hover:border-marigold-400" aria-label="{{ ucfirst($s) }}">
                            <x-icon :name="$s" class="h-4.5 w-4.5" />
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2">
            <p class="eyebrow text-paper/50 mb-4">{{ __('Shop') }}</p>
            <ul class="space-y-2.5 text-[15px] text-paper/80">
                @foreach($menuCategories as $cat)
                    <li><a href="{{ $cat->url }}" class="link-underline hover:text-paper">{{ $cat->name }}</a></li>
                @endforeach
                <li><a href="{{ route('shop.new') }}" class="link-underline hover:text-paper">{{ __('New arrivals') }}</a></li>
                <li><a href="{{ route('shop.sale') }}" class="link-underline hover:text-paper">{{ __('Sale') }}</a></li>
            </ul>
        </div>

        <div class="lg:col-span-2">
            <p class="eyebrow text-paper/50 mb-4">{{ __('Help') }}</p>
            <ul class="space-y-2.5 text-[15px] text-paper/80">
                <li><a href="{{ route('order.track.index') }}" class="link-underline hover:text-paper">{{ __('Track order') }}</a></li>
                <li><a href="{{ route('faq') }}" class="link-underline hover:text-paper">{{ __('FAQ') }}</a></li>
                <li><a href="{{ route('contact') }}" class="link-underline hover:text-paper">{{ __('Contact') }}</a></li>
                @foreach($footerPages->whereIn('slug', ['shipping', 'returns', 'size-guide']) as $p)
                    <li><a href="{{ route('page.show', $p->slug) }}" class="link-underline hover:text-paper">{{ $p->title }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="lg:col-span-2">
            <p class="eyebrow text-paper/50 mb-4">{{ __('Company') }}</p>
            <ul class="space-y-2.5 text-[15px] text-paper/80">
                <li><a href="{{ route('about') }}" class="link-underline hover:text-paper">{{ __('Our story') }}</a></li>
                <li><a href="{{ route('blog.index') }}" class="link-underline hover:text-paper">{{ __('Journal') }}</a></li>
                @foreach($footerPages->whereIn('slug', ['privacy-policy', 'terms']) as $p)
                    <li><a href="{{ route('page.show', $p->slug) }}" class="link-underline hover:text-paper">{{ $p->title }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="lg:col-span-2">
            <p class="eyebrow text-paper/50 mb-4">{{ __('Visit') }}</p>
            <address class="not-italic space-y-2.5 text-[15px] text-paper/80 leading-relaxed">
                <p>{{ setting('contact_address') }}</p>
                <p><a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone')) }}" class="hover:text-paper">{{ setting('contact_phone') }}</a></p>
                <p><a href="mailto:{{ setting('contact_email') }}" class="hover:text-paper">{{ setting('contact_email') }}</a></p>
                <p class="text-paper/50 text-sm">{{ setting('business_hours') }}</p>
            </address>
        </div>
    </div>

    {{-- Bottom --}}
    <div class="relative border-t border-white/10">
        <div class="container py-6 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-paper/50">
            <p>© {{ date('Y') }} {{ setting('site_name') }}. {{ __('Made with care in Dhaka.') }}</p>
            <div class="flex items-center gap-2">
                <span class="mr-1">{{ __('We accept') }}</span>
                @foreach(['bKash' => '#E2136E', 'Nagad' => '#F6921E', 'Rocket' => '#8C3494', 'VISA' => '#1A1F71', 'COD' => '#0F766E'] as $name => $color)
                    <span class="rounded-md px-2 py-1 text-[10px] font-extrabold tracking-wider text-white" style="background: {{ $color }}">{{ $name }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Giant wordmark --}}
    <div class="relative select-none overflow-hidden pointer-events-none" aria-hidden="true">
        <p class="font-display text-[22vw] leading-[0.75] font-bold text-white/[0.04] text-center -mb-[5vw] tracking-tighter">NOKSHI</p>
    </div>
</footer>
