<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Company</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('companies.store') }}"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                @csrf
                <div>
                    <x-input-label for="name" value="Name *" />
                    <x-text-input id="name" name="name" :value="old('name')" required class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="code" value="Code *" />
                    <x-text-input id="code" name="code" :value="old('code')" required class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('code')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="address" value="Address" />
                    <x-text-input id="address" name="address" :value="old('address')" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('companies.index') }}" class="px-4 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300">Cancel</a>
                    <button class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
