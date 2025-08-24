@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Bird Management</h2>
        <a href="{{ route('birds.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                  dark:bg-blue-500 dark:hover:bg-blue-600 transition">
            ➕ Add Bird Batch
        </a>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalQuantity, 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Birds</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Layers</span>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($layers, 0) }}</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Broilers</span>
                <p class="text-2xl font-bold text-green-600">{{ number_format($broilers, 0) }}</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Chicks</span>
                <p class="text-2xl font-bold text-pink-600">{{ number_format($chicks, 0) }}</p>
            </div>
        </div>
    </section>

    <!-- Birds Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Bird Records</h3>

            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-3 rounded mb-4">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if ($birds->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No birds found yet.</p>
                    <a href="{{ route('birds.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        ➕ Add Your First Batch
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700 text-sm">
                                <th class="p-3 text-gray-700 dark:text-gray-200">ID</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Breed</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Type</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Stage</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Quantity</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Alive</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Dead</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Cost</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Working</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Age (Weeks)</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Entry Date</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach ($birds as $bird)
                                <tr class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-3">{{ $bird->id }}</td>
                                    <td class="p-3">{{ $bird->breed }}</td>
                                    <td class="p-3">{{ ucfirst($bird->type) }}</td>
                                    <td class="p-3">{{ ucfirst($bird->stage) }}</td>
                                    <td class="p-3 font-semibold">{{ $bird->quantity ?? 'N/A' }}</td>
                                    <td class="p-3 text-green-600 font-bold">{{ $bird->alive ?? 'N/A' }}</td>
                                    <td class="p-3 text-red-600 font-bold">{{ $bird->dead ?? 'N/A' }}</td>
                                    <td class="p-3">{{ $bird->cost ? number_format($bird->cost, 2) : 'N/A' }}</td>
                                    <td class="p-3">
                                        @if($bird->working)
                                            <span class="px-2 py-1 bg-green-200 text-green-800 text-xs rounded-full">Yes</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-200 text-red-800 text-xs rounded-full">No</span>
                                        @endif
                                    </td>
                                    <td class="p-3">{{ $bird->age }}</td>
                                    <td class="p-3">{{ $bird->entry_date->format('Y-m-d') }}</td>
                                    <td class="p-3 flex space-x-2">
                                        <a href="{{ route('birds.edit', $bird->id) }}" 
                                           class="px-3 py-1 bg-yellow-500 text-white rounded shadow hover:bg-yellow-600 text-xs">
                                           ✏️ Edit
                                        </a>
                                        <form action="{{ route('birds.destroy', $bird->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-red-600 text-white rounded shadow hover:bg-red-700 text-xs">
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
                <div class="mt-6 flex justify-end">
                    {{ $birds->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
