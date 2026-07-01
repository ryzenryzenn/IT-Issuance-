<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Asset — {{ $asset->asset_tag }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('assets.update', $asset) }}"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                @csrf @method('PUT')
                @include('assets._form', ['asset' => $asset, 'companies' => $companies, 'categories' => $categories, 'models' => $models, 'locations' => $locations, 'employees' => $employees])
                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('assets.show', $asset) }}" class="px-4 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300">Cancel</a>
                    <button class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
