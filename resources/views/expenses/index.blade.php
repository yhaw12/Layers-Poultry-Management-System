@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- Header and Add Button -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Expenses</h1>
            <a href="{{ route('expenses.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">+ Add Expense</a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6 shadow-sm">{{ session('success') }}</div>
        @endif

        <!-- Expense Comparison Chart -->
        <div class="mb-8">
            <canvas id="expenseChart" class="w-full h-64"></canvas>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Category</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Amount</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Date</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-200">
                            <td class="p-4">{{ $expense->category }}</td>
                            <td class="p-4 text-gray-800 font-medium">${{ number_format($expense->amount, 2) }}</td>
                            <td class="p-4">{{ $expense->date->format('Y-m-d') }}</td>
                            <td class="p-4 flex space-x-3">
                                <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                                <a href="{{ route('expenses.edit', $expense) }}" class="text-green-600 hover:text-green-800 font-medium">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium" 
                                            onclick="return confirm('Are you sure you want to delete this expense?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection