@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Customer</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Create a new customer record.</p>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form id="customer-form" method="POST" action="{{ route('customers.store') }}" class="space-y-6">
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

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-600">*</span></label>
                    <input name="name" type="text" id="name" value="{{ old('name') }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                           required aria-describedby="name-error">
                    @error('name')
                        <p id="name-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone <span class="text-red-600">*</span></label>
                    <input name="phone" type="text" id="phone" value="{{ old('phone') }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('phone') border-red-500 @enderror"
                           required aria-describedby="phone-error">
                    @error('phone')
                        <p id="phone-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <a href="{{ route('customers.index') }}"
                       class="inline-flex items-center bg-gray-300 text-gray-800 py-2 px-4 rounded-lg shadow-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition-colors duration-200">
                        Previous
                    </a>
                    <button type="submit" id="submit-btn"
                            class="inline-flex items-center bg-blue-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors duration-200 font-medium">
                        <svg id="submit-spinner" class="hidden animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span>Next</span>
                    </button>
                    <a href="{{ route('customers.index') }}"
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
        const form = document.getElementById('customer-form');
        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('submit-spinner');

        form.addEventListener('submit', (e) => {
            console.log('Form submission triggered');
            console.log('Form data:', new FormData(form));
            if (!form.checkValidity()) {
                console.log('Form validation failed');
                e.preventDefault();
                form.reportValidity();
                return;
            }
            console.log('Form validated, submitting...');
            submitBtn.setAttribute('disabled', 'true');
            spinner.classList.remove('hidden');
            submitBtn.querySelector('span').textContent = 'Saving...';
        });
    });
</script>
@endpush
@endsection