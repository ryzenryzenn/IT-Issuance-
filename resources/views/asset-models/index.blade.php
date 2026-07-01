<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Asset Models</h2>
            @can('create asset models')
                <a href="{{ route('asset-models.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm">+ New Model</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search…"
                       class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <button class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Search</button>
            </form>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
                <table class="elegant-table">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Assets</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($models as $m)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $m->name }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $m->description ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $m->assets_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if (Auth::user()->can('update', $m) || Auth::user()->can('delete', $m))
                                        <x-actions-menu>
                                            @can('update', $m)
                                                <a href="{{ route('asset-models.edit', $m) }}" class="menu-item">Edit</a>
                                            @endcan
                                            @can('delete', $m)
                                                <form action="{{ route('asset-models.destroy', $m) }}" method="POST" data-confirm="Delete this model? This cannot be undone.">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="menu-item menu-item-danger">Delete</button>
                                                </form>
                                            @endcan
                                        </x-actions-menu>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No models yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $models->links() }}</div>
        </div>
    </div>
</x-app-layout>
