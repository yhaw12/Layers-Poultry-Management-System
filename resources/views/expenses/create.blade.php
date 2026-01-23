@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Expense</h2>
        </section>

        <!-- Form -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
                <form method="POST" action="{{ route('expenses.store') }}" class="space-y-6">
                    @csrf
                    <!-- Success/Error Messages -->
                    @if (session('error'))
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-gray-700 dark:text-gray-300">Category <span class="text-red-600">*</span></label>
                        <select name="category" id="category" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('category') border-red-500 @enderror" required>
                            <option value="Structure" {{ old('category') == 'Structure' ? 'selected' : '' }}>Structure</option>
                            <option value="Feed" {{ old('category') == 'Feed' ? 'selected' : '' }}>Feed</option>
                            <option value="MedicineLog" {{ old('category') == 'MedicineLog' ? 'selected' : '' }}>MedicineLog</option>
                            <option value="Utilities" {{ old('category') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                            <option value="Labor" {{ old('category') == 'Labor' ? 'selected' : '' }}>Labor</option>
                        </select>
                        @error('category')
                            <p id="category-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-gray-700 dark:text-gray-300">Description</label>
                        <input name="description" type="text" id="description" value="{{ old('description') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror"
                               aria-describedby="description-error">
                        @error('description')
                            <p id="description-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-gray-700 dark:text-gray-300">Amount <span class="text-red-600">*</span></label>
                        <input name="amount" type="number" step="0.01" id="amount" value="{{ old('amount') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('amount') border-red-500 @enderror"
                               required aria-describedby="amount-error">
                        @error('amount')
                            <p id="amount-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-gray-700 dark:text-gray-300">Date <span class="text-red-600">*</span></label>
                        <input name="date" type="date" id="date" value="{{ old('date') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('date') border-red-500 @enderror"
                               required aria-describedby="date-error">
                        @error('date')
                            <p id="date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Save
                        </button>
                        <a href="{{ route('expenses.index') }}"
                           class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
