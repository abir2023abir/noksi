import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;
Alpine.plugin(collapse);
Alpine.plugin(focus);

const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

window.admRequest = async function (url, { method = 'POST', body = null, form = null } = {}) {
    const headers = { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' };
    let payload = null;
    if (form) {
        payload = form;
    } else if (body) {
        headers['Content-Type'] = 'application/json';
        payload = JSON.stringify(body);
    }
    const res = await fetch(url, { method, headers, body: payload });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
};

Alpine.store('ui', {
    sidebarOpen: false,
    toasts: [],
    toast(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, message, type });
        setTimeout(() => (this.toasts = this.toasts.filter((t) => t.id !== id)), 3500);
    },
});

Chart.defaults.font.family = '"Plus Jakarta Sans", sans-serif';
Chart.defaults.color = '#8B8497';
Chart.defaults.borderColor = 'rgba(20,17,26,0.06)';

/* Slug helper */
Alpine.data('slugger', (initial = '', lockedInitially = false) => ({
    slug: initial,
    locked: lockedInitially || !!initial,
    from(value) {
        if (this.locked) return;
        this.slug = value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    },
}));

/* Image preview for single file inputs */
Alpine.data('imagePreview', (existing = null) => ({
    preview: existing,
    onChange(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.preview = URL.createObjectURL(file);
    },
    clear() {
        this.preview = null;
        this.$refs.input.value = '';
    },
}));

/* Confirm dialog */
Alpine.data('confirmAction', () => ({
    open: false,
    action: null,
    title: '',
    text: '',
    ask(form, title = 'Are you sure?', text = 'This action cannot be undone.') {
        this.action = form;
        this.title = title;
        this.text = text;
        this.open = true;
    },
    confirm() {
        this.action?.submit();
        this.open = false;
    },
}));

Alpine.start();
