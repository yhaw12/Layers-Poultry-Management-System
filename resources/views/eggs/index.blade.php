@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Eggs</h1>
            <a href="{{ route('eggs.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">+ Add Eggs</a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6 shadow-sm">{{ session('success') }}</div>
        @endif

        <!-- Monthly Egg Crate Chart -->
        <div class="mb-8">
            <canvas id="eggChart" class="w-full h-64"></canvas>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Crates</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Date Collected</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Sold Quantity</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Sold Date</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Sale Price</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eggs as $egg)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-200">
                            <td class="p-4">{{ $egg->crates }}</td>
                            <td class="p-4">{{ $egg->date_laid->format('Y-m-d') }}</td>
                            <td class="p-4">{{ $egg->sold_quantity }}</td>
                            <td class="p-4">{{ $egg->sold_date ? $egg->sold_date->format('Y-m-d') : 'N/A' }}</td>
                            <td class="p-4">{{ $egg->sale_price ? '$' . number_format($egg->sale_price, 2) : 'N/A' }}</td>
                            <td class="p-4 flex space-x-3">
                                <a href="{{ route('eggs.edit', $egg) }}" class="text-green-600 hover:text-green-800 font-medium">Edit</a>
                                <form action="{{ route('eggs.destroy', $egg) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">No eggs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const eggCtx = document.getElementById('eggChart').getContext('2d');
    new Chart(eggCtx, {
        type: 'line',
        data: {
            labels: {{ json_encode($eggLabels) }},
            datasets: [{
                label: 'Egg Crates',
                data: {{ json_encode($eggData) }},
                fill: false,
                borderColor: '#10b981',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection