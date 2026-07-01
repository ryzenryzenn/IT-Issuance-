<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <style>
            [x-cloak]{display:none !important;}

            /* Animated side panel */
            .bg-animated {
                background: linear-gradient(125deg, #00243a, #003049, #00405f, #002a40);
                background-size: 300% 300%;
                animation: gradientShift 18s ease infinite;
            }
            @keyframes gradientShift {
                0%   { background-position: 0% 50%; }
                50%  { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            @keyframes aurora  { 0%,100% { transform: translate(0,0) scale(1); }   50% { transform: translate(40px,-30px) scale(1.18); } }
            @keyframes aurora2 { 0%,100% { transform: translate(0,0) scale(1); }   50% { transform: translate(-32px,38px) scale(1.12); } }
            @keyframes floaty  { 0%,100% { transform: translateY(0); }             50% { transform: translateY(-16px); } }
            @keyframes drift   { 0% { background-position: 0 0; }                   100% { background-position: 240px 240px; } }
            @keyframes orbit    { from { transform: rotate(0deg); }   to { transform: rotate(360deg); } }
            @keyframes orbitRev { from { transform: rotate(0deg); }   to { transform: rotate(-360deg); } }
            @keyframes pulse-ring { 0%,100% { opacity: .25; } 50% { opacity: .55; } }
            .animate-aurora   { animation: aurora  14s ease-in-out infinite; }
            .animate-aurora2  { animation: aurora2 17s ease-in-out infinite; }
            .animate-floaty   { animation: floaty   6s ease-in-out infinite; }
            .animate-drift    { animation: drift   40s linear infinite; }
            .animate-orbit    { animation: orbit   32s linear infinite; }
            .animate-orbit-rev{ animation: orbitRev 32s linear infinite; }
            .animate-pulsering{ animation: pulse-ring 4s ease-in-out infinite; }

            @media (prefers-reduced-motion: reduce) {
                .bg-animated, .animate-aurora, .animate-aurora2, .animate-floaty,
                .animate-drift, .animate-orbit, .animate-orbit-rev, .animate-pulsering { animation: none; }
            }
        </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">

            {{-- Left: form --}}
            <div class="flex w-full lg:w-1/2 flex-col px-8 sm:px-16 py-10 bg-white">
                <a href="/" class="flex items-center gap-2">
                    <x-application-logo class="h-9 w-auto fill-current text-indigo-600" />
                    <span class="text-lg font-bold text-gray-800">IT Assets</span>
                </a>

                <div class="flex-1 flex flex-col justify-center">
                    <div class="w-full max-w-md mx-auto">
                        {{ $slot }}
                    </div>
                </div>

                <div class="text-xs text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }} &middot; IT Asset Issuance System
                </div>
            </div>

            {{-- Right: asset-themed illustration --}}
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-animated text-white">
                {{-- drifting dotted texture --}}
                <div class="absolute inset-0 opacity-20 animate-drift"
                     style="background-image: radial-gradient(circle, rgba(255,255,255,.35) 1px, transparent 1px); background-size: 24px 24px;"></div>
                {{-- drifting amber glows --}}
                <div class="absolute -top-28 -right-24 w-96 h-96 rounded-full bg-accent-400 opacity-20 blur-3xl animate-aurora"></div>
                <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-accent-400 opacity-10 blur-3xl animate-aurora2"></div>

                <div class="relative z-10 flex flex-col justify-center px-16 w-full">
                    <h2 class="text-4xl font-bold leading-tight">
                        Track every IT asset<br>in <span class="text-accent-400">one place.</span>
                    </h2>
                    <p class="mt-4 text-white/70 max-w-md">
                        Issue, transfer, and account for company assets — with QR tags, gate passes, reports, and a full audit trail.
                    </p>

                    {{-- orbiting asset icons (no labels) --}}
                    @php
                        $icons = [
                            'M4 6h16v9H4z M2 18h20',
                            'M3 5h18v11H3z M9 20h6 M12 16v4',
                            'M4 4h6v6H4z M14 4h6v6h-6z M4 14h6v6H4z M14 14h2v2h-2z M18 18h2v2h-2z M14 18h2v2h-2z',
                            'M5 13a10 10 0 0114 0 M8.5 16.5a5 5 0 017 0 M12 20h.01',
                            'M6 9V3h12v6 M6 18H4a2 2 0 01-2-2v-3a2 2 0 012-2h16a2 2 0 012 2v3a2 2 0 01-2 2h-2 M6 14h12v7H6z',
                        ];
                        $radius = 150;
                        $count  = count($icons);
                    @endphp
                    <div class="relative mx-auto mt-10" style="width: 360px; height: 360px;">
                        {{-- center hub --}}
                        <div class="absolute rounded-full bg-white/10 ring-1 ring-white/25 flex items-center justify-center animate-pulsering"
                             style="width: 120px; height: 120px; top: 120px; left: 120px;">
                            <x-application-logo class="h-12 w-auto fill-current text-accent-400" />
                        </div>

                        {{-- rotating ring + icons --}}
                        <div class="absolute inset-0 rounded-full border border-dashed border-white/25 animate-orbit">
                            @foreach ($icons as $i => $path)
                                @php $angle = $i * (360 / $count); @endphp
                                <div class="absolute top-1/2 left-1/2"
                                     style="transform: translate(-50%, -50%) rotate({{ $angle }}deg) translateY(-{{ $radius }}px);">
                                    <div style="transform: rotate(-{{ $angle }}deg);">
                                        <div class="animate-orbit-rev flex items-center justify-center w-14 h-14 rounded-full bg-accent-400 text-indigo-900 shadow-lg ring-4 ring-white/15">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
