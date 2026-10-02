import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import { animate, inView, stagger, scroll } from 'motion';
import Swiper from 'swiper';
import { Autoplay, EffectFade, Pagination, Navigation, FreeMode } from 'swiper/modules';

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.plugin(focus);

/* --------------------------------------------------------------------------
 | HTTP helper
 |-------------------------------------------------------------------------- */
const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

async function request(url, { method = 'POST', body = null } = {}) {
    const res = await fetch(url, {
        method,
        headers: {
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
            ...(body ? { 'Content-Type': 'application/json' } : {}),
        },
        body: body ? JSON.stringify(body) : null,
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        throw new Error(data.message || 'Something went wrong');
    }
    return data;
}
window.nkRequest = request;

/* --------------------------------------------------------------------------
 | Global stores
 |-------------------------------------------------------------------------- */
Alpine.store('ui', {
    cartOpen: false,
    menuOpen: false,
    searchOpen: false,
    quickView: null,
    toasts: [],
    toast(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.dismiss(id), 3500);
    },
    dismiss(id) {
        this.toasts = this.toasts.filter((t) => t.id !== id);
    },
});

Alpine.store('cart', {
    count: window.__cart?.count ?? 0,
    subtotal: window.__cart?.subtotal_formatted ?? '',
    discount: window.__cart?.discount_formatted ?? '',
    total: window.__cart?.total_formatted ?? '',
    coupon: window.__cart?.coupon ?? null,
    items: window.__cart?.items ?? [],
    loading: false,
    bump: false,

    sync(payload) {
        Object.assign(this, {
            count: payload.count,
            subtotal: payload.subtotal_formatted,
            discount: payload.discount_formatted,
            total: payload.total_formatted,
            coupon: payload.coupon,
            items: payload.items ?? this.items,
        });
        this.bump = true;
        setTimeout(() => (this.bump = false), 500);
    },

    async add(productId, variantId = null, quantity = 1, open = true) {
        this.loading = true;
        try {
            const data = await request('/cart/add', { body: { product_id: productId, variant_id: variantId, quantity } });
            this.sync(data.cart);
            Alpine.store('ui').toast(data.message);
            if (open) Alpine.store('ui').cartOpen = true;
        } catch (e) {
            Alpine.store('ui').toast(e.message, 'error');
        } finally {
            this.loading = false;
        }
    },

    async update(itemId, quantity) {
        this.loading = true;
        try {
            const data = await request('/cart/update', { body: { item_id: itemId, quantity } });
            this.sync(data.cart);
        } catch (e) {
            Alpine.store('ui').toast(e.message, 'error');
        } finally {
            this.loading = false;
        }
    },

    async remove(itemId) {
        this.loading = true;
        try {
            const data = await request('/cart/remove', { body: { item_id: itemId } });
            this.sync(data.cart);
            Alpine.store('ui').toast(data.message);
        } catch (e) {
            Alpine.store('ui').toast(e.message, 'error');
        } finally {
            this.loading = false;
        }
    },

    async applyCoupon(code) {
        try {
            const data = await request('/cart/coupon', { body: { code } });
            this.sync(data.cart);
            Alpine.store('ui').toast(data.message);
            return true;
        } catch (e) {
            Alpine.store('ui').toast(e.message, 'error');
            return false;
        }
    },

    async removeCoupon() {
        const data = await request('/cart/coupon', { method: 'DELETE' });
        this.sync(data.cart);
    },
});

Alpine.store('wishlist', {
    ids: window.__wishlist ?? [],
    has(id) {
        return this.ids.includes(id);
    },
    async toggle(id) {
        try {
            const data = await request('/wishlist/toggle', { body: { product_id: id } });
            this.ids = data.ids;
            Alpine.store('ui').toast(data.message);
        } catch (e) {
            if (e.message.includes('login') || e.message.includes('Unauthenticated')) {
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                return;
            }
            Alpine.store('ui').toast(e.message, 'error');
        }
    },
});

/* --------------------------------------------------------------------------
 | Alpine components
 |-------------------------------------------------------------------------- */
