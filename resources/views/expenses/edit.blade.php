@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23]">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Edit Expense</h1>
    <form action="{{ route('expenses.update', $expense) }}" method="POST" class="bg-white dark:bg-[#1a1a3a] p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300">Category</label>
            <select name="category" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('category') border-red-500 @enderror">
                <option value="Structure" {{ old('category', $expense->category) === 'Structure' ? 'selected' : '' }}>Structure</option>
                <option value="Feed" {{ old('category', $expense->category) === 'Feed' ? 'selected' : '' }}>Feed</option>
                <option value="Veterinary" {{ old('category', $expense->category) === 'Veterinary' ? 'selected' : '' }}>Veterinary</option>
                <option value="Utilities" {{ old('category', $expense->category) === 'Utilities' ? 'selected' : '' }}>Utilities</option>
                <option value="Labor" {{ old('category', $expense->category) === 'Labor' ? 'selected' : '' }}>Labor</option>
            </select>
            @error('category')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300">Description</label>
            <input type="text" name="description" value="{{ old('description', $expense->description) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror">
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300">Amount</label>
            <input type="number" name="amount" step="0.01" value="{{ old('amount', $expense->amount) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('amount') border-red-500 @enderror" required>
            @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300">Date</label>
            <input type="date" name="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('date') border-red-500 @enderror" required>
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-500">Update</button>
    </form>
</div>
@endsection