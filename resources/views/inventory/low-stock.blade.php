@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Low Stock Items</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($lowStockItems->isEmpty())
            <p class="text-gray-600 dark:text-gray-400">No low stock items found.</p>
        @else
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Threshold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($lowStockItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->qty }} {{ $item->type === 'Feed' ? 'kg' : 'units' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->threshold }} {{ $item->type === 'Feed' ? 'kg' : 'units' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($item->type === 'Inventory' && $item->id)
                                        <a href="{{ route('inventory.edit', $item->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                    @elseif ($item->type === 'Feed' && $item->id)
                                        <a href="{{ route('feed.edit', $item->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <a href="{{ route('inventory.index') }}" class="mt-6 inline-block text-blue-600 dark:text-blue-400 hover:underline">Back to Inventory</a>
    </div>
@endsection