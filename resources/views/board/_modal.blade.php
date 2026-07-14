@props(['assets', 'employees', 'columns'])

<div x-data="ticketModal()"
     @open-ticket-modal.window="openCreate()"
     @edit-ticket.window="openEdit($event.detail)"
     x-cloak>
    <div x-show="open" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 p-3 sm:p-4 flex items-center justify-center"
         @click.self="close()">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg max-h-[90dvh] flex flex-col">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-5 py-3 shrink-0">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100" x-text="editing ? 'Edit Note' : 'New Note'"></h3>
                <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl leading-none">&times;</button>
            </div>

            <form :action="action" method="POST" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
                    <div>
                        <x-input-label for="t_title" value="Title *" />
                        <x-text-input id="t_title" name="title" x-model="form.title" required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="t_body" value="Details" />
                        <textarea id="t_body" name="body" x-model="form.body" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="t_type" value="Type *" />
                            <select id="t_type" name="type" x-model="form.type"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <option value="support">Support / Ticket</option>
                                <option value="temp_issue">Temporary Asset Issue</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="t_status" value="Action" />
                            <select id="t_status" name="status" x-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                @foreach ($columns as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="t_priority" value="Priority *" />
                            <select id="t_priority" name="priority" x-model="form.priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="t_due" value="Due / Return date" />
                            <x-text-input type="date" id="t_due" name="due_date" x-model="form.due_date" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Note colour" />
                        <input type="hidden" name="color" :value="form.color">
                        <div class="mt-1 flex gap-2">
                            @php
                                $swatches = [
                                    'yellow' => 'bg-amber-200',
                                    'blue'   => 'bg-sky-200',
                                    'green'  => 'bg-emerald-200',
                                    'pink'   => 'bg-pink-200',
                                    'purple' => 'bg-violet-200',
                                ];
                            @endphp
                            @foreach ($swatches as $key => $cls)
                                <button type="button" @click="form.color = '{{ $key }}'"
                                        :class="form.color === '{{ $key }}' ? 'ring-2 ring-offset-1 ring-indigo-600' : ''"
                                        class="w-7 h-7 rounded-full border border-gray-300 {{ $cls }}"
                                        title="{{ ucfirst($key) }}"></button>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="t_asset" value="Related asset" />
                            <select id="t_asset" name="asset_id" x-model="form.asset_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <option value="">— None —</option>
                                @foreach ($assets as $a)
                                    <option value="{{ $a->id }}">{{ $a->asset_tag }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="t_emp" value="Person" />
                            <select id="t_emp" name="employee_id" x-model="form.employee_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <option value="">— None —</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 border-t border-gray-100 dark:border-gray-700 px-5 py-3 flex justify-end gap-2">
                    <button type="button" @click="close()"
                            class="px-4 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md"
                            x-text="editing ? 'Save Changes' : 'Add Note'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function ticketModal() {
        const blank = { id: null, title: '', body: '', type: 'support', status: 'todo', priority: 'normal', color: 'yellow', asset_id: '', employee_id: '', due_date: '' };
        return {
            open: false,
            editing: false,
            action: @js(route('board.store')),
            form: { ...blank },
            openCreate() {
                this.editing = false;
                this.action = @js(route('board.store'));
                this.form = { ...blank };
                this.open = true;
            },
            openEdit(t) {
                this.editing = true;
                this.action = @js(url('board')) + '/' + t.id;
                this.form = {
                    id: t.id,
                    title: t.title ?? '',
                    body: t.body ?? '',
                    type: t.type ?? 'support',
                    status: t.status ?? 'todo',
                    priority: t.priority ?? 'normal',
                    color: t.color ?? 'yellow',
                    asset_id: t.asset_id ?? '',
                    employee_id: t.employee_id ?? '',
                    due_date: t.due_date ?? '',
                };
                this.open = true;
            },
            close() { this.open = false; },
        };
    }
</script>
@endpush
