@props(['href', 'active' => false])

@php
    $base = 'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition';
    $state = $active
        ? 'bg-accent-400 text-indigo-900 font-semibold shadow-sm'
        : 'text-gray-600 dark:text-gray-300 hover:bg-accent-50 dark:hover:bg-gray-700 hover:text-indigo-700 dark:hover:text-white';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base.' '.$state]) }}>
    {{ $slot }}
</a>
