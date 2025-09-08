@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <section class="text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">➕ Record Feed Consumption</h2>
        <p class="text-gray-600 dark:text-gray-400">Record daily feed usage for your flocks.</p>
    </section>

    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-lg mx-auto">
            <form method="POST" action="{{ route('feed.storeConsumption') }}" class="space-y-6" novalidate>
                @csrf

                <div>
                    <label for="feed_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Feed</label>
                    <select name="feed_id" id="feed_id"
                            class="w-full mt-1 p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 @error('feed_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Select a feed --</option>
                        @foreach ($feeds as $feed)
                            <option value="{{ $feed->id }}" {{ (int) old('feed_id') === $feed->id ? 'selected' : '' }}>
                                {{ $feed->type }} ({{ $feed->quantity ?? 0 }} bags)
                            </option>
                        @endforeach
                    </select>
                    @error('feed_id')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                    <input name="date" type="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                           class="w-full mt-1 p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 @error('date') border-red-500 @enderror"
                           required>
                    @error('date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity Consumed (kg)</label>
                    <input name="quantity" type="number" step="0.01" id="quantity" value="{{ old('quantity') }}"
                           class="w-full mt-1 p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror"
                           min="0.01" required inputmode="decimal" pattern="^\d+(\.\d{1,2})?$">
                    @error('quantity')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-4">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition focus:ring-2 focus:ring-blue-500">
                        Save Record
                    </button>

                    <a href="{{ route('feed.consumption') }}"
                       class="flex-1 text-center bg-gray-300 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition">
                        Back to History
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
