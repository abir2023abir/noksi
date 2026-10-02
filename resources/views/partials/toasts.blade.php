<div class="pointer-events-none fixed inset-x-0 bottom-5 z-[70] flex flex-col items-center gap-2 px-4 sm:items-start sm:left-5 sm:inset-x-auto" aria-live="polite">
    <template x-for="t in $store.ui.toasts" :key="t.id">
        <div x-show="true" x-transition:enter="transition ease-out-expo duration-500" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="toast max-w-sm" :class="{ 'bg-terracotta-700': t.type === 'error', 'bg-plum-950': t.type === 'success', 'bg-teal-800': t.type === 'info' }">
            <span class="mt-0.5 shrink-0" :class="t.type === 'error' ? 'text-white' : 'text-marigold-400'">
                <template x-if="t.type === 'error'"><x-icon name="alert" class="h-5 w-5" /></template>
                <template x-if="t.type !== 'error'"><x-icon name="check-circle" class="h-5 w-5" /></template>
            </span>
            <span class="flex-1 font-medium" x-text="t.message"></span>
            <button class="shrink-0 opacity-60 hover:opacity-100" @click="$store.ui.dismiss(t.id)"><x-icon name="x" class="h-4 w-4" /></button>
        </div>
    </template>
</div>
