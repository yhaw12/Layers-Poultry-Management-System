@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Feed</h1>
            <a href="{{ route('feed.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">+ Add Feed</a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Type</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Quantity</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Weight (kg)</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Purchase Date</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Cost</th>
                        <th class="p-4 text-gray-700 font-semibold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feeds as $feed)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-200">
                            <td class="p-4">{{ $feed->type }}</td>
                            <td class="p-4">{{ $feed->quantity }}</td>
                            <td class="p-4">{{ number_format($feed->weight, 2) }}</td>
                            <td class="p-4">{{ $feed->purchase_date->format('Y-m-d') }}</td>
                            <td class="p-4">${{ number_format($feed->cost, 2) }}</td>
                            <td class="p-4 flex space-x-3">
                                <a href="{{ route('feed.edit', $feed) }}" class="text-green-600 hover:text-green-800 font-medium">Edit</a>
                                <form action="{{ route('feed.destroy', $feed) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">No feed found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>@extends('layouts.app')

        @section('content')
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Hens</h1>
                    <a href="{{ route('hens.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">+ Add Hens</a>
                </div>
        
                @if (session('success'))
                    <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6 shadow-sm">{{ session('success') }}</div>
                @endif
        
                <!-- Monthly Hen Chart -->
                <div class="mb-8">
                    <canvas id="henChart" class="w-full h-64"></canvas>
                </div>
        
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-4 text-gray-700 font-semibold uppercase">Breed</th>
                                <th class="p-4 text-gray-700 font-semibold uppercase">Quantity</th>
                                <th class="p-4 text-gray-700 font-semibold uppercase">Working</th>
                                <th class="p-4 text-gray-700 font-semibold uppercase">Age (Months)</th>
                                <th class="p-4 text-gray-700 font-semibold uppercase">Entry Date</th>
                                <th class="p-4 text-gray-700 font-semibold uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hens as $hen)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-200">
                                    <td class="p-4">{{ $hen->breed }}</td>
                                    <td class="p-4">{{ $hen->quantity }}</td>
                                    <td class="p-4">{{ $hen->working }}</td>
                                    <td class="p-4">{{ $hen->age }}</td>
                                    <td class="p-4">{{ $hen->entry_date->format('Y-m-d') }}</td>
                                    <td class="p-4 flex space-x-3">
                                        <a href="{{ route('hens.edit', $hen) }}" class="text-green-600 hover:text-green-800 font-medium">Edit</a>
                                        <form action="{{ route('hens.destroy', $hen) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium" 
                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">No hens found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const henCtx = document.getElementById('henChart').getContext('2d');
            new Chart(henCtx, {
                type: 'line',
                data: {
                    labels: {{ json_encode($henLabels) }},
                    datasets: [{
                        label: 'Hens Added',
                        data: {{ json_encode($henData) }},
                        fill: false,
                        borderColor: '#f97316',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        </script>
        @endsection
    </div>
</div>
@endsection