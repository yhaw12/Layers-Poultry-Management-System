@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Payroll Details</h1>

    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-700 dark:text-gray-300"><strong>Employee:</strong> {{ $payroll->employee->name }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Pay Date:</strong> {{ $payroll->pay_date }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Base Salary:</strong> {{ number_format($payroll->base_salary, 2) }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Bonus:</strong> {{ number_format($payroll->bonus, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-700 dark:text-gray-300"><strong>Deductions:</strong> {{ number_format($payroll->deductions, 2) }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Net Pay:</strong> {{ number_format($payroll->net_pay, 2) }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong> {{ ucfirst($payroll->status) }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Notes:</strong> {{ $payroll->notes ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ route('payroll.edit', $payroll->id) }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">Edit</a>
            <a href="{{ route('payroll.index') }}" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600">Back</a>
        </div>
    </div>
</div>
@endsection