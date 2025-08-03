@extends('layouts.app')

@section('content')
<div class="text-2xl font-semibold mb-4">Alerts</div>
<div class="container mx-auto">
    <canvas id="alertsChart" height="100"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('alertsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Warning', 'Sale', 'Critical', 'Info'],
                datasets: [{
                    label: 'Number of Alerts',
                    data: [
                        {{ $alerts->where('type', 'warning')->count() }},
                        {{ $alerts->where('type', 'sale')->count() }},
                        {{ $alerts->where('type', 'critical')->count() }},
                        {{ $alerts->where('type', 'info')->count() }}
                    ],
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Number of Alerts' }
                    },
                    x: {
                        title: { display: true, text: 'Alert Type' }
                    }
                },
                plugins: {
                    legend: { display: true, position: 'top' },
                    title: { display: true, text: 'Alerts by Type' }
                }
            }
        });
    </script>
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
                        <td class="border px-4 py-2">{{ $alert->created_at ? $alert->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $alert->is_read ? 'Read' : 'Unread' }}</td>
                        <td class="border px-4 py-2">
                            @if (!$alert->is_read && $alert->id)
                                <form method="POST" action="{{ route('alerts.read', $alert->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:underline">Mark as Read</button>
                                </form>
                            @elseif (!$alert->id)
                                <span class="text-gray-500">Cannot mark as read (No ID)</span>
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