Alpine.data('productOptions', (config) => ({
    variants: config.variants,
    color: config.defaultColor,
    size: null,
    qty: 1,
    galleryIndex: 0,
    get colors() {
        const seen = new Map();
        this.variants.forEach((v) => v.color && !seen.has(v.color) && seen.set(v.color, v.color_code));
        return [...seen.entries()].map(([name, code]) => ({ name, code }));
    },
    get sizes() {
        const order = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'];
        const list = [...new Set(this.variants.filter((v) => !this.color || v.color === this.color).map((v) => v.size).filter(Boolean))];
        return list.sort((a, b) => {
            const ia = order.indexOf(a), ib = order.indexOf(b);
            if (ia === -1 && ib === -1) return a.localeCompare(b, undefined, { numeric: true });
            if (ia === -1) return 1;
            if (ib === -1) return -1;
            return ia - ib;
        });
    },
    stockFor(size) {
        return this.variants.filter((v) => (!this.color || v.color === this.color) && v.size === size).reduce((s, v) => s + v.stock, 0);
    },
    get selected() {
        return this.variants.find((v) => (!this.color || v.color === this.color) && (!this.size || v.size === this.size) && (this.size || !v.size)) || null;
    },
    get price() {
        return this.selected?.price_formatted ?? config.price;
    },
    get maxQty() {
        return this.selected ? Math.max(1, this.selected.stock) : 99;
    },
    get canAdd() {
        if (!this.variants.length) return true;
        return !!this.selected && this.selected.stock > 0;
    },
    selectColor(name) {
        this.color = name;
        if (!this.sizes.includes(this.size)) this.size = null;
        const img = this.variants.find((v) => v.color === name && v.image);
        if (img && config.imageIndexByUrl && config.imageIndexByUrl[img.image] !== undefined) {
            this.galleryIndex = config.imageIndexByUrl[img.image];
        }
    },
    add() {
        if (this.variants.length && !this.selected) {
            Alpine.store('ui').toast('Please pick a size first', 'error');
            return;
        }
        Alpine.store('cart').add(config.productId, this.selected?.id ?? null, this.qty);
    },
}));

