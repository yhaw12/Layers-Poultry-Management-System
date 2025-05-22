@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Medicine Logs</h1>
        <a href="{{ route('medicine-logs.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Log</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Date</th>
                <th class="border p-2">Type</th>
                <th class="border p-2">Medicine</th>
                <th class="border p-2">Qty</th>
                <th class="border p-2">Unit</th>
                <th class="border p-2">Notes</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td class="border p-2">{{ $log->date->format('Y-m-d') }}</td>
                    <td class="border p-2 capitalize">{{ $log->type }}</td>
                    <td class="border p-2">{{ $log->medicine_name }}</td>
                    <td class="border p-2">{{ $log->quantity }}</td>
                    <td class="border p-2">{{ $log->unit }}</td>
                    <td class="border p-2">{{ $log->notes }}</td>
                    <td class="border p-2 space-x-2">
                        <a href="{{ route('medicine-logs.edit', $log) }}" class="text-blue-500">Edit</a>
                        <form action="{{ route('medicine-logs.destroy', $log) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this log?')" class="text-red-500">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
