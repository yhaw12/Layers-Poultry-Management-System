@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Feed</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form method="POST" action="{{ route('feed.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="type" class="block text-gray-700 dark:text-gray-300">Type</label>
                    <input name="type" type="text" id="type" value="{{ old('type') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('type') border-red-500 @enderror" required>
                    @error('type')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="supplier_id" class="block text-gray-700 dark:text-gray-300">Supplier</label>
                    <select name="supplier_id" id="supplier_id"
                            class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('supplier_id') border-red-500 @enderror">
                        <option value="">Select a supplier (optional)</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity (Bags)</label>
                    <input name="quantity" type="number" id="quantity" value="{{ old('quantity') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror" min="1" required>
                    @error('quantity')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="weight" class="block text-gray-700 dark:text-gray-300">Weight per Bag (kg)</label>
                    <input name="weight" type="number" step="0.01" id="weight" value="{{ old('weight') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('weight') border-red-500 @enderror" min="0" required>
                    @error('weight')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="purchase_date" class="block text-gray-700 dark:text-gray-300">Purchase Date</label>
                    <input name="purchase_date" type="date" id="purchase_date" value="{{ old('purchase_date', now()->format('Y-m-d')) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('purchase_date') border-red-500 @enderror" required>
                    @error('purchase_date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="cost" class="block text-gray-700 dark:text-gray-300">Cost (₵)</label>
                    <input name="cost" type="number" step="0.01" id="cost" value="{{ old('cost') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('cost') border-red-500 @enderror" min="0" required>
                    @error('cost')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Save
                    </button>
                    <a href="{{ route('feed.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection