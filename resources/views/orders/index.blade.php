```php
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Orders</h2>
        </section>

        <!-- Date Range Filter -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('orders.index') }}" class="space-y-6">
                    <!-- Success/Error Messages -->
                    @if (session('success'))
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-400 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex flex-col gap-4 sm:flex-row sm:gap-6">
                        <div class="flex-1">
                            <label for="start_date" class="block text-gray-700 dark:text-gray-300">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $start }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('start_date') border-red-500 @enderror"
                                   aria-describedby="start_date-error">
                            @error('start_date')
                                <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                        <div class="flex-1">
                            <label for="end_date" class="block text-gray-700 dark:text-gray-300">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $end }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('end_date') border-red-500 @enderror"
                                   aria-describedby="end_date-error">
                            @error('end_date')
                                <p id="end_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Filter
                        </button>
                        <a href="{{ route('orders.index') }}"
                           class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- Orders Table -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Order List</h3>
                    <a href="{{ route('orders.create') }}"
                       class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Add Order
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $order->customer->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($order->status) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('orders.edit', $order->id) }}"
                                           class="text-blue-600 dark:text-blue-400 hover:underline mr-2">Edit</a>
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $orders->links() }}
            </div>
        </section>
    </div>
@endsection
```