<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Employees</h2>
            @can('create employees')
                <a href="{{ route('employees.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm">+ New Employee</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, no., department, position, email…"
                       class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <button class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Search</button>
            </form>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
                <table class="elegant-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Employee No.</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Assets</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employees as $e)
                            <tr>
                                <td class="font-medium text-gray-900 dark:text-gray-100">{{ $e->name }}</td>
                                <td class="text-gray-700 dark:text-gray-300">{{ $e->employee_no ?? '—' }}</td>
                                <td class="text-gray-700 dark:text-gray-300">{{ $e->department ?? '—' }}</td>
                                <td class="text-gray-700 dark:text-gray-300">{{ $e->position ?? '—' }}</td>
                                <td>
                                    @if ($e->is_active)
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-gray-700 dark:text-gray-300">{{ $e->assets_count }}</td>
                                <td class="text-right">
                                    @if (Auth::user()->can('update', $e) || Auth::user()->can('delete', $e))
                                        <x-actions-menu>
                                            @can('update', $e)
                                                <a href="{{ route('employees.edit', $e) }}" class="menu-item">Edit</a>
                                            @endcan
                                            @can('delete', $e)
                                                <form action="{{ route('employees.destroy', $e) }}" method="POST" data-confirm="Delete this employee? This cannot be undone.">
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
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No employees yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $employees->links() }}</div>
        </div>
    </div>
</x-app-layout>
