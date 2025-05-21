@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Chicks</h1>
            <a href="{{ route('chicks.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">+ Add Chicks</a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Breed</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Bought</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Feed (kg)</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Alive</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Dead</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Purchase Date</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Cost</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($chicks as $chick)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-200">
                            <td class="p-4">{{ $chick->breed }}</td>
                            <td class="p-4">{{ $chick->quantity_bought }}</td>
                            <td class="p-4">{{ number_format($chick->feed_amount, 2) }}</td>
                            <td class="p-4">{{ $chick->alive }}</td>
                            <td class="p-4">{{ $chick->dead }}</td>
                            <td class="p-4">{{ $chick->purchase_date->format('Y-m-d') }}</td>
                            <td class="p-4">${{ number_format($chick->cost, 2) }}</td>
                            <td class="p-4 flex space-x-3">
                                <a href="{{ route('chicks.edit', $chick) }}" class="text-green-600 hover:text-green-800 font-medium">Edit</a>
                                <form action="{{ route('chicks.destroy', $chick) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500">No chicks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection