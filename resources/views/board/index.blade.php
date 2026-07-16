<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Board — Tickets &amp; Notes</h2>
            @can('create tickets')
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-ticket-modal'))"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm">
                    + New Note
                </button>
            @endcan
        </div>
    </x-slot>

    {{-- where the drag-and-drop persists to --}}
    <meta name="board-move-url" content="{{ route('board.move') }}">

    @php
        $colorClasses = [
            'yellow' => 'bg-amber-100 border-amber-300 dark:bg-amber-900/40 dark:border-amber-700',
            'blue'   => 'bg-sky-100 border-sky-300 dark:bg-sky-900/40 dark:border-sky-700',
            'green'  => 'bg-emerald-100 border-emerald-300 dark:bg-emerald-900/40 dark:border-emerald-700',
            'pink'   => 'bg-pink-100 border-pink-300 dark:bg-pink-900/40 dark:border-pink-700',
            'purple' => 'bg-violet-100 border-violet-300 dark:bg-violet-900/40 dark:border-violet-700',
        ];
        $priorityClasses = [
            'high'   => 'bg-red-600 text-white',
            'normal' => 'bg-gray-500 text-white',
            'low'    => 'bg-gray-300 text-gray-700',
        ];
    @endphp

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                Drag notes between columns. Use them for <span class="font-medium">temporary asset issues</span> and
                <span class="font-medium">technical support tickets</span>.
            </p>

            <div class="flex gap-4 overflow-x-auto pb-3 items-start">
                @foreach ($boardColumns as $col)
                    @php $key = $col->key; @endphp
                    <div class="w-64 shrink-0 bg-gray-100 dark:bg-gray-900/40 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col">
                        <div class="px-4 py-3 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $col->name }}</h3>
                            <div class="flex items-center gap-1.5">
                                <span data-count-for="{{ $key }}"
                                      class="text-xs px-2 py-0.5 rounded-full bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                    {{ ($tickets[$key] ?? collect())->count() }}
                                </span>
                                @can('delete tickets')
                                    <form action="{{ route('board.columns.destroy', $col) }}" method="POST"
                                          data-confirm="Remove the '{{ $col->name }}' column? (It must be empty.)">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Remove column"
                                                class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 text-lg leading-none">&times;</button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                        {{-- droppable list --}}
                        <div data-kanban-list="{{ $key }}" class="p-2 space-y-2 min-h-[280px]">
                            @forelse ($tickets[$key] ?? [] as $t)
                                @php
                                    $payload = [
                                        'id'          => $t->id,
                                        'title'       => $t->title,
                                        'body'        => $t->body,
                                        'type'        => $t->type,
                                        'status'      => $t->status,
                                        'priority'    => $t->priority,
                                        'color'       => $t->color,
                                        'asset_id'    => $t->asset_id,
                                        'employee_id' => $t->employee_id,
                                        'due_date'    => optional($t->due_date)->toDateString(),
                                    ];
                                    $typeLabels = ['support' => 'Support', 'temp_issue' => 'Temp Issue', 'deployment' => 'Deployment'];
                                    $typeBorder = ['support' => 'border-l-gray-400', 'temp_issue' => 'border-l-indigo-500', 'deployment' => 'border-l-teal-500'][$t->type] ?? 'border-l-gray-400';
                                    $priorityDot = ['high' => 'bg-red-500', 'normal' => 'bg-gray-400', 'low' => 'bg-gray-300'][$t->priority] ?? 'bg-gray-400';
                                    $cardTitle = ($typeLabels[$t->type] ?? 'Support').' · '.ucfirst($t->priority).($t->employee ? ' · '.$t->employee->name : '');
                                @endphp
                                @php
                                    $typeChip = ['support' => 'bg-gray-700 text-white', 'temp_issue' => 'bg-indigo-600 text-white', 'deployment' => 'bg-teal-600 text-white'][$t->type] ?? 'bg-gray-700 text-white';
                                    $columnName = optional($boardColumns->firstWhere('key', $t->status))->name ?? $t->status;
                                @endphp
                                <div data-ticket-id="{{ $t->id }}"
                                     x-data="{ show: false, x: 0, y: 0,
                                        place(el) {
                                            const r = el.getBoundingClientRect(), w = 288, gap = 8;
                                            let x = r.right + gap;
                                            if (x + w > window.innerWidth) x = Math.max(gap, r.left - w - gap);
                                            this.x = x;
                                            this.y = Math.min(r.top, window.innerHeight - 240);
                                        } }"
                                     @mouseenter="place($event.currentTarget); show = true"
                                     @mouseleave="show = false"
                                     class="group relative rounded-md border border-l-4 {{ $typeBorder }} shadow-sm px-2.5 py-2 cursor-grab active:cursor-grabbing {{ $colorClasses[$t->color] ?? $colorClasses['yellow'] }}">
                                    <div class="flex items-start justify-between gap-1.5">
                                        <p class="text-xs font-medium text-gray-900 dark:text-gray-100 leading-snug break-words line-clamp-2">{{ $t->title }}</p>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <span class="w-2 h-2 rounded-full {{ $priorityDot }}"></span>
                                            @if (Auth::user()->can('update', $t) || Auth::user()->can('delete', $t))
                                                <div class="opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition">
                                                    <x-actions-menu>
                                                        @can('update', $t)
                                                            <button type="button" class="menu-item"
                                                                    data-ticket="{{ json_encode($payload) }}"
                                                                    onclick="window.dispatchEvent(new CustomEvent('edit-ticket', { detail: JSON.parse(this.dataset.ticket) }))">Edit</button>
                                                        @endcan
                                                        @can('delete', $t)
                                                            <form action="{{ route('board.destroy', $t) }}" method="POST" data-confirm="Delete this note?">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="menu-item menu-item-danger">Delete</button>
                                                            </form>
                                                        @endcan
                                                    </x-actions-menu>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($t->asset || $t->due_date)
                                        <div class="mt-1.5 flex items-center flex-wrap gap-2 text-[10px]">
                                            @if ($t->asset)
                                                <span class="px-1 py-0.5 rounded bg-white/70 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-300/70 dark:border-gray-600">{{ $t->asset->asset_tag }}</span>
                                            @endif
                                            @if ($t->due_date)
                                                <span class="{{ $t->isOverdue() ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                                                    {{ $t->isOverdue() ? '⚠ ' : '' }}{{ $t->due_date->format('M j') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Hover: full details popover --}}
                                    <template x-teleport="body">
                                        <div x-show="show" x-cloak x-transition.opacity.duration.100ms
                                             :style="`position:fixed; top:${y}px; left:${x}px; width:18rem;`"
                                             class="z-50 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl p-3 text-xs pointer-events-none">
                                            <p class="font-semibold text-sm text-gray-900 dark:text-gray-100 break-words">{{ $t->title }}</p>
                                            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded {{ $typeChip }}">{{ $typeLabels[$t->type] ?? 'Support' }}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded {{ $priorityClasses[$t->priority] }}">{{ ucfirst($t->priority) }} priority</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $columnName }}</span>
                                            </div>
                                            @if ($t->body)
                                                <p class="mt-2 text-gray-600 dark:text-gray-300 whitespace-pre-wrap break-words">{{ $t->body }}</p>
                                            @endif
                                            <dl class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 space-y-1 text-gray-500 dark:text-gray-400">
                                                @if ($t->asset)
                                                    <div class="flex justify-between gap-2"><dt>Asset</dt><dd class="text-gray-700 dark:text-gray-200">{{ $t->asset->asset_tag }}</dd></div>
                                                @endif
                                                @if ($t->employee)
                                                    <div class="flex justify-between gap-2"><dt>Person</dt><dd class="text-gray-700 dark:text-gray-200">{{ $t->employee->name }}</dd></div>
                                                @endif
                                                @if ($t->due_date)
                                                    <div class="flex justify-between gap-2"><dt>Due / return</dt><dd class="{{ $t->isOverdue() ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-700 dark:text-gray-200' }}">{{ $t->due_date->format('M j, Y') }}</dd></div>
                                                @endif
                                                <div class="flex justify-between gap-2"><dt>Added by</dt><dd class="text-gray-700 dark:text-gray-200">{{ $t->creator?->name ?? '—' }}</dd></div>
                                                <div class="flex justify-between gap-2"><dt>Created</dt><dd class="text-gray-700 dark:text-gray-200">{{ $t->created_at->format('M j, Y') }}</dd></div>
                                            </dl>
                                        </div>
                                    </template>
                                </div>
                            @empty
                                <p class="text-xs text-center text-gray-400 dark:text-gray-500 py-6">Drop notes here</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach

                {{-- Add a new status column --}}
                @can('create tickets')
                    <div class="w-64 shrink-0" x-data="{ open: false }">
                        <button type="button" x-show="!open" @click="open = true"
                                class="w-full rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 py-3 text-sm font-medium">
                            + Add Board
                        </button>
                        <form x-show="open" x-cloak method="POST" action="{{ route('board.columns.store') }}"
                              class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3 space-y-2">
                            @csrf
                            <input type="text" name="name" maxlength="50" required placeholder="Column name (status)"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false"
                                        class="px-3 py-1.5 text-xs rounded-md border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300">Cancel</button>
                                <button type="submit" class="px-3 py-1.5 text-xs rounded-md bg-indigo-600 hover:bg-indigo-700 text-white">Add</button>
                            </div>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- Create / Edit modal --}}
    @can('create tickets')
        @include('board._modal', ['assets' => $assets, 'employees' => $employees, 'boardColumns' => $boardColumns])
    @endcan
</x-app-layout>
