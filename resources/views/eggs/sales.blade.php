{{-- eggs.sales --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Egg Sales</h2>
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow mb-6">
        <p><strong>Total Sales:</strong> {{ number_format($totalSales, 2) }}</p>
        <p><strong>Total Crates Sold:</strong> {{ number_format($totalCratesSold, 2) }}</p>
    </div>
    <table class="w-full text-left bg-white dark:bg-[#1a1a3a] rounded-2xl shadow">
        <thead>
            <tr class="text-gray-700 dark:text-gray-300">
                <th>Customer</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <th>Sale Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr class="border-t dark:border-gray-700">
                    <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>{{ number_format($sale->unit_price, 2) }}</td>
                    <td>{{ number_format($sale->total_amount, 2) }}</td>
                    <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('sales.edit', $sale) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Delete this sale?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                        <a href="{{ route('sales.invoice', $sale) }}" class="text-green-600 dark:text-green-400 hover:underline">Invoice</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 dark:text-gray-400">No sales found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $sales->links() }}
</div>
@endsection