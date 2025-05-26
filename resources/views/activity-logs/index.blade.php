@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Activity Logs</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">View all user activity logs (Admin only).</p>
    </section>

    <!-- Logs Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Activities</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $log->user->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ ucfirst($log->action) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $log->model_type ? class_basename($log->model_type) : '-' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $log->description ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </section>
</div>
@endsection