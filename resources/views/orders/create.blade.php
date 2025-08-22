
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Order</h2>
        </section>

        <!-- Form -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
                <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                    @csrf
                    <!-- Success/Error Messages -->
                    @if (session('error'))
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Customer -->
                    <div>
                        <label for="customer_id" class="block text-gray-700 dark:text-gray-300">Customer <span class="text-red-600">*</span></label>
                        <select name="customer_id" id="customer_id" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('customer_id') border-red-500 @enderror" required aria-describedby="customer_id-error">
                            <option value="" {{ old('customer_id') ? '' : 'selected' }} disabled>Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p id="customer_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Total Amount -->
                    <div>
                        <label for="total_amount" class="block text-gray-700 dark:text-gray-300">Total Amount <span class="text-red-600">*</span></label>
                        <input type="number" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" step="0.01"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('total_amount') border-red-500 @enderror"
                               min="0" required aria-describedby="total_amount-error">
                        @error('total_amount')
                            <p id="total_amount-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-gray-700 dark:text-gray-300">Status <span class="text-red-600">*</span></label>
                        <select name="status" id="status" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('status') border-red-500 @enderror" required aria-describedby="status-error">
                            <option value="" {{ old('status') ? '' : 'selected' }} disabled>Select Status</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                        @error('status')
                            <p id="status-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Save
                        </button>
                        <a href="{{ route('orders.index') }}"
                           class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection