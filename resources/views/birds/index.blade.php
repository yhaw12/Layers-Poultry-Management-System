@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">All Birds</h2>
            <a href="{{ route('birds.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Add New Bird Batch
            </a>
        </div>
    </section>

    <!-- Summary -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md mb-6">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Quantity</h3>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalQuantity, 0) }} birds</p>
            <p class="text-lg text-gray-700 dark:text-gray-300">Layers: {{ number_format($layers, 0) }}</p>
            <p class="text-lg text-gray-700 dark:text-gray-300">Broilers: {{ number_format($broilers, 0) }}</p>
            <p class="text-lg text-gray-700 dark:text-gray-300">Chicks: {{ number_format($chicks, 0) }}</p>
        </div>
    </section>

    <!-- Birds Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Bird Records</h3>
            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if ($birds->isEmpty())
                <p class="text-gray-600 dark:text-gray-400">No birds found.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
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
                        <tbody>
                            @foreach ($birds as $bird)
                                <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td class="p-3">{{ $bird->id }}</td>
                                    <td class="p-3">{{ $bird->breed }}</td>
                                    <td class="p-3">{{ ucfirst($bird->type) }}</td>
                                    <td class="p-3">{{ ucfirst($bird->stage) }}</td>
                                    <td class="p-3">{{ $bird->quantity ?? 'N/A' }}</td>
                                    <td class="p-3">{{ $bird->alive ?? 'N/A' }}</td>
                                    <td class="p-3">{{ $bird->dead ?? 'N/A' }}</td>
                                    <td class="p-3">{{ $bird->cost ? number_format($bird->cost, 2) : 'N/A' }}</td>
                                    <td class="p-3">{{ $bird->working ? 'Yes' : 'No' }}</td>
                                    <td class="p-3">{{ $bird->age }}</td>
                                    <td class="p-3">{{ $bird->entry_date->format('Y-m-d') }}</td>
                                    <td class="p-3 flex space-x-2">
                                        <a href="{{ route('birds.edit', $bird->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                        <form action="{{ route('birds.destroy', $bird->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $birds->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection