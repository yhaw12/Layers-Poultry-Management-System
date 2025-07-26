{{-- invoices.index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Invoices</h2>
    </section>

    <section>
        <form method="GET" class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500">
                    Filter
                </button>
            </div>
        </form>

        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
            @if($sales->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">No invoices found.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Paid</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($sales as $sale)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $sale->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $sale->customer->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $sale->sale_date->format('F j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $sale->due_date ? $sale->due_date->format('F j, Y') : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">${{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">${{ number_format($sale->paid_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    <span class="px-2 py-1 rounded text-sm
                                        {{ $sale->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                           $sale->status == 'partially_paid' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                           $sale->status == 'overdue' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                           'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                        {{ ucfirst(str_replace('_', ' ', $sale->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                    @if(!$sale->isPaid())
                                        <form action="{{ route('sales.recordPayment', $sale) }}" method="POST" class="inline no-print">
                                            @csrf
                                            <input type="number" name="amount" step="0.01" min="0.01" max="{{ $sale->total_amount - $sale->paid_amount }}" placeholder="Amount" class="border rounded p-1 dark:bg-gray-800 dark:text-white w-24">
                                            <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" class="border rounded p-1 dark:bg-gray-800 dark:text-white w-32">
                                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline">Pay</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('sales.invoice', ['sale' => $sale->id, 'preview' => 1]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Preview</a>
                                    <a href="{{ route('sales.invoice', $sale->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Download PDF</a>
                                    @can('email-invoices')
                                        <a href="{{ route('sales.emailInvoice', $sale->id) }}" class="text-purple-600 dark:text-purple-400 hover:underline">Email</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection