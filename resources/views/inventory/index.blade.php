@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Inventory</h2>
    <a href="{{ route('inventory.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 mb-6 inline-block">Add Item</a>
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-700 dark:text-gray-300">
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="border-t dark:border-gray-700">
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>
                            <a href="{{ route('inventory.edit', $item) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                            <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 dark:text-gray-400">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection