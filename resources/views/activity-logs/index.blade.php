@extends('layouts.app')

@section('content')
@role('admin')
    <div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Activity Logs</h2>
        <form method="GET" class="mb-4 flex items-center gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="flex-1 border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">Search</button>
        </form>
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
@else
    <div class="container mx-auto px-4 py-8 text-center text-red-600 dark:text-red-400">
        <p>You do not have permission to view activity logs.</p>
    </div>
@endrole
@endsection