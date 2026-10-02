<x-layouts.app :title="__('Checkout')">
    @php
        $addr = old() ? [
            'name' => old('name'), 'phone' => old('phone'), 'email' => old('email'),
            'address_line' => old('address_line'), 'area' => old('area'), 'city' => old('city'),
            'district' => old('district', 'Dhaka'), 'postal_code' => old('postal_code'),
        ] : [
            'name' => $defaultAddress?->name ?? auth()->user()?->name,
            'phone' => $defaultAddress?->phone ?? auth()->user()?->phone,
            'email' => $defaultAddress?->email ?? auth()->user()?->email,
            'address_line' => $defaultAddress?->address_line,
            'area' => $defaultAddress?->area,
            'city' => $defaultAddress?->city ?? 'Dhaka',
            'district' => $defaultAddress?->district ?? 'Dhaka',
            'postal_code' => $defaultAddress?->postal_code,
        ];
        $methodsJson = $methods->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'description' => $m->description,
            'eta' => $m->estimated_days,
            'cost' => $m->costFor($cart->subtotal),
            'cost_formatted' => $m->costFor($cart->subtotal) > 0 ? money($m->costFor($cart->subtotal)) : __('Free'),
        ])->values();
    @endphp

    <section class="relative overflow-hidden bg-teal-900 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-10 md:py-14">
            <x-breadcrumb :items="[__('Bag') => route('cart.index'), __('Checkout') => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
            <h1 class="mt-4 font-display text-display-sm md:text-display-md" data-split>{{ __('Checkout') }}</h1>
            <p class="mt-2 text-paper/70">{{ __('Almost there — where should we send it?') }}</p>
        </div>
    </section>

    @if(!empty($problems))
        <div class="container pt-6">
            <x-alert type="error">
                @foreach($problems as $p)<p>{{ $p }}</p>@endforeach
            </x-alert>
        </div>
    @endif

    <section class="container py-10 md:py-14"
             x-data="checkoutForm({
                district: @js($addr['district']),
                methodId: @js((int) old('shipping_method_id', $methods->first()?->id)),
                payment: @js(old('payment_method', $codEnabled ? 'cod' : 'sslcommerz')),
                methods: @js($methodsJson),
                subtotal: {{ (float) $cart->subtotal }},
                discount: {{ (float) $cart->discount }},
                currency: @js(setting('currency_symbol', '৳')),
             })">

        <form method="POST" action="{{ route('checkout.store') }}" class="grid gap-10 lg:grid-cols-12" @submit="submitting = true">
            @csrf

            <div class="lg:col-span-7 space-y-8">
                {{-- Saved addresses --}}
                @if($addresses->isNotEmpty())
                    <div>
                        <h2 class="font-display text-2xl mb-4">{{ __('Deliver to') }}</h2>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($addresses as $a)
                                <button type="button"
                                        @click="fillAddress(@js(['name'=>$a->name,'phone'=>$a->phone,'email'=>$a->email,'address_line'=>$a->address_line,'area'=>$a->area,'city'=>$a->city,'district'=>$a->district,'postal_code'=>$a->postal_code]))"
                                        class="rounded-2xl border p-4 text-left transition hover:border-plum-700"
                                        :class="district === @js($a->district) && fields.address_line === @js($a->address_line) ? 'border-plum-950 bg-plum-50' : 'border-ink/10 bg-white'">
                                    <p class="font-semibold">{{ $a->label ?? $a->name }}</p>
                                    <p class="mt-1 text-sm text-ink-500">{{ $a->address_line }}@if($a->area), {{ $a->area }}@endif</p>
                                    <p class="text-sm text-ink-500">{{ $a->city }}, {{ $a->district }}</p>
                                    <p class="mt-1 text-sm text-ink-300">{{ $a->phone }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Contact & address --}}
                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5 space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-2xl">{{ __('Shipping details') }}</h2>
                        @guest
                            <a href="{{ route('login', ['redirect' => route('checkout.index')]) }}" class="text-sm font-semibold text-plum-700 hover:underline">{{ __('Login') }}</a>
                        @endguest
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="label">{{ __('Full name') }}</label>
                            <input type="text" name="name" x-model="fields.name" value="{{ $addr['name'] }}" class="input" required autocomplete="name">
                            @error('name')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Mobile') }}</label>
                            <input type="tel" name="phone" x-model="fields.phone" value="{{ $addr['phone'] }}" class="input" placeholder="01XXXXXXXXX" required autocomplete="tel">
                            @error('phone')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Email') }} <span class="text-ink-300 font-normal">({{ __('optional') }})</span></label>
                            <input type="email" name="email" x-model="fields.email" value="{{ $addr['email'] }}" class="input" autocomplete="email">
                            @error('email')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label">{{ __('Address') }}</label>
                            <input type="text" name="address_line" x-model="fields.address_line" value="{{ $addr['address_line'] }}" class="input" placeholder="{{ __('House, road, block') }}" required autocomplete="street-address">
                            @error('address_line')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Area / Thana') }}</label>
                            <input type="text" name="area" x-model="fields.area" value="{{ $addr['area'] }}" class="input" placeholder="{{ __('e.g. Dhanmondi') }}">
                        </div>
                        <div>
                            <label class="label">{{ __('City') }}</label>
                            <input type="text" name="city" x-model="fields.city" value="{{ $addr['city'] }}" class="input" required>
                        </div>
                        <div>
                            <label class="label">{{ __('District') }}</label>
                            <select name="district" x-model="district" @change="loadMethods()" class="input" required>
                                @foreach($districts as $d)
                                    <option value="{{ $d }}" @selected($addr['district'] === $d)>{{ $d }}</option>
                                @endforeach
                            </select>
                            @error('district')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Postal code') }}</label>
                            <input type="text" name="postal_code" x-model="fields.postal_code" value="{{ $addr['postal_code'] }}" class="input" autocomplete="postal-code">
                        </div>
                    </div>

                    @auth
                        <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                            <input type="checkbox" name="save_address" value="1" class="checkbox" @checked(old('save_address'))>
                            <span>{{ __('Save this address to my account') }}</span>
                        </label>
                    @else
                        <div class="rounded-2xl bg-paper p-4 space-y-3" x-data="{ create: {{ old('create_account') ? 'true' : 'false' }} }">
                            <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                                <input type="checkbox" name="create_account" value="1" class="checkbox" x-model="create">
                                <span>{{ __('Create an account for faster checkout next time') }}</span>
                            </label>
                            <div x-show="create" x-collapse x-cloak>
                                <label class="label">{{ __('Password') }}</label>
                                <input type="password" name="password" class="input" minlength="8" :required="create" autocomplete="new-password">
                                @error('password')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endauth
                </div>

                {{-- Shipping methods --}}
                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-2xl">{{ __('Delivery') }}</h2>
                        <span class="text-sm text-ink-300" x-show="loadingMethods" x-cloak>{{ __('Updating…') }}</span>
                    </div>
                    <input type="hidden" name="shipping_method_id" :value="methodId">
                    <div class="space-y-3">
                        <template x-if="methods.length === 0">
                            <p class="text-sm text-terracotta-700">{{ __('No delivery options for this district. Try another or contact us.') }}</p>
                        </template>
                        <template x-for="m in methods" :key="m.id">
                            <label class="flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition"
                                   :class="methodId === m.id ? 'border-plum-950 bg-plum-50' : 'border-ink/10 hover:border-ink/30'">
                                <input type="radio" class="radio mt-1" :value="m.id" x-model.number="methodId" name="shipping_method_radio">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-semibold" x-text="m.name"></span>
                                        <span class="font-semibold shrink-0" x-text="m.cost_formatted"></span>
                                    </div>
                                    <p class="mt-0.5 text-sm text-ink-500" x-text="m.description" x-show="m.description"></p>
                                    <p class="mt-1 text-xs text-ink-300" x-show="m.eta" x-text="m.eta"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                    @error('shipping_method_id')<p class="text-xs text-terracotta-700">{{ $message }}</p>@enderror
                </div>

                {{-- Payment --}}
                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5 space-y-4">
                    <h2 class="font-display text-2xl">{{ __('Payment') }}</h2>
                    <div class="space-y-3">
                        @if($codEnabled)
                            <label class="flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition"
                                   :class="payment === 'cod' ? 'border-plum-950 bg-plum-50' : 'border-ink/10 hover:border-ink/30'">
                                <input type="radio" name="payment_method" value="cod" class="radio mt-1" x-model="payment">
                                <div>
                                    <span class="font-semibold inline-flex items-center gap-2"><x-icon name="cash" class="h-4.5 w-4.5 text-plum-700" /> {{ __('Cash on delivery') }}</span>
                                    <p class="mt-0.5 text-sm text-ink-500">{{ __('Pay with cash when your order arrives. Available nationwide.') }}</p>
                                </div>
                            </label>
                        @endif
                        @if($sslEnabled)
                            <label class="flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition"
                                   :class="payment === 'sslcommerz' ? 'border-plum-950 bg-plum-50' : 'border-ink/10 hover:border-ink/30'">
                                <input type="radio" name="payment_method" value="sslcommerz" class="radio mt-1" x-model="payment">
                                <div>
                                    <span class="font-semibold inline-flex items-center gap-2"><x-icon name="credit-card" class="h-4.5 w-4.5 text-teal-700" /> {{ __('Card / Mobile banking') }}</span>
                                    <p class="mt-0.5 text-sm text-ink-500">{{ __('Pay securely via SSLCommerz — Visa, Mastercard, bKash, Nagad, Rocket and more.') }}</p>
                                </div>
                            </label>
                        @endif
                    </div>
                    @error('payment_method')<p class="text-xs text-terracotta-700">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5 space-y-4">
                    <h2 class="font-display text-2xl">{{ __('Notes') }} <span class="text-base font-sans font-normal text-ink-300">({{ __('optional') }})</span></h2>
                    <textarea name="notes" rows="3" class="input" placeholder="{{ __('Gate code, preferred time, gift message…') }}">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Summary --}}
            <aside class="lg:col-span-5">
                <div class="lg:sticky lg:top-28 space-y-5">
                    <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 space-y-5">
                        <h2 class="font-display text-2xl">{{ __('Your order') }}</h2>
                        <ul class="space-y-4 max-h-72 overflow-y-auto">
                            @foreach($cart->items as $item)
                                <li class="flex gap-3">
                                    <img src="{{ $item->image_url }}" alt="" class="h-16 w-14 rounded-xl object-cover bg-paper-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-sm truncate">{{ $item->product->name }}</p>
                                        @if($item->variant)<p class="text-xs text-ink-300">{{ $item->variant->label }}</p>@endif
                                        <p class="text-xs text-ink-500">× {{ $item->quantity }}</p>
                                    </div>
                                    <span class="text-sm font-semibold shrink-0">{{ money($item->line_total) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="border-t border-ink/10 pt-4 space-y-2 text-[15px]">
                            <div class="flex justify-between text-ink-500"><span>{{ __('Subtotal') }}</span><span>{{ money($cart->subtotal) }}</span></div>
                            @if($cart->discount > 0)
                                <div class="flex justify-between text-teal-700">
                                    <span>{{ __('Discount') }}@if($cart->coupon) ({{ $cart->coupon->code }})@endif</span>
                                    <span>−{{ money($cart->discount) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-ink-500">
                                <span>{{ __('Delivery') }}</span>
                                <span x-text="shippingCostFormatted">{{ $methods->first() ? ($methods->first()->costFor($cart->subtotal) > 0 ? money($methods->first()->costFor($cart->subtotal)) : __('Free')) : '—' }}</span>
                            </div>
                            <div class="border-t border-ink/10 pt-3 flex justify-between text-lg font-bold">
                                <span>{{ __('Total') }}</span>
                                <span x-text="grandTotalFormatted">{{ money($cart->total + ($methods->first()?->costFor($cart->subtotal) ?? 0)) }}</span>
                            </div>
                        </div>

                        <label class="flex items-start gap-2.5 text-sm cursor-pointer">
                            <input type="checkbox" name="terms" value="1" class="checkbox mt-0.5" required @checked(old('terms'))>
                            <span>{{ __('I agree to the') }} <a href="{{ route('page.show', 'terms') }}" class="font-semibold text-plum-700 hover:underline" target="_blank">{{ __('terms') }}</a> {{ __('and') }} <a href="{{ route('page.show', 'returns') }}" class="font-semibold text-plum-700 hover:underline" target="_blank">{{ __('return policy') }}</a>.</span>
                        </label>
                        @error('terms')<p class="text-xs text-terracotta-700">{{ $message }}</p>@enderror

                        <button type="submit" class="btn btn-primary btn-lg w-full" data-magnetic :disabled="submitting || !methodId" :class="{ 'opacity-70': submitting }">
                            <span x-text="payment === 'sslcommerz' ? '{{ __('Pay now') }}' : '{{ __('Place order') }}'"></span>
                            <x-icon name="arrow-right" class="h-5 w-5" />
                        </button>
                        <p class="text-center text-xs text-ink-300">{{ __('You will receive an SMS confirmation shortly after placing.') }}</p>
                    </div>

                    <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-500 hover:text-ink"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Back to bag') }}</a>
                </div>
            </aside>
        </form>
    </section>
</x-layouts.app>
