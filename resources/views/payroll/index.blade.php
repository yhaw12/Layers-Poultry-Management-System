@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Payroll Management</h1>

    <!-- Date Filter -->
    <form method="GET" class="bg-white p-6 rounded shadow dark:bg-[#1a1a3a]">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 dark:text-gray-300">Start Date</label>
                <input type="date" name="start_date" value="{{ $start }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-gray-700 dark:text-gray-300">End Date</label>
                <input type="date" name="end_date" value="{{ $end }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Filter
            </button>
        </div>
    </form>

    <!-- Generate Monthly Payroll -->
    <form method="POST" action="{{ route('payroll.generate') }}" class="bg-white p-6 rounded shadow dark:bg-[#1a1a3a]">
        @csrf
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 dark:text-gray-300">Month</label>
                <input type="month" name="month" value="{{ now()->format('Y-m') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            </div>
            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                Generate Monthly Payroll
            </button>
        </div>
    </form>

    <!-- Summary -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Payroll Summary</h2>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">Total Payroll: {{ number_format($totalPayroll, 2) }}</p>
        </div>
    </section>

    <!-- Payroll List -->
    <section>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Payroll Records</h2>
            <div>
                <a href="{{ route('payroll.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">Add Payroll</a>
                <a href="{{ route('payroll.export') }}?start_date={{ $start }}&end_date={{ $end }}" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600">Export PDF</a>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1a1a3a] rounded-2xl shadow overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-200 dark:bg-gray-700">
                    <tr>
                        <th class="p-4">Employee</th>
                        <th class="p-4">Pay Date</th>
                        <th class="p-4">Base Salary</th>
                        <th class="p-4">Bonus</th>
                        <th class="p-4">Deductions</th>
                        <th class="p-4">Net Pay</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        <tr class="border-b dark:border-gray-600">
                            <td class="p-4">{{ $payroll->employee->name }}</td>
                            <td class="p-4">{{ $payroll->pay_date }}</td>
                            <td class="p-4">{{ number_format($payroll->base_salary, 2) }}</td>
                            <td class="p-4">{{ number_format($payroll->bonus, 2) }}</td>
                            <td class="p-4">{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="p-4">{{ number_format($payroll->net_pay, 2) }}</td>
                            <td class="p-4">{{ ucfirst($payroll->status) }}</td>
                            <td class="p-4">
                                <a href="{{ route('payroll.show', $payroll->id) }}" class="text-blue-600 hover:underline dark:text-blue-400">View</a>
                                <a href="{{ route('payroll.edit', $payroll->id) }}" class="text-green-600 hover:underline dark:text-green-400">Edit</a>
                                <form action="{{ route('payroll.destroy', $payroll->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:underline dark:text-red-400">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center">No payroll records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $payrolls->links() }}
        </div>
    </section>
</div>
@endsection