@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <header class="mb-8">
        <h1 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Payroll Details</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View details for this payroll record</p>
    </header>

    @if (isset($payroll))
        <div class="relative bg-gradient-to-r from-white to-gray-50 dark:from-[#1a1a3a] dark:to-gray-800 p-4 sm:p-6 rounded-2xl shadow-lg hover:shadow-xl hover:border-blue-500 border-2 border-transparent transition-all duration-300 animate-fade-in" role="region" aria-labelledby="payroll-details-title">
            <h2 id="payroll-details-title" class="sr-only">Payroll Record Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 relative">
                <!-- Left Column: Payment Details -->
                <div class="space-y-4">
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Employee:</strong> 
                        <span>{{ isset($payroll->employee) ? $payroll->employee->name : 'Unknown Employee' }}</span>
                    </p>
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Pay Date:</strong> 
                        <span>{{ $payroll->pay_date ?? 'N/A' }}</span>
                    </p>
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Base Salary:</strong> 
                        <span>₵{{ number_format($payroll->base_salary ?? 0, 2) }}</span>
                    </p>
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Bonus:</strong> 
                        <span>₵{{ number_format($payroll->bonus ?? 0, 2) }}</span>
                    </p>
                </div>

                <!-- Divider for larger screens -->
                <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700" aria-hidden="true"></div>

                <!-- Right Column: Status and Notes -->
                <div class="space-y-4">
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Deductions:</strong> 
                        <span>₵{{ number_format($payroll->deductions ?? 0, 2) }}</span>
                    </p>
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Net Pay:</strong> 
                        <span class="text-2xl font-bold text-white bg-gradient-to-r from-green-500 to-green-600 dark:from-green-400 dark:to-green-500 px-3 py-1 rounded-md">₵{{ number_format($payroll->net_pay ?? 0, 2) }}</span>
                    </p>
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Status:</strong> 
                        <span class="px-3 py-1 text-base font-medium rounded-full {{ $payroll->status === 'paid' ? 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' }}">
                            {{ ucfirst($payroll->status ?? 'unknown') }}
                        </span>
                    </p>
                    <p class="flex items-center text-gray-900 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <strong class="font-bold text-base w-32">Notes:</strong> 
                        <span class="inline-block px-3 py-1 rounded-md {{ $payroll->notes ? '' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 italic border border-dashed border-gray-300 dark:border-gray-600' }}" aria-describedby="notes-description">
                            {{ $payroll->notes ?? 'No notes available' }}
                        </span>
                    </p>
                    <span id="notes-description" class="sr-only">Additional notes for the payroll record</span>
                </div>
            </div>
            <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:gap-4 justify-end">
                <a href="{{ route('payroll.edit', $payroll->id) }}" class="bg-blue-600 text-white py-2 px-8 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-105" aria-label="Edit payroll record">Edit</a>
                <a href="{{ route('payroll.index') }}" class="bg-gray-600 text-white py-2 px-8 rounded-lg hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 transform hover:scale-105" aria-label="Return to payroll list">Back</a>
            </div>
        </div>
    @else
        <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-2xl" role="alert">
            No payroll record found.
        </div>
    @endif
</div>
@endsection
