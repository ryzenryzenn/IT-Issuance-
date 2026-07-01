<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Assets</h2>
            @can('create assets')
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-asset-modal'))"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm">
                    + New Asset
                </button>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <form method="GET" action="{{ route('assets.index') }}"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tag, model, serial, user, location, RustDesk ID…"
                       class="md:col-span-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">

                <select name="company_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                    <option value="">All companies</option>
                    @foreach ($companies as $c)
                        <option value="{{ $c->id }}" @selected(request('company_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>

                <select name="category_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>

                <select name="accountability_signed" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                    <option value="">Signed: any</option>
                    <option value="yes"     @selected(request('accountability_signed') === 'yes')>Signed</option>
                    <option value="pending" @selected(request('accountability_signed') === 'pending')>Pending</option>
                </select>

                <select name="accountability_uploaded_snipeit" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                    <option value="">Snipe-IT: any</option>
                    <option value="yes"     @selected(request('accountability_uploaded_snipeit') === 'yes')>Uploaded</option>
                    <option value="pending" @selected(request('accountability_uploaded_snipeit') === 'pending')>Pending</option>
                </select>

                <div class="md:col-span-6 flex gap-2 justify-end">
                    <a href="{{ route('assets.index') }}"
                       class="px-3 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Reset
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">
                        Filter
                    </button>
                </div>
            </form>

            <div id="assets-table-scope" x-data="{ selected: [], pageIds: @js($assets->pluck('id')->values()) }">
                @can('delete assets')
                <form method="POST" action="{{ route('assets.bulk-destroy') }}" id="bulk-delete-form"
                      :data-confirm="'Delete ' + selected.length + ' selected asset(s)? An admin can restore them.'"
                      data-confirm-button="Yes, delete them">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400"><span x-text="selected.length">0</span> selected</span>
                        <button type="button" :disabled="selected.length === 0"
                                @click="window.open('{{ route('assets.transmittal') }}?ids=' + selected.join(','), '_blank')"
                                class="px-3 py-2 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-40 disabled:cursor-not-allowed">
                            Transmittal
                        </button>
                        <button type="button" :disabled="selected.length === 0"
                                @click="window.open('{{ route('assets.gate-pass') }}?ids=' + selected.join(','), '_blank')"
                                class="px-3 py-2 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-40 disabled:cursor-not-allowed">
                            Gate Pass
                        </button>
                        <button type="submit" :disabled="selected.length === 0"
                                class="px-3 py-2 text-sm rounded-md bg-red-600 hover:bg-red-700 text-white disabled:opacity-40 disabled:cursor-not-allowed">
                            Delete selected
                        </button>
                    </div>
                </form>
                @endcan

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
                <table class="elegant-table">
                    <thead>
                        <tr>
                            @can('delete assets')
                                <th class="px-4 py-3 w-8">
                                    <input type="checkbox" aria-label="Select all"
                                           @change="selected = $event.target.checked ? pageIds : []"
                                           :checked="pageIds.length > 0 && selected.length === pageIds.length"
                                           class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                </th>
                            @endcan
                            @php
                                $sortableBefore = ['asset_tag' => 'Asset Tag', 'asset_model' => 'Model'];
                                $sortableAfter  = ['location' => 'Location', 'date_issued' => 'Date Issued'];
                            @endphp
                            @foreach ($sortableBefore as $col => $label)
                                @php $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc'; @endphp
                                <th class="px-4 py-3">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir]) }}" class="hover:underline">
                                        {{ $label }} @if($sort === $col)<span>{{ $dir === 'asc' ? '▲' : '▼' }}</span>@endif
                                    </a>
                                </th>
                            @endforeach
                            <th class="px-4 py-3">Assigned</th>
                            @foreach ($sortableAfter as $col => $label)
                                @php $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc'; @endphp
                                <th class="px-4 py-3">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir]) }}" class="hover:underline">
                                        {{ $label }} @if($sort === $col)<span>{{ $dir === 'asc' ? '▲' : '▼' }}</span>@endif
                                    </a>
                                </th>
                            @endforeach
                            <th class="px-4 py-3">Serial</th>
                            <th class="px-4 py-3">Company</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Signed</th>
                            <th class="px-4 py-3">Snipe-IT</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assets-tbody" class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($assets as $a)
                            @include('assets._row', ['a' => $a])
                        @empty
                            <tr data-empty-row><td colspan="{{ auth()->user()->can('delete assets') ? 12 : 11 }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No assets match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>

            <div>{{ $assets->links() }}</div>
        </div>
    </div>

    {{-- Create Asset modal (AJAX) --}}
    @can('create assets')
        <div x-data="assetModal()" @open-asset-modal.window="openModal()" x-cloak>
            <div x-show="open" x-transition.opacity
                 class="fixed inset-0 z-50 overflow-y-auto bg-black/50 p-4 flex items-start justify-center"
                 @click.self="close()">
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">New Asset</h3>
                        <button type="button" @click="close()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl leading-none">&times;</button>
                    </div>

                    <form id="create-asset-form" @submit.prevent="submit($event)" class="px-6 py-4">
                        @csrf
                        <div x-show="Object.keys(errors).length"
                             class="mb-4 rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 p-3 text-sm text-red-700 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                <template x-for="(msgs, field) in errors" :key="field">
                                    <li x-text="msgs[0]"></li>
                                </template>
                            </ul>
                        </div>

                        @include('assets._form', ['asset' => new \App\Models\Asset(), 'companies' => $companies, 'categories' => $categories, 'models' => $models, 'locations' => $locations, 'employees' => $employees])

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="close()"
                                    class="px-4 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300">Cancel</button>
                            <button type="submit" :disabled="submitting"
                                    class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md disabled:opacity-50">
                                <span x-show="!submitting">Create Asset</span>
                                <span x-show="submitting">Saving…</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            function assetModal() {
                return {
                    open: false,
                    submitting: false,
                    errors: {},
                    openModal() { this.errors = {}; this.open = true; },
                    close() { this.open = false; },
                    async submit(e) {
                        this.submitting = true;
                        this.errors = {};
                        const form = e.target;

                        try {
                            const res = await fetch(@js(route('assets.store')), {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: new FormData(form),
                            });

                            if (res.status === 422) {
                                const json = await res.json();
                                this.errors = json.errors || {};
                                return;
                            }
                            if (!res.ok) throw new Error('Request failed (' + res.status + ')');

                            const json = await res.json();
                            const tbody = document.getElementById('assets-tbody');

                            const emptyRow = tbody.querySelector('[data-empty-row]');
                            if (emptyRow) emptyRow.remove();

                            tbody.insertAdjacentHTML('afterbegin', json.html);
                            const newRow = tbody.firstElementChild;
                            if (window.Alpine && window.Alpine.initTree) window.Alpine.initTree(newRow);

                            // keep "select all" in sync with the new row
                            const scope = document.getElementById('assets-table-scope');
                            if (scope && window.Alpine && window.Alpine.$data) {
                                const data = window.Alpine.$data(scope);
                                if (data && Array.isArray(data.pageIds)) data.pageIds.push(json.id);
                            }

                            form.reset();
                            this.close();
                            window.showToast('success', json.message || 'Asset created.');
                        } catch (err) {
                            window.showToast('error', 'Could not create the asset. Please try again.');
                        } finally {
                            this.submitting = false;
                        }
                    },
                };
            }
        </script>
        @endpush
    @endcan
</x-app-layout>
