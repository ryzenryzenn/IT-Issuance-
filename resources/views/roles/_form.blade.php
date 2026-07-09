@props(['role', 'groupedPermissions', 'rolePermissions', 'isProtected' => false])

@php $checked = collect(old('permissions', $rolePermissions)); @endphp

<div class="space-y-5">
    <div>
        <x-input-label for="name" value="Role Name *" />
        <x-text-input id="name" name="name" :value="old('name', $role->name)" required
                      class="mt-1 block w-full max-w-sm" @disabled($isProtected) />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    @if ($isProtected)
        <div class="rounded-md bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 p-3 text-sm text-amber-800 dark:text-amber-200">
            This is a protected role — it always keeps full access, so its permissions can't be changed.
        </div>
    @endif

    <div>
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($groupedPermissions as $group => $perms)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                     x-data="{ toggleAll(e) { this.$root.querySelectorAll('input.perm:not(:disabled)').forEach(c => c.checked = e.target.checked); } }">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100">{{ $group }}</h4>
                        @unless ($isProtected)
                            <label class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                                <input type="checkbox" @change="toggleAll($event)"
                                       class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                All
                            </label>
                        @endunless
                    </div>
                    <div class="space-y-1.5">
                        @foreach ($perms as $perm)
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                       class="perm rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500"
                                       @checked($isProtected || $checked->contains($perm->name)) @disabled($isProtected)>
                                {{ ucfirst($perm->name) }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('permissions')" class="mt-1" />
    </div>
</div>
