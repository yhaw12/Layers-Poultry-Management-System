@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Chick Batch</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <form method="POST" action="{{ route('chicks.update', $chick) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label for="breed" class="block text-gray-700 dark:text-gray-300">Breed</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed', $chick->breed) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('breed')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="quantity_bought" class="block text-gray-700 dark:text-gray-300">Quantity Bought</label>
                    <input type="number" name="quantity_bought" id="quantity_bought" value="{{ old('quantity_bought', $chick->quantity_bought) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="1" required>
                    @error('quantity_bought')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="feed_amount" class="block text-gray-700 dark:text-gray-300">Feed Amount (kg)</label>
                    <input type="number" name="feed_amount" id="feed_amount" value="{{ old('feed_amount', $chick->feed_amount) }}" step="0.01" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" required>
                    @error('feed_amount')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="alive" class="block text-gray-700 dark:text-gray-300">Alive</label>
                    <input type="number" name="alive" id="alive" value="{{ old('alive', $chick->alive) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" required>
                    @error('alive')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="dead" class="block text-gray-700 dark:text-gray-300">Dead</label>
                    <input type="number" name="dead" id="dead" value="{{ old('dead', $chick->dead) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" required>
                    @error('dead')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="purchase_date" class="block text-gray-700 dark:text-gray-300">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $chick->purchase_date->format('Y-m-d')) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('purchase_date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="cost" class="block text-gray-700 dark:text-gray-300">Cost ($)</label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $chick->cost) }}" step="0.01" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" required>
                    @error('cost')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Update
                    </button>
                    <a href="{{ route('chicks.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection