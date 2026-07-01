<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Asset — {{ $asset->asset_tag }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('assets.transmittal', ['ids' => $asset->id]) }}" target="_blank"
                   class="px-3 py-2 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white">Transmittal</a>
                <a href="{{ route('assets.gate-pass', ['ids' => $asset->id]) }}" target="_blank"
                   class="px-3 py-2 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white">Gate Pass</a>
                @can('update', $asset)
                    <a href="{{ route('assets.edit', $asset) }}"
                       class="px-3 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Edit</a>
                @endcan
                <a href="{{ route('assets.index') }}"
                   class="px-3 py-2 text-sm rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    @php
                        $fields = [
                            'Company'                 => $asset->company?->name,
                            'Category'                => $asset->category?->name,
                            'Asset Tag'               => $asset->asset_tag,
                            'Asset Model'             => $asset->model?->name ?? '—',
                            'Serial Number'           => $asset->serial_number ?? '—',
                            'Assigned User'           => $asset->assigned_user ?? '—',
                            'Location'                => $asset->location?->name ?? '—',
                            'RustDesk ID'             => $asset->rustdesk_id ?? '—',
                            'Windows License Key'     => $asset->windows_license_key ?? '—',
                            'Date Issued'             => optional($asset->date_issued)->format('Y-m-d') ?? '—',
                            'Created At'              => $asset->created_at?->format('Y-m-d H:i'),
                            'Updated At'              => $asset->updated_at?->format('Y-m-d H:i'),
                        ];
                    @endphp
                    @foreach ($fields as $label => $value)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100 break-words">{{ $value }}</dd>
                        </div>
                    @endforeach
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Accountability Signed</dt>
                        <dd class="mt-1"><x-status-badge :status="$asset->accountability_signed" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Uploaded to Snipe-IT</dt>
                        <dd class="mt-1"><x-status-badge :status="$asset->accountability_uploaded_snipeit" /></dd>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="text-gray-500 dark:text-gray-400">Latest Updates / Remarks</dt>
                        <dd class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $asset->latest_updates_remarks ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- QR Code --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Asset QR Code</h3>
                    <a href="{{ route('assets.label', $asset) }}" target="_blank"
                       class="px-3 py-2 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white">Print Label</a>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="bg-white p-2 rounded-md border border-gray-200 dark:border-gray-600">
                        {!! $qrSvg !!}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        <p class="mb-2">Scanning this code shows:</p>
                        <ul class="space-y-1">
                            <li><span class="text-gray-500 dark:text-gray-400">Model:</span> {{ $asset->model?->name ?: 'N/A' }}</li>
                            <li><span class="text-gray-500 dark:text-gray-400">Serial:</span> {{ $asset->serial_number ?: 'N/A' }}</li>
                            <li><span class="text-gray-500 dark:text-gray-400">Assignee:</span> {{ $asset->assigned_user ?: 'Unassigned' }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Transfer --}}
                @can('transfer', $asset)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Transfer / Reassign</h3>
                        <form method="POST" action="{{ route('assets.transfer', $asset) }}" class="space-y-3">
                            @csrf
                            <div>
                                <x-input-label for="to_user" value="Transfer to (new assigned user) *" />
                                <x-text-input id="to_user" name="to_user" required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('to_user')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="to_location_id" value="New location" />
                                <select id="to_location_id" name="to_location_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                    <option value="">— Keep current —</option>
                                    @foreach ($locations as $l)
                                        <option value="{{ $l->id }}" @selected(old('to_location_id', $asset->location_id) == $l->id)>{{ $l->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('to_location_id')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="transferred_at" value="Transfer date *" />
                                <x-text-input type="date" id="transferred_at" name="transferred_at" :value="now()->toDateString()" required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('transferred_at')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="notes" value="Notes" />
                                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm"></textarea>
                            </div>
                            <button class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Transfer Asset</button>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Accountability status will be reset to pending after transfer.</p>
                        </form>
                    </div>
                @endcan

                {{-- Accountability files --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Accountability Files</h3>
                    @can('uploadAccountability', $asset)
                        <form method="POST" action="{{ route('assets.files.store', $asset) }}" enctype="multipart/form-data" class="mb-4 space-y-2">
                            @csrf
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-300">
                            <x-input-error :messages="$errors->get('file')" class="mt-1" />
                            <button class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Upload</button>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Allowed: PDF, DOCX, JPG, PNG. Max 10 MB.</p>
                        </form>
                    @endcan
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        @forelse ($asset->accountabilityFiles as $f)
                            <li class="py-2 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('assets.files.download', [$asset, $f]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $f->original_filename }}</a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $f->human_size }} · {{ $f->created_at->diffForHumans() }} · by {{ $f->uploadedBy?->name ?? 'Unknown' }}</p>
                                </div>
                                @can('delete accountability files')
                                    <form action="{{ route('assets.files.destroy', [$asset, $f]) }}" method="POST" data-confirm="Remove this accountability file?" data-confirm-button="Yes, remove it">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 dark:text-red-400 hover:underline text-xs">Remove</button>
                                    </form>
                                @endcan
                            </li>
                        @empty
                            <li class="py-4 text-center text-gray-500 dark:text-gray-400">No files uploaded yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Transfer history --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Transfer History</h3>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    @forelse ($asset->transfers as $t)
                        <li class="px-6 py-3">
                            <p class="text-gray-800 dark:text-gray-100">
                                <span class="font-medium">{{ $t->from_user ?? '—' }}</span> → <span class="font-medium">{{ $t->to_user }}</span>
                                @if ($t->from_location || $t->to_location)
                                    · <span class="text-gray-500">{{ $t->from_location ?? '—' }} → {{ $t->to_location ?? '—' }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $t->transferred_at->format('Y-m-d') }} · by {{ $t->transferredBy?->name ?? 'Unknown' }}
                                @if ($t->notes) · {{ $t->notes }} @endif
                            </p>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">No transfers yet.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Audit trail for this asset --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Audit Trail</h3>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    @forelse ($activities as $a)
                        <li class="px-6 py-3">
                            <p class="text-gray-800 dark:text-gray-100">
                                <span class="font-medium">{{ $a->causer?->name ?? 'System' }}</span>
                                <span class="text-gray-500">{{ $a->event ?? $a->description }}</span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $a->created_at->format('Y-m-d H:i') }}</p>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">No activity recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
