@props(['status'])

@php
    $isYes = $status === 'yes';
    $classes = $isYes
        ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 ring-1 ring-inset ring-green-600/20'
        : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium $classes"]) }}>
    {{ $isYes ? 'Yes' : 'Pending' }}
</span>
