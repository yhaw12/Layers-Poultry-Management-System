@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Medicine Log</h2>
            <a href="{{ route('medicine-logs.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                Back to Medicine Logs
            </a>
        </div>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form method="POST" action="{{ route('medicine-logs.update', $medicineLog) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label for="medicine_name" class="block text-gray-700 dark:text-gray-300">Medicine Name</label>
                    <input name="medicine_name" type="text" id="medicine_name" value="{{ old('medicine_name', $medicineLog->medicine_name) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('medicine_name') border-red-500 @enderror" required>
                    @error('medicine_name')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="type" class="block text-gray-700 dark:text-gray-300">Type</label>
                    <select name="type" id="type" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('type') border-red-500 @enderror" required>
                        <option value="">Select Type</option>
                        <option value="purchase" {{ old('type', $medicineLog->type) == 'purchase' ? 'selected' : '' }}>Purchase</option>
                        <option value="consumption" {{ old('type', $medicineLog->type) == 'consumption' ? 'selected' : '' }}>Consumption</option>
                    </select>
                    @error('type')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity</label>
                    <input name="quantity" type="number" step="0.01" id="quantity" value="{{ old('quantity', $medicineLog->quantity) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror" min="0.01" required>
                    @error('quantity')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="unit" class="block text-gray-700 dark:text-gray-300">Unit</label>
                    <input name="unit" type="text" id="unit" value="{{ old('unit', $medicineLog->unit) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('unit') border-red-500 @enderror" placeholder="e.g., ml, mg, tablets" required>
                    @error('unit')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="date" class="block text-gray-700 dark:text-gray-300">Date</label>
                    <input name="date" type="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('date') border-red-500 @enderror" required>
                    @error('date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="notes" class="block text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea name="notes" id="notes" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('notes') border-red-500 @enderror" rows="4">{{ old('notes', $medicineLog->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                        Update
                    </button>
                    <a href="{{ route('medicine-logs.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection