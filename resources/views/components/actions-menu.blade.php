@php $mid = 'menu-'.\Illuminate\Support\Str::random(8); @endphp

<div class="relative inline-block text-left"
     x-data="{
        id: '{{ $mid }}',
        x: 0, y: 0,
        place() {
            const r = $refs.btn.getBoundingClientRect();
            this.y = r.bottom + 6;
            this.x = Math.max(8, r.right - 176); /* 176px = w-44, right-aligned */
        },
     }">
    <button x-ref="btn" type="button" aria-label="Actions"
            @click.stop="place(); $store.menu.current = ($store.menu.current === id ? null : id)"
            class="p-1.5 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 focus:outline-none">
        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
            <circle cx="10" cy="4" r="1.7"/><circle cx="10" cy="10" r="1.7"/><circle cx="10" cy="16" r="1.7"/>
        </svg>
    </button>

    <template x-teleport="body">
        <div x-show="$store.menu.current === id" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.outside="$store.menu.current = null"
             @keydown.escape.window="$store.menu.current = null"
             @scroll.window="$store.menu.current = null"
             @resize.window="$store.menu.current = null"
             :style="`position:fixed; top:${y}px; left:${x}px; width:11rem;`"
             class="z-50 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10 py-1 text-sm overflow-hidden">
            {{ $slot }}
        </div>
    </template>
</div>
