<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Reports</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Asset Inventory Report</h3>

                <form method="GET" id="reportForm" class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div>
                        <x-input-label value="Company" />
                        <select name="company_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="">All</option>
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Category" />
                        <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="">All</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Accountability Signed" />
                        <select name="accountability_signed" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="">Any</option>
                            <option value="yes">Yes</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Snipe-IT Uploaded" />
                        <select name="accountability_uploaded_snipeit" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="">Any</option>
                            <option value="yes">Yes</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Date Issued From" />
                        <x-text-input type="date" name="from" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label value="Date Issued To" />
                        <x-text-input type="date" name="to" class="mt-1 block w-full" />
                    </div>
                </form>

                <div class="mt-6 flex flex-wrap gap-2">
                    <button type="button"
                            onclick="submitReport('{{ route('reports.assets.pdf') }}')"
                            class="px-4 py-2 text-sm rounded-md bg-red-600 hover:bg-red-700 text-white">
                        Download PDF
                    </button>
                    <button type="button"
                            onclick="submitReport('{{ route('reports.assets.excel') }}')"
                            class="px-4 py-2 text-sm rounded-md bg-green-600 hover:bg-green-700 text-white">
                        Download Excel
                    </button>
                    <button type="button"
                            onclick="submitReport('{{ route('reports.assets.csv') }}')"
                            class="px-4 py-2 text-sm rounded-md bg-blue-600 hover:bg-blue-700 text-white">
                        Download CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitReport(action) {
            const form = document.getElementById('reportForm');
            form.action = action;
            form.method = 'GET';
            form.submit();
        }
    </script>
</x-app-layout>
