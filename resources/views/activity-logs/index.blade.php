@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Activity Logs</h2>
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-700 dark:text-gray-300">
                    <th>Date</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t dark:border-gray-700">
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->details }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 dark:text-gray-400">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $logs->links() }}
    </div>
</div>
@endsection