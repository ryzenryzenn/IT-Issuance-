<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Welcome back, {{ Auth::user()->name }}
            </span>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- KPI cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @php
                    $cards = [
                        ['label' => 'Total Assets',          'value' => $totalAssets,        'class' => 'text-indigo-600 dark:text-white'],
                        ['label' => 'Assigned Assets',       'value' => $assignedAssets,     'class' => 'text-sky-600 dark:text-white'],
                        ['label' => 'Pending Signature',     'value' => $pendingSignature,   'class' => 'text-amber-500 dark:text-white'],
                        ['label' => 'Pending Snipe-IT',      'value' => $pendingSnipeit,     'class' => 'text-rose-600 dark:text-white'],
                    ];
                @endphp
                @foreach ($cards as $card)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 px-4 py-3">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 truncate">{{ $card['label'] }}</p>
                        <p class="mt-0.5 text-2xl font-semibold leading-tight {{ $card['class'] }}">
                            {{ number_format($card['value']) }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Row: Today + Calendar + Trend --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-xl shadow-sm p-3.5 flex flex-col justify-center">
                    <p class="text-white/60 text-[10px] font-medium uppercase tracking-wider">Today</p>
                    <p class="mt-0.5 text-xl font-bold leading-tight">{{ now()->format('l') }}</p>
                    <p class="text-white/80 text-xs">{{ now()->format('F j, Y') }}</p>
                    <p id="live-clock" class="mt-1 text-base font-semibold tabular-nums text-accent-400">{{ now()->format('h:i:s A') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-3">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-xs font-semibold text-gray-800 dark:text-gray-100">{{ now()->format('F Y') }}</h3>
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 inline-block" title="assets issued"></span>
                    </div>
                    @php
                        $first        = now()->startOfMonth();
                        $daysInMonth  = $first->daysInMonth;
                        $startWeekday = (int) $first->dayOfWeek; // 0 = Sunday
                        $todayDay     = now()->day;
                    @endphp
                    <div class="grid grid-cols-7 gap-0.5 text-center">
                        @foreach (['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $d)
                            <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500">{{ $d }}</div>
                        @endforeach
                        @for ($i = 0; $i < $startWeekday; $i++)
                            <div></div>
                        @endfor
                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php $count = $issuedDays[$day] ?? 0; @endphp
                            <div class="relative h-6 flex items-center justify-center text-[11px] rounded
                                        {{ $day === $todayDay
                                            ? 'bg-accent-400 text-indigo-900 font-bold'
                                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                {{ $day }}
                                @if ($count > 0)
                                    <span title="{{ $count }} asset(s) issued"
                                          class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full {{ $day === $todayDay ? 'bg-indigo-900' : 'bg-indigo-500' }}"></span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="sm:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-3.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Assets Added</h3>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500">Last 6 months</span>
                    </div>
                    <div id="chart-trend"></div>
                </div>
            </div>

            {{-- Row: three charts side by side --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-3.5">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Accountability</h3>
                    <div id="chart-accountability"></div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-3.5">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Assets by Category</h3>
                    <div id="chart-category"></div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-3.5">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Assets by Company</h3>
                    <div id="chart-company"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Recent assets --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Recent Assets</h3>
                        @can('view assets')
                            <a href="{{ route('assets.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View all →</a>
                        @endcan
                    </div>
                    <div x-data="ajaxPager" @click="go($event)" :class="loading && 'opacity-50 pointer-events-none transition'">
                        <div data-pager-body>
                            @include('dashboard._recent-assets')
                        </div>
                    </div>
                </div>

                {{-- Recent activity --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Recent Activity</h3>
                    </div>
                    <div x-data="ajaxPager" @click="go($event)" :class="loading && 'opacity-50 pointer-events-none transition'">
                        <div data-pager-body>
                            @include('dashboard._recent-activity')
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        // Live clock in the "Today" card
        (function () {
            const clock = document.getElementById('live-clock');
            if (!clock) return;
            const tick = () => clock.textContent = new Date().toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            tick();
            setInterval(tick, 1000);
        })();

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof ApexCharts === 'undefined') { console.error('ApexCharts not loaded'); return; }

            const dark    = document.documentElement.classList.contains('dark');
            const fg      = dark ? '#9ca3af' : '#6b7280';
            const grid    = dark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
            const tooltip = { theme: dark ? 'dark' : 'light' };
            const palette = ['#003049', '#FCBF49', '#0ea5e9', '#10b981', '#f43f5e', '#8b5cf6', '#f59e0b', '#14b8a6'];
            const baseChart = {
                fontFamily: 'Figtree, ui-sans-serif, sans-serif',
                foreColor: fg,
                toolbar: { show: false },
                animations: { easing: 'easeinout', speed: 700 },
            };

            // Assets added — area
            new ApexCharts(document.querySelector('#chart-trend'), {
                chart: { ...baseChart, type: 'area', height: 170 },
                series: [{ name: 'Assets', data: @json($assetsPerMonth->pluck('count')) }],
                xaxis: { categories: @json($assetsPerMonth->pluck('label')), axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { formatter: (v) => Math.round(v) } },
                colors: ['#003049'],
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.03, stops: [0, 90] } },
                dataLabels: { enabled: false },
                grid: { borderColor: grid, strokeDashArray: 4 },
                markers: { size: 0, hover: { size: 6 } },
                tooltip,
            }).render();

            // Accountability — donut
            const acc = @json([$signedAssets, $pendingSignature]);
            new ApexCharts(document.querySelector('#chart-accountability'), {
                chart: { ...baseChart, type: 'donut', height: 170 },
                series: acc,
                labels: ['Signed', 'Pending'],
                colors: ['#10b981', '#FCBF49'],
                legend: { position: 'bottom' },
                stroke: { width: 0 },
                plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Total', color: fg, formatter: () => acc.reduce((a, b) => a + b, 0) } } } } },
                dataLabels: { enabled: true, formatter: (v) => Math.round(v) + '%' },
                tooltip,
            }).render();

            // By category — donut
            new ApexCharts(document.querySelector('#chart-category'), {
                chart: { ...baseChart, type: 'donut', height: 170 },
                series: @json($perCategory->filter(fn ($c) => $c->assets_count > 0)->pluck('assets_count')->values()),
                labels: @json($perCategory->filter(fn ($c) => $c->assets_count > 0)->pluck('name')->values()),
                colors: palette,
                legend: { position: 'bottom' },
                stroke: { width: 0 },
                plotOptions: { pie: { donut: { size: '68%' } } },
                dataLabels: { enabled: false },
                tooltip,
            }).render();

            // By company — horizontal bar (names on the left so they never overlap)
            new ApexCharts(document.querySelector('#chart-company'), {
                chart: { ...baseChart, type: 'bar', height: 170 },
                series: [{ name: 'Assets', data: @json($perCompany->filter(fn ($c) => $c->assets_count > 0)->pluck('assets_count')->values()) }],
                xaxis: {
                    categories: @json($perCompany->filter(fn ($c) => $c->assets_count > 0)->pluck('name')->values()),
                    labels: { formatter: (v) => Math.round(v) },
                    axisBorder: { show: false }, axisTicks: { show: false },
                },
                yaxis: {
                    labels: {
                        maxWidth: 120,
                        style: { fontSize: '11px' },
                        formatter: (v) => (typeof v === 'string' && v.length > 18) ? v.slice(0, 17) + '…' : v,
                    },
                },
                colors: ['#003049'],
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%', distributed: false } },
                dataLabels: { enabled: false },
                grid: { borderColor: grid, strokeDashArray: 4 },
                tooltip,
            }).render();
        });
    </script>
    @endpush
</x-app-layout>
