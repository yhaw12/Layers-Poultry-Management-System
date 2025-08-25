@extends('layouts.app')

@section('title', 'Medicine Logs')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Medicine Logs</h2>
        <a href="{{ route('medicine-logs.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                  dark:bg-blue-500 dark:hover:bg-blue-600 transition">
            ➕ Add Medicine Log
        </a>
    </section>

    <!-- Summary Card -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Medicine Logs</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($medicineLogs->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
        </div>
    </section>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Medicine Logs Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Medicine Log Records</h3>

            @if ($medicineLogs->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No medicine logs found yet.</p>
                    <a href="{{ route('medicine-logs.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        ➕ Add Your First Medicine Log
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Medicine</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Unit</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Notes</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($medicineLogs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->medicine_name }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ ucfirst($log->type) }}</td>
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">{{ number_format($log->quantity, 2) }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->unit }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->date->format('Y-m-d') }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->notes ?? 'None' }}</td>
                                    <td class="p-4 flex space-x-2">
                                        <a href="{{ route('medicine-logs.edit', $log) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs transition">
                                           ✏️ Edit
                                        </a>
                                        <form action="{{ route('medicine-logs.destroy', $log) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this medicine log record?');">
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
                @if ($medicineLogs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-6 flex justify-end">
                        {{ $medicineLogs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection