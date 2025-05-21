@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Add Hens</h1>
        <form method="POST" action="{{ route('hens.store') }}" class="space-y-6">
            @csrf
            <div>
                <label for="breed" class="block text-gray-700 font-medium mb-2">Breed</label>
                <input id="breed" type="text" name="breed" value="{{ old('breed') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('breed') border-red-500 @enderror">
                @error('breed')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="quantity" class="block text-gray-700 font-medium mb-2">Quantity</label>
                <input id="quantity" type="number" name="quantity" value="{{ old('quantity') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror">
                @error('quantity')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="working" class="block text-gray-700 font-medium mb-2">Working (Laying)</label>
                <input id="working" type="number" name="working" value="{{ old('working', 0) }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('working') border-red-500 @enderror">
                @error('working')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="age" class="block text-gray-700 font-medium mb-2">Age (Months)</label>
                <input id="age" type="number" name="age" value="{{ old('age') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('age') border-red-500 @enderror">
                @error('age')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="entry_date" class="block text-gray-700 font-medium mb-2">Entry Date</label>
                <input id="entry_date" type="date" name="entry_date" value="{{ old('entry_date') }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('entry_date') border-red-500 @enderror">
                @error('entry_date')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">Save Hens</button>
        </form>
    </div>
</div>
@endsection