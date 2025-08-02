@extends('layouts.app')

@section('content')
<div class="text-2xl font-semibold mb-4">Alerts</div>
<div class="container mx-auto">
    @if ($alerts->isEmpty())
        <p>No alerts found.</p>
    @else
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700">
                    <th class="border px-4 py-2 text-left">Message</th>
                    <th class="border px-4 py-2 text-left">Created At</th>
                    <th class="border px-4 py-2 text-left">Status</th>
                    <th class="border px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alerts as $alert)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="border px-4 py-2">{{ $alert->message }}</td>
                        <td class="border px-4 py-2">{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                        <td class="border px-4 py-2">{{ $alert->is_read ? 'Read' : 'Unread' }}</td>
                        <td class="border px-4 py-2">
                            @if (!$alert->is_read)
                                <form method="POST" action="{{ route('alerts.read', $alert->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:underline">Mark as Read</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $alerts->links() }}
    @endif
</div>
@endsection