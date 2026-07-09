<ul class="divide-y divide-gray-100 dark:divide-gray-700">
    @forelse ($recentActivity as $log)
        <li class="px-6 py-3 text-sm">
            <p class="text-gray-800 dark:text-gray-200">
                <span class="font-medium">{{ $log->causer?->name ?? 'System' }}</span>
                {{ $log->description }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
        </li>
    @empty
        <li class="px-6 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">No activity recorded yet.</li>
    @endforelse
</ul>
@if ($recentActivity->hasPages())
    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700" data-pagination>
        {{ $recentActivity->links() }}
    </div>
@endif
