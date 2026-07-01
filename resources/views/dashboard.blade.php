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

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- KPI cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $cards = [
                        ['label' => 'Total Assets',          'value' => $totalAssets,        'class' => 'text-indigo-600 dark:text-white'],
                        ['label' => 'Assigned Assets',       'value' => $assignedAssets,     'class' => 'text-sky-600 dark:text-white'],
                        ['label' => 'Pending Signature',     'value' => $pendingSignature,   'class' => 'text-amber-500 dark:text-white'],
                        ['label' => 'Pending Snipe-IT',      'value' => $pendingSnipeit,     'class' => 'text-rose-600 dark:text-white'],
                    ];
                @endphp
                @foreach ($cards as $card)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-semibold {{ $card['class'] }}">
                            {{ number_format($card['value']) }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Assets Added</h3>
                        <span class="text-xs text-gray-400 dark:text-gray-500">Last 6 months</span>
                    </div>
                    <div id="chart-trend"></div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Accountability</h3>
                    <div id="chart-accountability"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Assets by Category</h3>
                    <div id="chart-category"></div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Assets by Company</h3>
                    <div id="chart-company"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Recent assets --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Recent Assets</h3>
                        @can('view assets')
                            <a href="{{ route('assets.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View all →</a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <table class="elegant-table">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3">Asset Tag</th>
                                    <th class="px-4 py-3">Model</th>
                                    <th class="px-4 py-3">Assigned</th>
                                    <th class="px-4 py-3">Signed</th>
                                    <th class="px-4 py-3">Snipe-IT</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($recentAssets as $a)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                            @can('view assets')
                                                <a href="{{ route('assets.show', $a) }}" class="hover:underline">{{ $a->asset_tag }}</a>
                                            @else
                                                {{ $a->asset_tag }}
                                            @endcan
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->model?->name }}</td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->assigned_user ?? '—' }}</td>
                                        <td class="px-4 py-3"><x-status-badge :status="$a->accountability_signed" /></td>
                                        <td class="px-4 py-3"><x-status-badge :status="$a->accountability_uploaded_snipeit" /></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No assets yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Recent activity --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Recent Activity</h3>
                    </div>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($recentActivity as $log)
                            <li class="px-6 py-3 text-sm">
                                <p class="text-gray-800 dark:text-gray-200">
                                    <span class="font-medium">{{ $log->causer?->name ?? 'System' }}</span>
                                    {{ $log->description }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                            </li>
                        @empty
                            <li class="px-6 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">No activity recorded yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
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
                chart: { ...baseChart, type: 'area', height: 280 },
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
                chart: { ...baseChart, type: 'donut', height: 280 },
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
                chart: { ...baseChart, type: 'donut', height: 300 },
                series: @json($perCategory->filter(fn ($c) => $c->assets_count > 0)->pluck('assets_count')->values()),
                labels: @json($perCategory->filter(fn ($c) => $c->assets_count > 0)->pluck('name')->values()),
                colors: palette,
                legend: { position: 'bottom' },
                stroke: { width: 0 },
                plotOptions: { pie: { donut: { size: '68%' } } },
                dataLabels: { enabled: false },
                tooltip,
            }).render();

            // By company — bar
            new ApexCharts(document.querySelector('#chart-company'), {
                chart: { ...baseChart, type: 'bar', height: 300 },
                series: [{ name: 'Assets', data: @json($perCompany->filter(fn ($c) => $c->assets_count > 0)->pluck('assets_count')->values()) }],
                xaxis: { categories: @json($perCompany->filter(fn ($c) => $c->assets_count > 0)->pluck('name')->values()) },
                colors: ['#003049'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
                dataLabels: { enabled: false },
                grid: { borderColor: grid, strokeDashArray: 4 },
                tooltip,
            }).render();
        });
    </script>
    @endpush
</x-app-layout>
