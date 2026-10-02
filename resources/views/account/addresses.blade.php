<x-layouts.app :title="__('Addresses')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-10 md:py-12">
            <h1 class="font-display text-display-sm">{{ __('Addresses') }}</h1>
            <p class="mt-2 text-paper/70">{{ __('Where your orders land.') }}</p>
        </div>
    </section>

    <section class="container py-10 md:py-14" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }}, editing: null }">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-3">@include('partials.account-nav')</div>
            <div class="lg:col-span-9 space-y-5">
                <div class="flex justify-between items-center">
                    <p class="text-ink-500">{{ trans_choice(':count address|:count addresses', $addresses->count(), ['count' => $addresses->count()]) }}</p>
                    <button type="button" class="btn btn-primary btn-md" @click="open = true; editing = null">{{ __('Add address') }}</button>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @forelse($addresses as $a)
                        <article class="rounded-3xl bg-white p-5 ring-1 ring-ink/5 relative">
                            @if($a->is_default)<span class="absolute right-4 top-4 badge bg-teal-50 text-teal-700">{{ __('Default') }}</span>@endif
                            <p class="font-semibold">{{ $a->label ?: __('Address') }}</p>
                            <p class="mt-2 text-sm text-ink-500">{{ $a->name }} · {{ $a->phone }}</p>
                            <p class="mt-1 text-sm text-ink-500">{{ $a->address_line }}@if($a->area), {{ $a->area }}@endif</p>
                            <p class="text-sm text-ink-500">{{ $a->city }}, {{ $a->district }} {{ $a->postal_code }}</p>
                            <div class="mt-4 flex gap-3">
                                <button type="button" class="text-sm font-semibold text-plum-700 hover:underline"
                                    @click="editing = @js($a->only(['id','label','name','phone','email','address_line','area','city','district','postal_code','is_default'])); open = true">{{ __('Edit') }}</button>
                                <form method="POST" action="{{ route('account.addresses.destroy', $a) }}" onsubmit="return confirm(@js(__('Remove this address?')))">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-semibold text-terracotta-700 hover:underline">{{ __('Remove') }}</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="sm:col-span-2 rounded-3xl border-2 border-dashed border-ink/10 p-12 text-center">
                            <p class="font-display text-2xl">{{ __('No saved addresses') }}</p>
                            <button type="button" class="btn btn-primary btn-md mt-5" @click="open = true">{{ __('Add your first') }}</button>
                        </div>
                    @endforelse
                </div>

                {{-- Modal form --}}
                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4" role="dialog">
                    <div class="absolute inset-0 bg-plum-950/60 backdrop-blur-sm" @click="open = false"></div>
                    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl bg-paper p-6 shadow-lift" @click.stop>
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="font-display text-2xl" x-text="editing ? '{{ __('Edit address') }}' : '{{ __('New address') }}'"></h2>
                            <button type="button" class="icon-btn" @click="open = false"><x-icon name="x" class="h-5 w-5" /></button>
                        </div>
                        <form method="POST" :action="editing ? `/account/addresses/${editing.id}` : '{{ route('account.addresses.store') }}'" class="space-y-4">
                            @csrf
                            <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div><label class="label">{{ __('Label') }}</label><input type="text" name="label" class="input" placeholder="Home / Office" :value="editing?.label"></div>
                                <div><label class="label">{{ __('Full name') }}</label><input type="text" name="name" class="input" required :value="editing?.name || '{{ auth()->user()->name }}'"></div>
                                <div><label class="label">{{ __('Phone') }}</label><input type="tel" name="phone" class="input" required :value="editing?.phone || '{{ auth()->user()->phone }}'"></div>
                                <div><label class="label">{{ __('Email') }}</label><input type="email" name="email" class="input" :value="editing?.email || '{{ auth()->user()->email }}'"></div>
                                <div class="sm:col-span-2"><label class="label">{{ __('Address') }}</label><input type="text" name="address_line" class="input" required :value="editing?.address_line"></div>
                                <div><label class="label">{{ __('Area') }}</label><input type="text" name="area" class="input" :value="editing?.area"></div>
                                <div><label class="label">{{ __('City') }}</label><input type="text" name="city" class="input" required :value="editing?.city || 'Dhaka'"></div>
                                <div>
                                    <label class="label">{{ __('District') }}</label>
                                    <select name="district" class="input" required>
                                        @foreach($districts as $d)
                                            <option value="{{ $d }}" :selected="(editing?.district || 'Dhaka') === '{{ $d }}'">{{ $d }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label class="label">{{ __('Postal code') }}</label><input type="text" name="postal_code" class="input" :value="editing?.postal_code"></div>
                            </div>
                            <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                                <input type="checkbox" name="is_default" value="1" class="checkbox" :checked="editing?.is_default">
                                <span>{{ __('Set as default') }}</span>
                            </label>
                            <button class="btn btn-primary btn-lg w-full">{{ __('Save address') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
