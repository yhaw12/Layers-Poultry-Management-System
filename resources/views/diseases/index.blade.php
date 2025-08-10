@extends('layouts.app')

@section('title', 'Diseases')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Diseases</h1>

        <div class="mb-4">
            <form action="{{ route('diseases.store') }}" method="POST" class="inline-block">
                @csrf
                <input type="text" name="name" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Add new disease" required>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 ml-2">Add</button>
            </form>
        </div>

        @if ($diseases->isEmpty())
            <p class="text-gray-600">No diseases recorded.</p>
        @else
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($diseases as $disease)
                            <tr class="border-t">
                                <td class="px-6 py-4 text-gray-800">{{ $disease->name }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('diseases.history', $disease->id) }}" class="text-blue-600 hover:underline">View History</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $diseases->links() }}
        @endif
    </div>
@endsection