Alpine.data('checkoutForm', (config) => ({
    district: config.district,
    methodId: config.methodId,
    payment: config.payment,
    methods: config.methods,
    subtotal: config.subtotal,
    discount: config.discount,
    currency: config.currency,
    loadingMethods: false,
    submitting: false,
    fields: {
        name: document.querySelector('[name=name]')?.value ?? '',
        phone: document.querySelector('[name=phone]')?.value ?? '',
        email: document.querySelector('[name=email]')?.value ?? '',
        address_line: document.querySelector('[name=address_line]')?.value ?? '',
        area: document.querySelector('[name=area]')?.value ?? '',
        city: document.querySelector('[name=city]')?.value ?? '',
        postal_code: document.querySelector('[name=postal_code]')?.value ?? '',
    },
    get selectedMethod() {
        return this.methods.find((m) => m.id === this.methodId) || null;
    },
    get shippingCost() {
        return this.selectedMethod?.cost ?? 0;
    },
    get shippingCostFormatted() {
        if (!this.selectedMethod) return '—';
        return this.shippingCost > 0 ? this.selectedMethod.cost_formatted : (window.__i18n?.free ?? 'Free');
    },
    get grandTotalFormatted() {
        const total = Math.max(0, this.subtotal - this.discount + this.shippingCost);
        return this.currency + total.toLocaleString('en-BD', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    },
    fillAddress(a) {
        Object.assign(this.fields, a);
        ['name', 'phone', 'email', 'address_line', 'area', 'city', 'postal_code'].forEach((k) => {
            const el = document.querySelector(`[name="${k}"]`);
            if (el && a[k] !== undefined && a[k] !== null) el.value = a[k];
        });
        this.district = a.district;
        this.loadMethods();
    },
    async loadMethods() {
        this.loadingMethods = true;
        try {
            const res = await fetch(`/checkout/shipping-methods?district=${encodeURIComponent(this.district)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            this.methods = await res.json();
            if (!this.methods.find((m) => m.id === this.methodId)) {
                this.methodId = this.methods[0]?.id ?? null;
            }
        } finally {
            this.loadingMethods = false;
        }
    },
}));

Alpine.data('countdown', (endsAt) => ({
    d: '00', h: '00', m: '00', s: '00', ended: false,
    init() {
        this.tick();
        this._t = setInterval(() => this.tick(), 1000);
    },
    destroy() {
        clearInterval(this._t);
    },
    tick() {
        const diff = new Date(endsAt).getTime() - Date.now();
        if (diff <= 0) {
            this.ended = true;
            clearInterval(this._t);
            return;
        }
        const pad = (n) => String(n).padStart(2, '0');
        this.d = pad(Math.floor(diff / 864e5));
        this.h = pad(Math.floor((diff % 864e5) / 36e5));
        this.m = pad(Math.floor((diff % 36e5) / 6e4));
        this.s = pad(Math.floor((diff % 6e4) / 1e3));
    },
}));

Alpine.data('liveSearch', () => ({
    q: '',
    results: { products: [], categories: [] },
    loading: false,
    _t: null,
    search() {
        clearTimeout(this._t);
        if (this.q.trim().length < 2) {
            this.results = { products: [], categories: [] };
            return;
        }
        this.loading = true;
        this._t = setTimeout(async () => {
            try {
                const res = await fetch(`/search/suggest?q=${encodeURIComponent(this.q)}`, { headers: { Accept: 'application/json' } });
                this.results = await res.json();
            } finally {
                this.loading = false;
            }
        }, 220);
    },
}));

Alpine.data('shopFilters', () => ({
    loading: false,
    init() {
        window.addEventListener('popstate', () => this.load(window.location.href, false));
        document.getElementById('shop-results')?.addEventListener('click', (e) => {
            const a = e.target.closest('a[href*="page="]');
            if (a) {
                e.preventDefault();
                this.load(a.href);
            }
        });
    },
    submit(e) {
        const form = e?.target?.closest('form') ?? this.$root.querySelector('form');
        const params = new URLSearchParams(new FormData(form));
        for (const [k, v] of [...params]) if (v === '') params.delete(k);
        params.delete('page');
        this.load(`${form.action}?${params.toString()}`);
    },
    async load(url, push = true) {
        this.loading = true;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const grid = document.getElementById('shop-results');
            grid.innerHTML = html;
            if (push) history.pushState({}, '', url);
            grid.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
            window.scrollTo({ top: grid.getBoundingClientRect().top + window.scrollY - 140, behavior: 'smooth' });
        } finally {
            this.loading = false;
        }
    },
    reset() {
        this.load(window.location.pathname);
        this.$root.querySelectorAll('input:checked').forEach((i) => (i.checked = false));
        this.$root.querySelectorAll('input[type=number]').forEach((i) => (i.value = ''));
    },
}));

Alpine.data('quickView', () => ({
    open: false,
    html: '',
    async show(slug) {
        this.open = true;
        this.html = '<div class="p-16 text-center"><div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-plum-700 border-t-transparent"></div></div>';
        const res = await fetch(`/product/${slug}/quick-view`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        this.html = await res.text();
    },
}));

/* --------------------------------------------------------------------------
 | Motion: entrance animations
 |-------------------------------------------------------------------------- */
function initMotion() {
    // Scroll reveal for anything tagged .reveal
    document.querySelectorAll('.reveal:not(.is-visible)').forEach((el) => {
        inView(el, () => el.classList.add('is-visible'), { margin: '0px 0px -10% 0px' });
    });

    // Staggered children
    document.querySelectorAll('[data-stagger]').forEach((container) => {
        const children = container.querySelectorAll('[data-stagger-item]');
        if (!children.length) return;
        children.forEach((c) => (c.style.opacity = '0'));
        inView(container, () => {
            animate(children, { opacity: [0, 1], y: [28, 0] }, { delay: stagger(0.08), duration: 0.8, easing: [0.16, 1, 0.3, 1] });
        }, { margin: '0px 0px -10% 0px' });
    });

    // Hero text: split words
    document.querySelectorAll('[data-split]').forEach((el) => {
        if (el.dataset.splitDone) return;
        const words = el.textContent.trim().split(/\s+/);
        el.innerHTML = words.map((w) => `<span class="inline-block overflow-hidden align-bottom"><span class="inline-block will-change-transform">${w}</span></span>`).join(' ');
        el.dataset.splitDone = '1';
        animate(el.querySelectorAll('span > span'), { y: ['110%', '0%'] }, { delay: stagger(0.06, { start: 0.15 }), duration: 0.9, easing: [0.16, 1, 0.3, 1] });
    });

    // Parallax images
    document.querySelectorAll('[data-parallax]').forEach((el) => {
        const strength = parseFloat(el.dataset.parallax || '0.15');
        scroll(animate(el, { y: [`-${strength * 100}px`, `${strength * 100}px`] }, { easing: 'linear' }), { target: el.parentElement, offset: ['start end', 'end start'] });
    });

    // Magnetic buttons
    document.querySelectorAll('[data-magnetic]').forEach((el) => {
        el.addEventListener('mousemove', (e) => {
            const r = el.getBoundingClientRect();
            const x = (e.clientX - r.left - r.width / 2) * 0.25;
            const y = (e.clientY - r.top - r.height / 2) * 0.25;
            animate(el, { x, y }, { duration: 0.3, easing: 'ease-out' });
        });
        el.addEventListener('mouseleave', () => animate(el, { x: 0, y: 0 }, { duration: 0.5, easing: [0.16, 1, 0.3, 1] }));
    });
}

/* --------------------------------------------------------------------------
 | Swiper sliders
 |-------------------------------------------------------------------------- */
function initSliders() {
    document.querySelectorAll('[data-hero-slider]').forEach((el) => {
        new Swiper(el, {
            modules: [Autoplay, EffectFade, Pagination],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            loop: el.querySelectorAll('.swiper-slide').length > 1,
            speed: 1200,
            autoplay: { delay: 6000, disableOnInteraction: false },
            pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
            on: {
                slideChangeTransitionStart(s) {
                    const active = s.slides[s.activeIndex];
                    const items = active.querySelectorAll('[data-hero-anim]');
                    animate(items, { opacity: [0, 1], y: [30, 0] }, { delay: stagger(0.12, { start: 0.2 }), duration: 0.9, easing: [0.16, 1, 0.3, 1] });
                },
            },
        });
    });

    document.querySelectorAll('[data-carousel]').forEach((el) => {
        const perView = parseFloat(el.dataset.perView || '4');
        new Swiper(el, {
            modules: [Navigation, FreeMode],
            slidesPerView: 1.3,
            spaceBetween: 16,
            freeMode: { enabled: true, momentumBounce: false },
            navigation: { nextEl: el.parentElement.querySelector('[data-next]'), prevEl: el.parentElement.querySelector('[data-prev]') },
            breakpoints: {
                640: { slidesPerView: 2.2, spaceBetween: 20 },
                1024: { slidesPerView: Math.min(perView, 3.3), spaceBetween: 24 },
                1280: { slidesPerView: perView, spaceBetween: 28 },
            },
        });
    });

    document.querySelectorAll('[data-testimonials]').forEach((el) => {
        new Swiper(el, {
            modules: [Autoplay, Pagination],
            slidesPerView: 1,
            spaceBetween: 24,
            autoplay: { delay: 5000 },
            pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
            breakpoints: { 768: { slidesPerView: 2 }, 1280: { slidesPerView: 3 } },
        });
    });
}

/* --------------------------------------------------------------------------
 | Header behaviour
 |-------------------------------------------------------------------------- */
function initHeader() {
    const header = document.getElementById('site-header');
    if (!header) return;
    let last = 0;
    window.addEventListener('scroll', () => {
        const y = window.scrollY;
        header.classList.toggle('is-scrolled', y > 40);
        header.classList.toggle('is-hidden', y > 400 && y > last && !Alpine.store('ui').menuOpen);
        last = y;
    }, { passive: true });
}

/* --------------------------------------------------------------------------
 | Image fallback
 |-------------------------------------------------------------------------- */
document.addEventListener('error', (e) => {
    const el = e.target;
    if (el.tagName === 'IMG' && !el.dataset.fallbackApplied) {
        el.dataset.fallbackApplied = '1';
        el.src = '/images/placeholder.svg';
    }
}, true);

document.addEventListener('alpine:init', () => {});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initMotion();
    initSliders();
    initHeader();
});

// Re-run motion after AJAX content swaps
window.nkRefreshMotion = initMotion;
