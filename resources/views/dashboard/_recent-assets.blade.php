<div class="overflow-x-auto">
    <table class="elegant-table">
        <thead>
            <tr>
                <th class="px-4 py-3">Asset Tag</th>
                <th class="px-4 py-3">Model</th>
                <th class="px-4 py-3">Assigned</th>
                <th class="px-4 py-3">Signed</th>
                <th class="px-4 py-3">Snipe-IT</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($recentAssets as $a)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                        @can('view assets')
                            <a href="{{ route('assets.index') }}" class="hover:underline">{{ $a->asset_tag }}</a>
                        @else
                            {{ $a->asset_tag }}
                        @endcan
                    </td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->model?->name }}</td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->assignee?->name ?? '—' }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$a->accountability_signed" /></td>
                    <td class="px-4 py-3"><x-status-badge :status="$a->accountability_uploaded_snipeit" /></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No assets yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if ($recentAssets->hasPages())
    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700" data-pagination>
        {{ $recentAssets->links() }}
    </div>
@endif
