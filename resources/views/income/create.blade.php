@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Add Income</h1>
        <form method="POST" action="{{ route('income.store') }}" class="space-y-6">
            @csrf
            <div>
                <label for="source" class="block text-gray-700 font-medium mb-2">Source</label>
                <input id="source" type="text" name="source" value="{{ old('source') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('source') border-red-500 @enderror">
                @error('source')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea id="description" name="description" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="amount" class="block text-gray-700 font-medium mb-2">Amount</label>
                <input id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror">
                @error('amount')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="date" class="block text-gray-700 font-medium mb-2">Date</label>
                <input id="date" type="date" name="date" value="{{ old('date') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date') border-red-500 @enderror">
                @error('date')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">Save Income</button>
        </form>
    </div>
</div>
@endsection