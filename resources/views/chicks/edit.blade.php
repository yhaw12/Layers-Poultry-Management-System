@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Edit Chick Record</h1>
        <form method="POST" action="{{ route('chicks.update', $chick) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label for="breed" class="block text-gray-700 font-medium mb-2">Breed</label>
                <input id="breed" type="text" name="breed" value="{{ old('breed', $chick->breed) }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('breed') border-red-500 @enderror">
                @error('breed')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="quantity_bought" class="block text-gray-700 font-medium mb-2">Quantity Bought</label>
                <input id="quantity_bought" type="number" name="quantity_bought" value="{{ old('quantity_bought', $chick->quantity_bought) }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity_bought') border-red-500 @enderror">
                @error('quantity_bought')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="feed_amount" class="block text-gray-700 font-medium mb-2">Feed Amount (kg)</label>
                <input id="feed_amount" type="number" step="0.01" name="feed_amount" value="{{ old('feed_amount', $chick->feed_amount) }}" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('feed_amount') border-red-500 @enderror">
                @error('feed_amount')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="alive" class="block text-gray-700 font-medium mb-2">Alive</label>
                <input id="alive" type="number" name="alive" value="{{ old('alive', $chick->alive) }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alive') border-red-500 @enderror">
                @error('alive')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="dead" class="block text-gray-700 font-medium mb-2">Dead</label>
                <input id="dead" type="number" name="dead" value="{{ old('dead', $chick->dead) }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dead') border-red-500 @enderror">
                @error('dead')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="purchase_date" class="block text-gray-700 font-medium mb-2">Purchase Date</label>
                <input id="purchase_date" type="date" name="purchase_date" value="{{ old('purchase_date', $chick->purchase_date->format('Y-m-d')) }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('purchase_date') border-red-500 @enderror">
                @error('purchase_date')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="cost" class="block text-gray-700 font-medium mb-2">Cost</label>
                <input id="cost" type="number" step="0.01" name="cost" value="{{ old('cost', $chick->cost) }}" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cost') border-red-500 @enderror">
                @error('cost')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">Update Chicks</button>
        </form>
    </div>
</div>
@endsection