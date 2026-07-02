<tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
    @can('delete assets')
        <td class="px-4 py-3">
            <input type="checkbox" value="{{ $a->id }}" x-model.number="selected"
                   aria-label="Select asset {{ $a->asset_tag }}"
                   class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
        </td>
    @endcan
    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
        <a href="{{ route('assets.show', $a) }}" class="hover:underline">{{ $a->asset_tag }}</a>
    </td>
    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->model?->name ?? '—' }}</td>
    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
        {{ $a->assignee?->name ?? '—' }}
        @if ($a->assignee_type === 'location')<span class="text-xs text-gray-400">(shared)</span>@endif
    </td>
    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->location?->name ?? '—' }}</td>
    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ optional($a->date_issued)->format('Y-m-d') ?? '—' }}</td>
    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->serial_number ?? '—' }}</td>
    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->company?->name }}</td>
    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $a->category?->name }}</td>
    <td class="px-4 py-3"><x-status-badge :status="$a->accountability_signed" /></td>
    <td class="px-4 py-3"><x-status-badge :status="$a->accountability_uploaded_snipeit" /></td>
    <td class="px-4 py-3 text-right">
        <x-actions-menu>
            <a href="{{ route('assets.show', $a) }}" class="menu-item">View</a>
            <a href="{{ route('assets.transmittal', ['ids' => $a->id]) }}" target="_blank" class="menu-item">Transmittal</a>
            <a href="{{ route('assets.gate-pass', ['ids' => $a->id]) }}" target="_blank" class="menu-item">Gate Pass</a>
            @can('update', $a)
                <a href="{{ route('assets.edit', $a) }}" class="menu-item">Edit</a>
            @endcan
            @can('delete', $a)
                <form action="{{ route('assets.destroy', $a) }}" method="POST"
                      data-confirm="Delete this asset? An admin can restore it.">
                    @csrf @method('DELETE')
                    <button type="submit" class="menu-item menu-item-danger">Delete</button>
                </form>
            @endcan
        </x-actions-menu>
    </td>
</tr>
