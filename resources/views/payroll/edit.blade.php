@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Edit Payroll Record</h1>

    <form method="POST" action="{{ route('payroll.update', $payroll->id) }}" class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Employee</label>
                <select name="employee_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $payroll->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ number_format($employee->monthly_salary, 2) }})</option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Pay Date</label>
                <input type="date" name="pay_date" value="{{ $payroll->pay_date }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('pay_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Bonus</label>
                <input type="number" name="bonus" step="0.01" value="{{ $payroll->bonus }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('bonus')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Deductions</label>
                <input type="number" name="deductions" step="0.01" value="{{ $payroll->deductions }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('deductions')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Status</label>
                <select name="status" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="pending" {{ $payroll->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $payroll->status == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ $payroll->notes }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">Update</button>
            <a href="{{ route('payroll.index') }}" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection