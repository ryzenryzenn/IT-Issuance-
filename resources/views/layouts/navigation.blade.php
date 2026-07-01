{{-- Mobile top bar (visible below lg) --}}
<div class="lg:hidden sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <button @click="sidebarOpen = true" aria-label="Open menu"
            class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
        <x-application-logo class="block h-7 w-auto fill-current text-accent-400" />
        <span class="font-semibold text-gray-800 dark:text-gray-100">IT Assets</span>
    </a>
    <span class="w-10"></span>
</div>

{{-- Backdrop (mobile) --}}
<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-cloak></div>

{{-- Sidebar --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 transition-transform duration-200 lg:translate-x-0">

    {{-- Brand --}}
    <div class="h-16 shrink-0 flex items-center justify-between px-6 border-b border-gray-100 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="block h-8 w-auto fill-current text-accent-400" />
            <span class="font-semibold text-gray-800 dark:text-gray-100">IT Assets</span>
        </a>
        <button @click="sidebarOpen = false" aria-label="Close menu"
                class="lg:hidden p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Nav links --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10" />
            </svg>
            <span>Dashboard</span>
        </x-sidebar-link>

        @can('view assets')
            <x-sidebar-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10l9 4 9-4V7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v10" />
                </svg>
                <span>Assets</span>
            </x-sidebar-link>
        @endcan

        @can('view companies')
            <x-sidebar-link :href="route('companies.index')" :active="request()->routeIs('companies.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5a2 2 0 012-2h6a2 2 0 012 2v16M9 7h2M9 11h2M9 15h2M15 21V11h2a2 2 0 012 2v8" />
                </svg>
                <span>Companies</span>
            </x-sidebar-link>
        @endcan

        @can('view categories')
            <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M3 5a2 2 0 012-2h5.586a2 2 0 011.414.586l8 8a2 2 0 010 2.828l-5.586 5.586a2 2 0 01-2.828 0l-8-8A2 2 0 013 9.586V5z" />
                </svg>
                <span>Categories</span>
            </x-sidebar-link>
        @endcan

        @can('view locations')
            <x-sidebar-link :href="route('locations.index')" :active="request()->routeIs('locations.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Locations</span>
            </x-sidebar-link>
        @endcan

        @can('view employees')
            <x-sidebar-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Employees</span>
            </x-sidebar-link>
        @endcan

        @can('view asset models')
            <x-sidebar-link :href="route('asset-models.index')" :active="request()->routeIs('asset-models.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Asset Models</span>
            </x-sidebar-link>
        @endcan

        @can('export reports')
            <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6M12 17v-3M15 17v-8M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" />
                </svg>
                <span>Reports</span>
            </x-sidebar-link>
        @endcan

        @can('view audit logs')
            <x-sidebar-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span>Audit Logs</span>
            </x-sidebar-link>
        @endcan

        @can('view users')
            <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a3 3 0 10-2.5-1.34" />
                </svg>
                <span>Users</span>
            </x-sidebar-link>
        @endcan
    </nav>

    {{-- Footer: user + actions --}}
    <div class="shrink-0 border-t border-gray-100 dark:border-gray-700 px-3 py-4 space-y-2">
        <div class="flex items-center justify-between px-2">
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-indigo-500 dark:text-indigo-400 truncate">{{ Auth::user()->getRoleNames()->first() ?? 'No role' }}</p>
            </div>
            <button id="theme-toggle" type="button" title="Toggle dark mode"
                    class="shrink-0 rounded-md p-2 text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>Profile</span>
        </x-sidebar-link>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</aside>
