@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Add Inventory Item</h2>
    <form action="{{ route('inventory.store') }}" method="POST" class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="sku" class="block text-gray-700 dark:text-gray-300">SKU</label>
            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('sku') border-red-500 @enderror">
            @error('sku')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="qty" class="block text-gray-700 dark:text-gray-300">Quantity</label>
            <input type="number" name="qty" id="qty" value="{{ old('qty') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('qty') border-red-500 @enderror">
            @error('qty')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Save</button>
    </form>
</div>
@endsection