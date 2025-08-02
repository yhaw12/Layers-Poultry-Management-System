{{-- create --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Add Egg Record</h2>
    <form method="POST" action="{{ route('eggs.store') }}"
        class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Crates</label>
                <input type="number" name="crates" value="{{ old('crates') }}"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                @error('crates')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Date Laid</label>
                <input type="date" name="date_laid" value="{{ old('date_laid') }}"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                @error('date_laid')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Sold Quantity</label>
                <input type="number" name="sold_quantity" value="{{ old('sold_quantity') }}"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('sold_quantity')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Sold Date</label>
                <input type="date" name="sold_date" value="{{ old('sold_date') }}"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('sold_date')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Sale Price</label>
                <input type="number" name="sale_price" value="{{ old('sale_price') }}" step="0.01"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('sale_price')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-6">
            <button type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Save
            </button>
            <a href="{{ route('eggs.index') }}"
                class="ml-4 text-gray-600 dark:text-gray-300 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection