@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-lg">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Add Egg Record</h1>
        <form method="POST" action="{{ route('eggs.store') }}" class="space-y-6">
            @csrf
            <div>
                <label for="crates" class="block text-gray-700 font-medium mb-2">Crates</label>
                <input id="crates" type="number" name="crates" value="{{ old('crates') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('crates') border-red-500 @enderror">
                @error('crates')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="date_laid" class="block text-gray-700 font-medium mb-2">Date Collected</label>
                <input id="date_laid" type="date" name="date_laid" value="{{ old('date_laid') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_laid') border-red-500 @enderror">
                @error('date_laid')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="sold_quantity" class="block text-gray-700 font-medium mb-2">Sold Quantity</label>
                <input id="sold_quantity" type="number" name="sold_quantity" value="{{ old('sold_quantity', 0) }}" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sold_quantity') border-red-500 @enderror">
                @error('sold_quantity')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="sold_date" class="block text-gray-700 font-medium mb-2">Sold Date</label>
                <input id="sold_date" type="date" name="sold_date" value="{{ old('sold_date') }}" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sold_date') border-red-500 @enderror">
                @error('sold_date')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="sale_price" class="block text-gray-700 font-medium mb-2">Sale Price</label>
                <input id="sale_price" type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sale_price') border-red-500 @enderror">
                @error('sale_price')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">Save Egg Record</button>
        </form>
    </div>
</div>
@endsection