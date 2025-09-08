@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Edit Payroll Record</h1>

    <form id="payroll-form" method="POST" action="{{ route('payroll.update', $payroll->id) }}" class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg">
                <p class="font-bold">Please correct the following errors:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="p-4 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-400 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="employee_id" class="block text-gray-700 dark:text-gray-300">Employee <span class="text-red-600">*</span></label>
                <select name="employee_id" id="employee_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('employee_id') border-red-500 @enderror" required>
                    <option value="" disabled>Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id', $payroll->employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} (Salary: ₵{{ number_format($employee->monthly_salary, 2) }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="pay_date" class="block text-gray-700 dark:text-gray-300">Pay Date <span class="text-red-600">*</span></label>
                <input type="date" name="pay_date" id="pay_date" value="{{ old('pay_date', $payroll->pay_date) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('pay_date') border-red-500 @enderror" required max="{{ now()->toDateString() }}">
                @error('pay_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="bonus" class="block text-gray-700 dark:text-gray-300">Bonus (₵)</label>
                <input type="number" name="bonus" id="bonus" step="0.01" min="0" value="{{ old('bonus', $payroll->bonus) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('bonus') border-red-500 @enderror">
                @error('bonus')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="deductions" class="block text-gray-700 dark:text-gray-300">Deductions (₵)</label>
                <input type="number" name="deductions" id="deductions" step="0.01" min="0" value="{{ old('deductions', $payroll->deductions) }}"
                       class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('deductions') border-red-500 @enderror">
                @error('deductions')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="block text-gray-700 dark:text-gray-300">Status <span class="text-red-600">*</span></label>
                <select name="status" id="status" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('status') border-red-500 @enderror" required>
                    <option value="pending" {{ old('status', $payroll->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ old('status', $payroll->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-2">
                <label for="notes" class="block text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" id="notes" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes', $payroll->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-6 flex space-x-4">
            <button type="submit" id="submit-btn" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                <svg id="submit-spinner" class="hidden animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span>Update</span>
            </button>
            <a href="{{ route('payroll.index') }}" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('payroll-form');
    const submitBtn = document.getElementById('submit-btn');
    const spinner = document.getElementById('submit-spinner');
    const bonusInput = document.getElementById('bonus');
    const deductionsInput = document.getElementById('deductions');
    const employeeSelect = document.getElementById('employee_id');

    form.addEventListener('submit', (e) => {
        console.log('Form submission triggered');
        console.log('Form data:', new FormData(form));
        if (!form.checkValidity()) {
            console.log('Form validation failed');
            e.preventDefault();
            form.reportValidity();
            return;
        }

        const bonus = parseFloat(bonusInput.value) || 0;
        const deductions = parseFloat(deductionsInput.value) || 0;
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        const baseSalary = parseFloat(selectedOption.text.match(/₵([\d,.]+)/)?.[1].replace(',', '')) || 0;

        if (baseSalary + bonus - deductions < 0) {
            e.preventDefault();
            alert('Net pay cannot be negative.');
            return;
        }

        console.log('Form validated, submitting...');
        submitBtn.setAttribute('disabled', 'true');
        spinner.classList.remove('hidden');
        submitBtn.querySelector('span').textContent = form.id === 'payroll-form' ? 'Saving...' : 'Updating...';
    });
});
</script>
@endpush
@endsection