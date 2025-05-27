@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23]">
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-lg shadow-md">
        <!-- Header and Add Button -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Expenses</h1>
            <a href="{{ route('expenses.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-semibold dark:hover:bg-blue-500">+ Add Expense</a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 p-4 rounded-md mb-6 shadow-sm">{{ session('success') }}</div>
        @endif

        <!-- Expense Comparison Chart -->
        <div class="mb-8">
            <canvas id="expenseChart" class="w-full h-64"></canvas>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 dark:bg-[#2a2a4a]">
                    <tr>
                        <th class="p-4 text-gray-700 dark:text-gray-300 font-semibold uppercase">Category</th>
                        <th class="p-4 text-gray-700 dark:text-gray-300 font-semibold uppercase">Amount</th>
                        <th class="p-4 text-gray-700 dark:text-gray-300 font-semibold uppercase">Date</th>
                        <th class="p-4 text-gray-700 dark:text-gray-300 font-semibold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#2a2a4a] transition-colors duration-150 border-b border-gray-200 dark:border-gray-700">
                            <td class="p-4 text-gray-800 dark:text-gray-200">{{ $expense->category }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-200 font-medium">${{ number_format($expense->amount, 2) }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-200">{{ $expense->date->format('Y-m-d') }}</td>
                            <td class="p-4 flex space-x-3">
                                <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">View</a>
                                <a href="{{ route('expenses.edit', $expense) }}" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-medium">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium" 
                                            onclick="return confirm('Are you sure you want to delete this expense?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500 dark:text-gray-400">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $expenses->links() }}
    </div>
</div>

<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {
        type: 'bar',
        data: {
            labels: {{ json_encode($expenseLabels) }},
            datasets: [{
                label: 'Expenses ($)',
                data: {{ json_encode($expenseData) }},
                backgroundColor: ['#ef4444', '#3b82f6'],
                borderColor: ['#b91c1c', '#1d4ed8'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#e5e7eb',
                        drawOnChartArea: true
                    },
                    ticks: {
                        color: '#374151',
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#374151'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#374151'
                    }
                }
            }
        }
    });
</script>
@endsection