@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Create Payroll Record</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Add a new payroll record for an employee.</p>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form id="payroll-form" method="POST" action="{{ route('payroll.store') }}" class="space-y-6">
                @csrf

                <!-- Debug Info -->
                @if ($errors->any() || session('error'))
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400 rounded-lg">
                        <p class="font-bold">Debug Info:</p>
                        <p>Form submission failed. Please check the fields below.</p>
                        <p>Submitted data: {{ json_encode(old()) }}</p>
                    </div>
                @endif

                <!-- Success/Error Messages -->
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

                <!-- Employee -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee <span class="text-red-600">*</span></label>
                    <select name="employee_id" id="employee_id" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('employee_id') border-red-500 @enderror" required aria-describedby="employee_id-error">
                        <option value="" {{ old('employee_id') ? '' : 'selected' }} disabled>Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} (Salary: ₵{{ number_format($employee->monthly_salary, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p id="employee_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pay Date -->
                <div>
                    <label for="pay_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pay Date <span class="text-red-600">*</span></label>
                    <input type="date" name="pay_date" id="pay_date" value="{{ old('pay_date', now()->toDateString()) }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('pay_date') border-red-500 @enderror"
                           required aria-describedby="pay_date-error" max="{{ now()->toDateString() }}">
                    @error('pay_date')
                        <p id="pay_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bonus -->
                <div>
                    <label for="bonus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bonus (₵)</label>
                    <input type="number" name="bonus" id="bonus" value="{{ old('bonus', 0) }}"
                           step="0.01" min="0" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('bonus') border-red-500 @enderror"
                           aria-describedby="bonus-error">
                    @error('bonus')
                        <p id="bonus-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deductions -->
                <div>
                    <label for="deductions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deductions (₵)</label>
                    <input type="number" name="deductions" id="deductions" value="{{ old('deductions', 0) }}"
                           step="0.01" min="0" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('deductions') border-red-500 @enderror"
                           aria-describedby="deductions-error">
                    @error('deductions')
                        <p id="deductions-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-600">*</span></label>
                    <select name="status" id="status" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('status') border-red-500 @enderror" required aria-describedby="status-error">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    @error('status')
                        <p id="status-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                    <textarea name="notes" id="notes" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('notes') border-red-500 @enderror"
                              rows="4" maxlength="500" aria-describedby="notes-error">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p id="notes-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" id="submit-btn"
                            class="inline-flex items-center bg-blue-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors duration-200 font-medium">
                        <svg id="submit-spinner" class="hidden animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span>Save</span>
                    </button>
                    <a href="{{ route('payroll.index') }}"
                       class="inline-flex items-center bg-gray-300 text-gray-800 py-2 px-4 rounded-lg shadow-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition-colors duration-200">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
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