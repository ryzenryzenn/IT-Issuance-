@props(['employee'])

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Full Name *" />
        <x-text-input id="name" name="name" :value="old('name', $employee->name)" required class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="employee_no" value="Employee No." />
        <x-text-input id="employee_no" name="employee_no" :value="old('employee_no', $employee->employee_no)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('employee_no')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="department" value="Department" />
        <x-text-input id="department" name="department" :value="old('department', $employee->department)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('department')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="position" value="Position" />
        <x-text-input id="position" name="position" :value="old('position', $employee->position)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('position')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" :value="old('email', $employee->email)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="is_active" value="Status" />
        @php $activeVal = old('is_active', $employee->is_active === false ? '0' : '1'); @endphp
        <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
            <option value="1" @selected($activeVal == '1')>Active</option>
            <option value="0" @selected($activeVal == '0')>Inactive</option>
        </select>
        <x-input-error :messages="$errors->get('is_active')" class="mt-1" />
    </div>
</div>
