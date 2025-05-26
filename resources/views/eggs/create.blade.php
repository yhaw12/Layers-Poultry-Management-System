@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Egg Record</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form method="POST" action="{{ route('eggs.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="crates" class="block text-gray-700 dark:text-gray-300">Crates</label>
                    <input name="crates" type="number" id="crates" value="{{ old('crates') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('crates') border-red-500 @enderror" min="0" required>
                    @error('crates')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="date_laid" class="block text-gray-700 dark:text-gray-300">Date Laid</label>
                    <input name="date_laid" type="date" id="date_laid" value="{{ old('date_laid') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('date_laid') border-red-500 @enderror" required>
                    @error('date_laid')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="sold_quantity" class="block text-gray-700 dark:text-gray-300">Sold Quantity (Optional)</label>
                    <input name="sold_quantity" type="number" id="sold_quantity" value="{{ old('sold_quantity') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('sold_quantity') border-red-500 @enderror" min="0">
                    @error('sold_quantity')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="sold_date" class="block text-gray-700 dark:text-gray-300">Sold Date (Optional)</label>
                    <input name="sold_date" type="date" id="sold_date" value="{{ old('sold_date') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('sold_date') border-red-500 @enderror">
                    @error('sold_date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="sale_price" class="block text-gray-700 dark:text-gray-300">Sale Price (GHS, Optional)</label>
                    <input name="sale_price" type="number" step="0.01" id="sale_price" value="{{ old('sale_price') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('sale_price') border-red-500 @enderror" min="0">
                    @error('sale_price')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Save
                    </button>
                    <a href="{{ route('eggs.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection