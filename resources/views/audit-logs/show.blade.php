<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Audit Log #{{ $activity->id }}</h2>
            <a href="{{ route('audit-logs.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 text-sm space-y-2">
                <p><span class="text-gray-500 dark:text-gray-400">When:</span> {{ $activity->created_at->format('Y-m-d H:i:s') }}</p>
                <p><span class="text-gray-500 dark:text-gray-400">Causer:</span> {{ $activity->causer?->name ?? 'System' }} (#{{ $activity->causer_id ?? '—' }})</p>
                <p><span class="text-gray-500 dark:text-gray-400">Log name:</span> {{ $activity->log_name }}</p>
                <p><span class="text-gray-500 dark:text-gray-400">Event:</span> {{ $activity->event ?? '—' }}</p>
                <p><span class="text-gray-500 dark:text-gray-400">Subject:</span> {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</p>
                <p><span class="text-gray-500 dark:text-gray-400">Description:</span> {{ $activity->description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2">Before</h3>
                    <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($activity->properties['old'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2">After</h3>
                    <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($activity->properties['attributes'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
