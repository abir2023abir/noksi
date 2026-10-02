<x-layouts.app :title="__('Contact')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-12 md:py-16">
            <x-breadcrumb :items="[__('Contact') => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
            <h1 class="mt-4 font-display text-display-sm md:text-display-md" data-split>{{ __('Say hello') }}</h1>
            <p class="mt-3 text-paper/70 max-w-xl">{{ __('Orders, sizing, collaborations — we read every message.') }}</p>
        </div>
    </section>

    <section class="container py-12 md:py-16">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-5 space-y-6">
                <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 space-y-5">
                    <div class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-plum-100 text-plum-700"><x-icon name="phone" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold">{{ __('Call / WhatsApp') }}</p>
                            <a href="tel:{{ setting('contact_phone', '+8801711000000') }}" class="text-ink-500 hover:text-plum-700">{{ setting('contact_phone', '+880 1711-000000') }}</a>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-teal-700"><x-icon name="mail" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold">{{ __('Email') }}</p>
                            <a href="mailto:{{ setting('contact_email', 'hello@nokshi.com.bd') }}" class="text-ink-500 hover:text-plum-700">{{ setting('contact_email', 'hello@nokshi.com.bd') }}</a>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-marigold-100 text-marigold-700"><x-icon name="map-pin" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold">{{ __('Studio') }}</p>
                            <p class="text-ink-500">{{ setting('contact_address', 'Lalmatia, Dhaka 1207') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-ink/5 text-ink"><x-icon name="clock" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold">{{ __('Hours') }}</p>
                            <p class="text-ink-500">{{ __('Sat–Thu, 10am–7pm') }}</p>
                        </div>
                    </div>
                </div>
                @if(setting('whatsapp'))
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('whatsapp')) }}" target="_blank" class="btn btn-primary btn-lg w-full">
                        <x-icon name="whatsapp" class="h-5 w-5" /> {{ __('Chat on WhatsApp') }}
                    </a>
                @endif
            </div>

            <div class="lg:col-span-7">
                @if(session('success'))
                    <div class="mb-6"><x-alert type="success">{{ session('success') }}</x-alert></div>
                @endif
                <form method="POST" action="{{ route('contact.send') }}" class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5 space-y-5">
                    @csrf
                    {{-- Honeypot --}}
                    <input type="text" name="website" value="" class="hidden" tabindex="-1" autocomplete="off">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">{{ __('Name') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="input" required>
                            @error('name')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="input" required>
                            @error('email')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Phone') }}</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="input">
                        </div>
                        <div>
                            <label class="label">{{ __('Subject') }}</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="input" placeholder="{{ __('Order help, sizing…') }}">
                        </div>
                    </div>
                    <div>
                        <label class="label">{{ __('Message') }}</label>
                        <textarea name="message" rows="5" class="input" required>{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                    </div>
                    <button class="btn btn-primary btn-lg" data-magnetic>{{ __('Send message') }}</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
