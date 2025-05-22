@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Date Filter -->
    <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow-md animate-fadeInUp">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 font-medium mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-gray-700 font-medium mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition duration-200">Filter</button>
        </div>
    </form>

    <!-- Financial Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Total Expenses</h2>
                <span class="text-red-500 text-2xl">💸</span>
            </div>
            <p class="text-3xl font-bold text-red-600 mt-4">${{ number_format($totalExpenses, 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">Filtered Period</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Total Egg Sales</h2>
                <span class="text-green-500 text-2xl">💰</span>
            </div>
            <p class="text-3xl font-bold text-green-600 mt-4">${{ number_format($totalIncome, 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">Filtered Period</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Profit</h2>
                <span class="text-blue-500 text-2xl">📈</span>
            </div>
            <p class="text-3xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-4">${{ number_format($profit, 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">Filtered Period</p>
        </div>
    </div>

    <!-- Inventory & Staff Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Chicks -->
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-300">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Chicks</h2>
                <span class="text-yellow-500 text-2xl">🐤</span>
            </div>
            <p class="text-3xl font-bold text-blue-600 mt-4">{{ $chickCount }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Count</p>
        </div>

        <!-- Hens -->
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-400">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Hens</h2>
                <span class="text-orange-500 text-2xl">🐔</span>
            </div>
            <p class="text-3xl font-bold text-blue-600 mt-4">{{ $henCount }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Count</p>
        </div>

        <!-- Feed -->
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-500">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Feed This Month</h2>
                <span class="text-green-500 text-2xl">🌾</span>
            </div>
            <p class="text-3xl font-bold text-blue-600 mt-4">{{ $feedQuantityThisMonth }}</p>
            <p class="text-sm text-gray-500 mt-1">kg, {{ now()->format('F Y') }}</p>
        </div>

        <!-- Eggs -->
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-600">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Eggs This Month</h2>
                <span class="text-purple-500 text-2xl">🥚</span>
            </div>
            <p class="text-3xl font-bold text-blue-600 mt-4">{{ $eggCountThisMonth }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
        </div>

        <!-- Mortality Rate -->
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Mortality Rate</h2>
                <span class="text-red-500 text-2xl">⚰️</span>
            </div>
            <p class="text-3xl font-bold {{ $mortalityRate > 5 ? 'text-red-600' : 'text-yellow-600' }} mt-4">{{ number_format($mortalityRate, 2) }}%</p>
            <p class="text-sm text-gray-500 mt-1">This Month</p>
        </div>

        <!-- Employees -->
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-800">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Employees</h2>
                <span class="text-blue-500 text-2xl">👨‍🌾</span>
            </div>
            <p class="text-3xl font-bold text-blue-600 mt-4">{{ $employeeCount }}</p>
            <p class="text-sm text-gray-500 mt-1">Active Staff</p>
        </div>

        <!-- Monthly Payroll -->
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate-fadeInUp delay-900">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-700">Monthly Payroll</h2>
                <span class="text-green-500 text-2xl">💵</span>
            </div>
            <p class="text-3xl font-bold text-green-600 mt-4">${{ number_format($monthlyPayroll, 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
        </div>
    </div>
</div>
@endsection
