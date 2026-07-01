<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Apply theme before paint to avoid flash -->
        <script>
            (function() {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Scripts -->
        <style>[x-cloak]{display:none !important;}</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <div class="lg:pl-64">
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @if (session('success') || session('error') || session('status'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        @if (session('success'))
                            window.showToast('success', @json(session('success')));
                        @endif
                        @if (session('error'))
                            window.showToast('error', @json(session('error')));
                        @endif
                        @if (session('status'))
                            window.showToast('info', @json(session('status')));
                        @endif
                    });
                </script>
            @endif

            <main>
                {{ $slot }}
            </main>
            </div>
        </div>

        @stack('scripts')

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggle = document.getElementById('theme-toggle');
                const darkIcon = document.getElementById('theme-toggle-dark-icon');
                const lightIcon = document.getElementById('theme-toggle-light-icon');

                function paint() {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (darkIcon)  darkIcon.classList.toggle('hidden', isDark);
                    if (lightIcon) lightIcon.classList.toggle('hidden', !isDark);
                }
                paint();

                if (toggle) {
                    toggle.addEventListener('click', function() {
                        const isDark = document.documentElement.classList.toggle('dark');
                        localStorage.setItem('theme', isDark ? 'dark' : 'light');
                        paint();
                    });
                }
            });
        </script>
    </body>
</html>
