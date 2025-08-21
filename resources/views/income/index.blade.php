@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 ">
    <div class=" p-6 rounded-lg shadow-md bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Income</h1>
            <a href="{{ route('income.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">+ Add Income</a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6 shadow-sm">{{ session('success') }}</div>
        @endif

        <!-- Monthly Income Chart -->
        <div class="mb-8  ">
            <canvas id="incomeChart" class="w-full h-64"></canvas>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Source</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Amount</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Date</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($income as $income)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-200">
                            <td class="p-4">{{ $income->source }}</td>
                            <td class="p-4 text-gray-800 font-medium">${{ number_format($income->amount, 2) }}</td>
                            <td class="p-4">{{ $income->date->format('Y-m-d') }}</td>
                            <td class="p-4 flex space-x-3">
                                <a href="{{ route('income.edit', $income) }}" class="text-green-600 hover:text-green-800 font-medium">Edit</a>
                                <form action="{{ route('income.destroy', $income) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">No income found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    new Chart(incomeCtx, {
        type: 'line',
        data: {
            labels: {{ json_encode($incomeLabels) }},
            datasets: [{
                label: 'Income ($)',
                data: {{ json_encode($incomeData) }},
                fill: false,
                borderColor: '#22c55e',
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