<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Trash / Restore</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Deleted records are kept here. <span class="font-medium">Restore</span> brings them back;
                <span class="font-medium">Delete forever</span> removes them permanently.
            </p>

            {{-- Type tabs --}}
            <div class="flex flex-wrap gap-2">
                @foreach ($types as $slug => $cfg)
                    <a href="{{ route('trash.index', ['type' => $slug]) }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm border transition
                              {{ $active === $slug
                                  ? 'bg-indigo-600 text-white border-indigo-600'
                                  : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        {{ $cfg['label'] }}
                        <span class="text-xs rounded-full px-1.5 {{ $active === $slug ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-700' }}">{{ $counts[$slug] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
                <table class="elegant-table">
                    <thead>
                        <tr>
                            <th>{{ $active === 'assets' ? 'Asset Tag' : 'Name' }}</th>
                            <th>Details</th>
                            <th>Deleted</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($items as $item)
                            <tr>
                                <td class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $active === 'assets' ? $item->asset_tag : $item->name }}
                                </td>
                                <td class="text-gray-600 dark:text-gray-300">
                                    @if ($active === 'assets')
                                        {{ $item->model?->name ?? '—' }}{{ $item->company ? ' · '.$item->company->name : '' }}
                                    @elseif ($active === 'users')
                                        {{ $item->email }}
                                    @else
                                        {{ $item->description ?? ($item->department ?? '—') }}
                                    @endif
                                </td>
                                <td class="text-gray-500 dark:text-gray-400 whitespace-nowrap" title="{{ $item->deleted_at }}">
                                    {{ $item->deleted_at?->diffForHumans() }}
                                </td>
                                <td class="text-right whitespace-nowrap space-x-2">
                                    @can('restore records')
                                        <form action="{{ route('trash.restore', ['type' => $active, 'id' => $item->id]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 dark:text-green-400 hover:underline text-sm">Restore</button>
                                        </form>
                                    @endcan
                                    @can('force delete records')
                                        <form action="{{ route('trash.force-delete', ['type' => $active, 'id' => $item->id]) }}" method="POST" class="inline"
                                              data-confirm="Permanently delete this record? This CANNOT be undone." data-confirm-button="Yes, delete forever">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">Delete forever</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">Nothing in the trash here. 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $items->links() }}</div>
        </div>
    </div>
</x-app-layout>
