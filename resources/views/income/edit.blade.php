@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Edit Income Record</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Update the details of the income record.</p>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form action="{{ route('income.update', $income) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

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

                <!-- Source -->
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Source <span class="text-red-600">*</span></label>
                    <input type="text" name="source" id="source" value="{{ old('source', $income->source) }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('source') border-red-500 @enderror"
                           required aria-describedby="source-error" maxlength="255">
                    @error('source')
                        <p id="source-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                    <input type="text" name="description" id="description" value="{{ old('description', $income->description) }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('description') border-red-500 @enderror"
                           aria-describedby="description-error">
                    @error('description')
                        <p id="description-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (₵) <span class="text-red-600">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', $income->amount) }}"
                           step="0.01" min="0" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('amount') border-red-500 @enderror"
                           required aria-describedby="amount-error">
                    @error('amount')
                        <p id="amount-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date <span class="text-red-600">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', $income->date->format('Y-m-d')) }}"
                           class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('date') border-red-500 @enderror"
                           required aria-describedby="date-error" max="{{ now()->toDateString() }}">
                    @error('date')
                        <p id="date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="inline-flex items-center bg-blue-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors duration-200 font-medium">
                        <span>Update</span>
                    </button>
                    <a href="{{ route('income.index') }}"
                       class="inline-flex items-center bg-gray-300 text-gray-800 py-2 px-4 rounded-lg shadow-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition-colors duration-200">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection