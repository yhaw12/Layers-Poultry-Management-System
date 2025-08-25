@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Inventory Management</h2>
        <a href="{{ route('inventory.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                  dark:bg-blue-500 dark:hover:bg-blue-600 transition">
            ➕ Add New Item
        </a>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Inventory Items</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($items->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Low Stock Items</span>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($lowStockItems->count(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Items</span>
            </div>
        </div>
    </section>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Low Stock Alerts Section -->
    @if ($lowStockItems->isNotEmpty())
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Low Stock Alerts</h3>
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Name</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Threshold</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($lowStockItems as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $item->type }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $item->name }}</td>
                                    <td class="p-4 font-semibold text-red-600 dark:text-red-400">{{ $item->qty }} {{ $item->type === 'Feed' ? 'kg' : 'units' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $item->threshold }} {{ $item->type === 'Feed' ? 'kg' : 'units' }}</td>
                                    <td class="p-4 flex space-x-2">
                                        @if ($item->type === 'Inventory' && $item->id)
                                            <a href="{{ route('inventory.edit', $item->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs transition">
                                               ✏️ Edit
                                            </a>
                                        @elseif ($item->type === 'Feed' && $item->id)
                                            <a href="{{ route('feed.edit', $item->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs transition">
                                               ✏️ Edit
                                            </a>
                                        @else
                                            <span class="px-3 py-1 text-xs text-gray-500 dark:text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('alerts.low-stock') }}" 
                       class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 text-xs transition">
                       📜 View All Low Stock Items
                    </a>
                </div>
            </div>
        </section>
    @else
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 text-center">
                <p class="text-gray-600 dark:text-gray-400">No low stock items found.</p>
            </div>
        </section>
    @endif

    <!-- Inventory Items Section -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Inventory Items</h3>
            @if ($items->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No inventory items found yet.</p>
                    <a href="{{ route('inventory.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        ➕ Add Your First Inventory Item
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Name</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">SKU</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Threshold</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $item->name }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $item->sku }}</td>
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">{{ $item->qty }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $item->threshold }}</td>
                                    <td class="p-4 flex space-x-2">
                                        <a href="{{ route('inventory.edit', $item->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs transition">
                                           ✏️ Edit
                                        </a>
                                        <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this inventory item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs transition">
                                                🗑 Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-6 flex justify-end">
                        {{ $items->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection