@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Medicine Logs</h2>
            <a href="{{ route('medicine-logs.create') }}" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                Add Medicine Log
            </a>
        </div>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Total Records: {{ $medicineLogs->total() }}</p>
    </section>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            {{ session('success') }}
        </div>
    @endif

    <!-- Medicine Logs Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Medicine Log History</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($medicineLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $log->medicine_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ ucfirst($log->type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ number_format($log->quantity, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $log->unit }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $log->date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $log->notes ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('medicine-logs.edit', $log) }}" class="text-blue-600 dark:text-blue-400 hover:underline mr-2">Edit</a>
                                <form action="{{ route('medicine-logs.destroy', $log) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No medicine logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($medicineLogs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $medicineLogs->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection