@props(['asset', 'companies', 'categories', 'models', 'locations'])

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="company_id" value="Company *" />
        <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" required>
            <option value="">— Select —</option>
            @foreach ($companies as $c)
                <option value="{{ $c->id }}" @selected(old('company_id', $asset->company_id) == $c->id)>{{ $c->name }} ({{ $c->code }})</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('company_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="category_id" value="Category *" />
        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" required>
            <option value="">— Select —</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $asset->category_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="asset_tag" value="Asset Tag *" />
        <x-text-input id="asset_tag" name="asset_tag" :value="old('asset_tag', $asset->asset_tag)" required class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('asset_tag')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="model_id" value="Asset Model *" />
        <select id="model_id" name="model_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" required>
            <option value="">— Select —</option>
            @foreach ($models as $m)
                <option value="{{ $m->id }}" @selected(old('model_id', $asset->model_id) == $m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('model_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="serial_number" value="Serial Number" />
        <x-text-input id="serial_number" name="serial_number" :value="old('serial_number', $asset->serial_number)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('serial_number')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="assigned_user" value="Assigned User" />
        <x-text-input id="assigned_user" name="assigned_user" :value="old('assigned_user', $asset->assigned_user)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('assigned_user')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="location_id" value="Location" />
        <select id="location_id" name="location_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
            <option value="">— None —</option>
            @foreach ($locations as $l)
                <option value="{{ $l->id }}" @selected(old('location_id', $asset->location_id) == $l->id)>{{ $l->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('location_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="rustdesk_id" value="RustDesk ID" />
        <x-text-input id="rustdesk_id" name="rustdesk_id" :value="old('rustdesk_id', $asset->rustdesk_id)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('rustdesk_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="windows_license_key" value="Windows 11 Pro License Key (encrypted at rest)" />
        <x-text-input id="windows_license_key" name="windows_license_key" :value="old('windows_license_key', $asset->windows_license_key)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('windows_license_key')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="accountability_signed" value="Accountability Signed *" />
        <select id="accountability_signed" name="accountability_signed" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
            <option value="pending" @selected(old('accountability_signed', $asset->accountability_signed) === 'pending')>Pending</option>
            <option value="yes"     @selected(old('accountability_signed', $asset->accountability_signed) === 'yes')>Yes</option>
        </select>
        <x-input-error :messages="$errors->get('accountability_signed')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="accountability_uploaded_snipeit" value="Accountability Uploaded to Snipe-IT *" />
        <select id="accountability_uploaded_snipeit" name="accountability_uploaded_snipeit" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
            <option value="pending" @selected(old('accountability_uploaded_snipeit', $asset->accountability_uploaded_snipeit) === 'pending')>Pending</option>
            <option value="yes"     @selected(old('accountability_uploaded_snipeit', $asset->accountability_uploaded_snipeit) === 'yes')>Yes</option>
        </select>
        <x-input-error :messages="$errors->get('accountability_uploaded_snipeit')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="date_issued" value="Date Issued" />
        <x-text-input type="date" id="date_issued" name="date_issued" :value="old('date_issued', optional($asset->date_issued)->format('Y-m-d'))" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('date_issued')" class="mt-1" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="latest_updates_remarks" value="Latest Updates / Remarks" />
    <textarea id="latest_updates_remarks" name="latest_updates_remarks" rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('latest_updates_remarks', $asset->latest_updates_remarks) }}</textarea>
    <x-input-error :messages="$errors->get('latest_updates_remarks')" class="mt-1" />
</div>
