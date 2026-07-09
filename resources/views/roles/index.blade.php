<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Roles &amp; Permissions</h2>
            @can('create roles')
                <a href="{{ route('roles.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm">+ New Role</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Control what each role can do. Users get their access from the role assigned to them on the Users page.
            </p>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
                <table class="elegant-table">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($roles as $role)
                            <tr>
                                <td class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $role->name }}
                                    @if ($role->name === 'Admin')
                                        <span class="ms-1 text-xs text-amber-600 dark:text-amber-400">(protected)</span>
                                    @endif
                                </td>
                                <td class="text-gray-700 dark:text-gray-300">{{ $role->users_count }}</td>
                                <td class="text-gray-700 dark:text-gray-300">{{ $role->permissions_count }}</td>
                                <td class="text-right">
                                    <x-actions-menu>
                                        @can('update roles')
                                            <a href="{{ route('roles.edit', $role) }}" class="menu-item">Edit</a>
                                        @endcan
                                        @can('delete roles')
                                            @if ($role->name !== 'Admin')
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" data-confirm="Delete the '{{ $role->name }}' role? This cannot be undone.">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="menu-item menu-item-danger">Delete</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </x-actions-menu>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No roles yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
