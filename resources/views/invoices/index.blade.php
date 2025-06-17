@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Invoices</h2>
    </section>

    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($sales as $sale)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $sale->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $sale->customer->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $sale->sale_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                <span class="px-2 py-1 rounded {{ $sale->status == 'paid' ? 'bg-green-100 text-green-800' : ($sale->status == 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                <a href="{{ route('sales.invoice', ['sale' => $sale->id, 'preview' => 1]) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Preview</a>
                                <a href="{{ route('sales.invoice', $sale->id) }}" class="text-blue-600 hover:underline dark:text-blue-400">Download PDF</a>
                                @can('email-invoices')
                                    <a href="{{ route('sales.emailInvoice', $sale->id) }}" class="text-purple-600 hover:underline dark:text-purple-400">Email</a>
                                @endcan
                                @can('update-invoice-status')
                                    @if($sale->status != 'paid')
                                        <a href="{{ route('sales.updateStatus', $sale->id) }}" class="text-green-600 hover:underline dark:text-green-400">Mark Paid</a>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $sales->links() }}
        </div>
    </section>
</div>
@endsection