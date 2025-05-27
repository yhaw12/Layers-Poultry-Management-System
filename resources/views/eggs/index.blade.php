@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Egg Records</h2>
        <a href="{{ route('eggs.create') }}"
            class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
            Add Egg Record
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" class="bg-white p-6 rounded shadow dark:bg-[#1a1a3a]">
        <div class="flex items-center gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by date or crates..."
                class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <button type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Search
            </button>
        </div>
    </form>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
            <h3 class="font-semibold text-gray-700 dark:text-gray-200">Total Crates</h3>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-4">{{ number_format($totalCrates, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
            <h3 class="font-semibold text-gray-700 dark:text-gray-200">Total Sold</h3>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-4">{{ number_format($totalSold, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
            <h3 class="font-semibold text-gray-700 dark:text-gray-200">Total Produced</h3>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-4">{{ number_format($totalProduced, 2) }}</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Monthly Egg Crates (Last 6 Months)</h3>
        <canvas id="eggChart" class="w-full h-64"></canvas>
    </div>

    <!-- Egg Records Table -->
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
        <form method="POST" action="{{ route('eggs.bulkDelete') }}" onsubmit="return confirm('Delete selected records?')">
            @csrf
            <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 mb-4"
                onclick="return confirm('Are you sure?')">Delete Selected</button>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-700 dark:text-gray-300">
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Date Laid</th>
                        <th>Crates</th>
                        <th>Sold Quantity</th>
                        <th>Sold Date</th>
                        <th>Sale Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eggs as $egg)
                        <tr class="border-t dark:border-gray-700">
                            <td><input type="checkbox" name="ids[]" value="{{ $egg->id }}"></td>
                            <td>{{ $egg->date_laid->format('Y-m-d') }}</td>
                            <td>{{ $egg->crates }}</td>
                            <td>{{ $egg->sold_quantity ?? 'N/A' }}</td>
                            <td>{{ $egg->sold_date ? $egg->sold_date->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $egg->sale_price ? number_format($egg->sale_price, 2) : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('eggs.edit', $egg) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                <form action="{{ route('eggs.destroy', $egg) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 dark:text-gray-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
        {{ $eggs->links() }}
    </div>
</div>

<!-- Chart.js (Local) -->
{{-- <script src="{{ asset('js/chart.min.js') }}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const eggChart = new Chart(document.getElementById('eggChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($eggLabels),
            datasets: [{
                label: 'Egg Crates',
                data: @json($eggData),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Select all checkboxes
    document.getElementById('select-all').addEventListener('change', function () {
        document.querySelectorAll('input[name="ids[]"]').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endsection


