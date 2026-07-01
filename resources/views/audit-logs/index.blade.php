<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Audit Logs</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4 grid grid-cols-1 md:grid-cols-4 gap-2">
                <select name="log_name" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                    <option value="">All log names</option>
                    @foreach ($logNames as $ln)
                        <option value="{{ $ln }}" @selected(request('log_name') === $ln)>{{ $ln }}</option>
                    @endforeach
                </select>
                <select name="event" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                    <option value="">All events</option>
                    @foreach ($events as $ev)
                        <option value="{{ $ev }}" @selected(request('event') === $ev)>{{ $ev }}</option>
                    @endforeach
                </select>
                <input type="number" name="causer_id" value="{{ request('causer_id') }}" placeholder="Causer user ID"
                       class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <div class="flex gap-2 justify-end">
                    <a href="{{ route('audit-logs.index') }}" class="px-3 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300">Reset</a>
                    <button class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Filter</button>
                </div>
            </form>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
                <table class="elegant-table">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">When</th>
                            <th class="px-4 py-3">Causer</th>
                            <th class="px-4 py-3">Log</th>
                            <th class="px-4 py-3">Event</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($activities as $a)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $a->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->causer?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->log_name }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->event }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ class_basename($a->subject_type) }} #{{ $a->subject_id }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->description }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-actions-menu>
                                        <a href="{{ route('audit-logs.show', $a) }}" class="menu-item">View details</a>
                                    </x-actions-menu>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No activity recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $activities->links() }}</div>
        </div>
    </div>
</x-app-layout>
