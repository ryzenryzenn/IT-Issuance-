@props([
    'name',
    'employees',
    'locations',
    'selected' => null,            // e.g. "employee:5" or "location:3"
    'placeholder' => '— Unassigned —',
    'required' => false,
])

<select name="{{ $name }}" @required($required)
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm']) }}>
    <option value="">{{ $placeholder }}</option>
    <optgroup label="Employees">
        @foreach ($employees as $e)
            <option value="employee:{{ $e->id }}" @selected($selected === 'employee:'.$e->id)>{{ $e->name }}</option>
        @endforeach
    </optgroup>
    <optgroup label="Locations (shared / generic)">
        @foreach ($locations as $l)
            <option value="location:{{ $l->id }}" @selected($selected === 'location:'.$l->id)>{{ $l->name }}</option>
        @endforeach
    </optgroup>
</select>
