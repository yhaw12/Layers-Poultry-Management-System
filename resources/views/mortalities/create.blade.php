@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Add Mortality Record</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Record a new mortality event for a bird batch.</p>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form id="mortality-form" method="POST" action="{{ route('mortalities.store') }}" class="space-y-6">
                @csrf
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
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date <span class="text-red-600">*</span></label>
                    <input name="date" type="date" id="date" value="{{ old('date', now()->toDateString()) }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('date') border-red-500 @enderror"
                           required aria-describedby="date-error">
                    @error('date')
                        <p id="date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity <span class="text-red-600">*</span></label>
                    <input name="quantity" type="number" id="quantity" value="{{ old('quantity', 1) }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('quantity') border-red-500 @enderror"
                           min="1" step="1" required aria-describedby="quantity-error">
                    @error('quantity')
                        <p id="quantity-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cause -->
                <div>
                    <label for="cause" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cause (Optional)</label>
                    <input name="cause" type="text" id="cause" value="{{ old('cause') }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('cause') border-red-500 @enderror"
                           placeholder="e.g., Disease, Predator" aria-describedby="cause-error">
                    @error('cause')
                        <p id="cause-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bird Batch -->
                <div>
                    <label for="bird_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bird Batch <span class="text-red-600">*</span></label>
                    <select name="bird_id" id="bird_id" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('bird_id') border-red-500 @enderror" required aria-describedby="bird_id-error">
                        <option value="" {{ old('bird_id') ? '' : 'selected' }} disabled>Select Bird Batch</option>
                        @foreach($birds as $bird)
                            <option value="{{ $bird->id }}" {{ old('bird_id') == $bird->id ? 'selected' : '' }}>
                                {{ $bird->breed }} ({{ $bird->type }})
                            </option>
                        @endforeach
                    </select>
                    @error('bird_id')
                        <p id="bird_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
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
                    <a href="{{ route('mortalities.index') }}"
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
    // Form Submission Handler
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('mortality-form');
        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('submit-spinner');

        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }

            submitBtn.setAttribute('disabled', 'true');
            spinner.classList.remove('hidden');
            submitBtn.querySelector('span').textContent = 'Saving...';
        });
    });
</script>
@endpush
@endsection