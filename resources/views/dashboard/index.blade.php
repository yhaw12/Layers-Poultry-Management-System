@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl overflow-hidden">
        <!-- Date Filter -->
        <section class="mb-8">
            <form method="GET" class="container-box">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[150px]">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $start ?? now()->startOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $end ?? now()->endOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200">
                        Filter
                    </button>
                </div>
            </form>
        </section>

        <!-- Alerts (Admin Only) -->
        @role('admin')
            <section id="alerts-section" class="mb-8 relative">
                <div class="container-box bg-white dark:bg-gray-900 shadow-md rounded-lg p-6">
                    <!-- Close Button -->
                    <button id="close-alerts" class="absolute top-4 right-4 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:ring-2 focus:ring-blue-500 rounded-md p-1 transition duration-200" title="Close Alerts" aria-label="Close Alerts">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Session Messages -->
                    @if (session('error'))
                        <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-2xl mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded-2xl mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Alerts Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Alerts
                        </h3>
                        @if ($alerts->isNotEmpty())
                            <form id="dismiss-all-form" action="{{ route('alerts.dismiss-all') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" id="dismiss-all-btn" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 text-sm font-medium transition duration-200" aria-label="Dismiss All Alerts">
                                    Dismiss All
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Alerts Content -->
                    <div id="alerts-content">
                        @if ($alerts->isNotEmpty())
                            <ul class="space-y-3">
                                @foreach ($alerts as $item)
                                    <li class="list-item p-3 bg-gray-50 dark:bg-gray-700 rounded-md transition-opacity duration-300" data-alert-id="{{ $item->id }}">
                                        <div class="flex justify-between items-center">
                                            <a href="{{ $item->url ?? '#' }}" class="{{ $item->type === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : ($item->type === 'sale' ? 'text-green-600 dark:text-green-400' : ($item->type === 'critical' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400')) }} hover:underline">
                                                {{ $item->message }}
                                            </a>
                                            @if ($item->is_read)
                                                <span class="text-gray-500 dark:text-gray-400 text-sm">Read</span>
                                            @elseif ($item->id)
                                                <form action="{{ route('alerts.read', $item->id) }}" method="POST" class="inline dismiss-single-form">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Mark as Read</button>
                                                </form>
                                            @else
                                                <span class="text-gray-500 dark:text-gray-400 text-sm">Cannot mark as read (No ID)</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            {{ $alerts->links() }}
                        @else
                            <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No active alerts at this time.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endrole

        <!-- Role-Specific Sections -->
        <!-- Daily Instructions (Labourer) -->
        @role('labourer')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Daily Instructions</h2>
                <div class="container-box">
                    @if ($dailyInstructions->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($dailyInstructions as $item)
                                <li class="list-item">
                                    <span class="highlight">{{ $item->content }}</span> (Posted: {{ $item->created_at->format('Y-m-d H:i') }})
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No instructions for today.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Flock Health Summary (Farm Manager) -->
        @role('farm_manager')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Flock Health Summary</h2>
                <div class="container-box">
                    @if ($healthSummary->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($healthSummary as $item)
                                <li class="list-item">
                                    <span class="highlight">{{ $item->date->format('Y-m-d') }}</span>: {{ $item->checks }} checks, {{ $item->unhealthy }} unhealthy
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No recent health checks.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Vaccination Schedule (Veterinarian) -->
        @role('veterinarian')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Vaccination Schedule</h2>
                <div class="container-box">
                    @if ($vaccinationSchedule->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($vaccinationSchedule as $item)
                                <li class="list-item">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            <span class="highlight">{{ $item->vaccine_name }}</span> (Due: {{ $item->due_date->format('Y-m-d') }})
                                        </span>
                                        <form action="{{ route('vaccinations.complete', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Mark as Complete</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No upcoming vaccinations.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Supplier Quick Links (Inventory Manager) -->
        @role('inventory_manager')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Suppliers</h2>
                <div class="container-box">
                    @if ($suppliers->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($suppliers as $item)
                                <li class="list-item">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            <span class="highlight">{{ $item->name }}</span> ({{ $item->contact_info }})
                                        </span>
                                        <div>
                                            <a href="{{ route('inventory.create', ['supplier_id' => $item->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mr-4">Add Inventory</a>
                                            <a href="{{ route('suppliers.show', $item->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View Details</a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No suppliers found.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Financial Summary (Admins with Permission) -->
        @can('manage_finances')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Financial Summary</h2>
                @if (isset($totalExpenses, $totalIncome, $profit))
                    @php
                        $cards = [
                            ['label' => 'Expenses', 'value' => $totalExpenses ?? 0, 'icon' => '💸', 'color' => 'red', 'trend' => $expenseTrend ?? []],
                            ['label' => 'Income', 'value' => $totalIncome ?? 0, 'icon' => '💰', 'color' => 'green', 'trend' => $incomeTrend ?? []],
                            ['label' => 'Profit', 'value' => $profit ?? 0, 'icon' => '📈', 'color' => ($profit ?? 0) >= 0 ? 'green' : 'red', 'trend' => $profitTrend ?? []],
                        ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($cards as $card)
                            <div class="container-box">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-base sm:text-lg">{{ $card['label'] }}</h3>
                                    <span class="text-xl sm:text-2xl text-{{ $card['color'] }}-500">{{ $card['icon'] }}</span>
                                </div>
                                <p class="text-xl sm:text-2xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-4 truncate">
                                    ${{ number_format($card['value'], 2) }}
                                </p>
                                <div class="relative h-12 mt-4">
                                    <canvas id="{{ strtolower($card['label']) }}Trend" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded-2xl" role="alert">
                        Financial data is currently unavailable.
                    </div>
                @endif
            </section>
        @else
            <section class="mb-8">
                <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded-2xl" role="alert">
                    You do not have permission to view the financial summary.
                </div>
            </section>
        @endcan

        <!-- Pending Approvals (Admins or Finance Managers) -->
        @if ($pendingApprovals->isNotEmpty() && (auth()->user()->hasRole('admin') || auth()->user()->hasPermissionTo('manage_finances')))
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Pending Approvals</h2>
                <div class="container-box">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" onclick="sortTable(0, 'date')">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" onclick="sortTable(1, 'number')">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" onclick="sortTable(2, 'number')">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" onclick="sortTable(3, 'date')">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Source</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($pendingApprovals as $approval)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $approval->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($approval->type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">${{ number_format($approval->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $approval->date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if ($approval->source_type === \App\Models\Sale::class)
                                            Sale #{{ $approval->source_id }}
                                        @elseif ($approval->source_type === \App\Models\Expense::class)
                                            Expense: {{ $approval->source->category }}
                                        @elseif ($approval->source_type === \App\Models\Income::class)
                                            Income: {{ $approval->source->source }}
                                        @elseif ($approval->source_type === \App\Models\Order::class)
                                            Order #{{ $approval->source_id }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('transactions.approve', $approval->id) }}" class="text-green-600 dark:text-green-400 hover:underline">Approve</a>
                                        <a href="{{ route('transactions.reject', $approval->id) }}" class="text-red-600 dark:text-red-400 hover:underline ml-4">Reject</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Key Performance Indicators (KPIs) -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Performance Indicators (KPIs)</h2>
            @php
                $groupedKpis = [
                    'Flock Statistics' => [
                        ['label' => 'Chicks', 'value' => $chicks ?? 0, 'icon' => '🐤'],
                        ['label' => 'Layers', 'value' => $layerBirds ?? 0, 'icon' => '🐓'],
                        ['label' => 'Broilers', 'value' => $broilerBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'total Birds', 'value' => $totalBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'Mortality %', 'value' => number_format($mortalityRate ?? 0, 2), 'icon' => '⚰️'],
                    ],
                    'Production' => [
                        ['label' => 'Egg Crates', 'value' => $metrics['egg_crates'] ?? 0, 'icon' => '🥚'],
                        ['label' => 'Feed (kg)', 'value' => $metrics['feed_kg'] ?? 0, 'icon' => '🌾'],
                        ['label' => 'FCR', 'value' => number_format($fcr ?? 0, 2), 'icon' => '⚖️'],
                    ],
                    'Operations' => [
                        ['label' => 'Employees', 'value' => $employees ?? 0, 'icon' => '👨‍🌾'],
                        ['label' => 'Payroll', 'value' => number_format($payroll ?? 0, 2), 'icon' => '💵'],
                        ['label' => 'Sales', 'value' => $metrics['sales'] ?? 0, 'icon' => '🛒'],
                        ['label' => 'Customers', 'value' => $metrics['customers'] ?? 0, 'icon' => '👥'],
                        ['label' => 'Med Bought', 'value' => $metrics['medicine_buy'] ?? 0, 'icon' => '💊'],
                        ['label' => 'Med Used', 'value' => $metrics['medicine_use'] ?? 0, 'icon' => '🩺'],
                    ],
                ];
            @endphp
            @foreach ($groupedKpis as $group => $kpis)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $group }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($kpis as $item)
                            @if ($group === 'Operations' && in_array($item['label'], ['Employees', 'Payroll', 'Sales', 'Customers']))
                                @role('admin')
                                    <div class="container-box">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">{{ $item['label'] }}</h4>
                                            <span class="text-xl sm:text-2xl">{{ $item['icon'] }}</span>
                                        </div>
                                        <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $item['value'] }}</p>
                                    </div>
                                @endrole
                            @else
                                <div class="container-box">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">{{ $item['label'] }}</h4>
                                        <span class="text-xl sm:text-2xl">{{ $item['icon'] }}</span>
                                    </div>
                                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $item['value'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </section>

        <!-- Payroll Status (Admin/Accountant) -->
        @if (auth()->user()->hasAnyRole(['admin', 'accountant']))
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Payroll Status
                </h2>
                <div class="container-box">
                    @if ($payrollStatus->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" onclick="sortTable(0, 'date')">Pay Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" onclick="sortTable(1, 'number')">Employees</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" onclick="sortTable(2, 'number')">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($payrollStatus as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <td class="px-4 py-3 text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $item->date }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item->employees }} {{ Str::plural('employee', $item->employees) }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-green-600 dark:text-green-400">${{ number_format($item->total, 2) }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $item->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <a href="{{ route('payroll.index', ['start_date' => $item->date, 'end_date' => $item->date]) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Add Pagination Links -->
                            {{ $payrollStatus->links() }}
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No recent payroll activity for the selected date range.</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Try adjusting the date filter or
                                <form action="{{ route('payroll.generate') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline">generate monthly payroll</button>
                                </form>.
                            </p>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        <!-- Production Trends -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Production Trends</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="chart-container">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title">Egg Trend</h4>
                        <select id="eggChartType" class="chart-select">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>
                    <div class="relative h-64">
                        <canvas id="eggTrend" class="w-full h-full"></canvas>
                    </div>
                    @if (!isset($eggTrend) || $eggTrend->isEmpty())
                        <p class="no-data">No egg data available.</p>
                    @endif
                </div>
                <div class="chart-container">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title">Feed Trend</h4>
                        <select id="feedChartType" class="chart-select">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>
                    <div class="relative h-64">
                        <canvas id="feedTrend" class="w-full h-full"></canvas>
                    </div>
                    @if (!isset($feedTrend) || $feedTrend->isEmpty())
                        <p class="no-data">No feed data available.</p>
                    @endif
                </div>
                @role('admin')
                    <div class="chart-container">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="chart-title">Sales Trend</h4>
                            <select id="salesChartType" class="chart-select">
                                <option value="line">Line</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                        <div class="relative h-64">
                            <canvas id="salesTrend" class="w-full h-full"></canvas>
                        </div>
                        @if (!isset($salesTrend) || $salesTrend->isEmpty())
                            <p class="no-data">No sales data available.</p>
                        @endif
                    </div>
                @endrole
            </div>
        </section>

        <!-- Income Trend -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Income Trend</h2>
            <div class="chart-container">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="chart-title">Income Trend</h4>
                    <select id="incomeChartType" class="chart-select">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <div class="relative h-64">
                    <canvas id="incomeChart" class="w-full h-full"></canvas>
                </div>
                @if (!isset($incomeLabels) || empty($incomeLabels))
                    <p class="no-data">No income data available.</p>
                @endif
            </div>
        </section>

        <!-- Invoice Status (Admin Only) -->
        @role('admin')
            <section class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Invoice Status</h2>
                    <form action="{{ route('dashboard.export') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200">
                            Export to CSV
                        </button>
                    </form>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="chart-container">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="chart-title">Invoice Status Distribution</h4>
                            <select id="invoiceChartType" class="chart-select">
                                <option value="line">Line</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                        <div class="relative h-64">
                            <canvas id="invoiceStatus" class="w-full h-full"></canvas>
                        </div>
                        @if (!isset($invoiceStatuses) || array_sum($invoiceStatuses) == 0)
                            <p class="no-data">No invoice status data available.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endrole

        <!-- Monthly Income Summary -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Monthly Income Summary
      </h2>
      <div class="container-box bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl">
        @if (!empty($monthlyIncome))
          <ul class="space-y-3">
            @foreach ($monthlyIncome as $month => $amount)
              <li class="list-item p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                <span class="highlight font-medium text-green-600 dark:text-green-400">{{ $month }}</span>:
                ${{ number_format($amount, 2) }}
              </li>
            @endforeach
          </ul>
        @else
          <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No monthly income data available.</p>
        @endif
      </div>
    </section>

    <!-- Vaccination Overview -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Vaccination Overview
      </h2>
      <div class="container-box bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl">
        <div class="mb-4">
          <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Upcoming Vaccinations</h4>
          <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $upcomingVaccinations ?? 0 }}</p>
        </div>
      </div>
    </section>

    <!-- Transaction Overview -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Transaction Overview
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="container-box bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl hover:border-indigo-500 border-2 border-transparent">
          <a href="{{ route('transactions.index') }}" class="block" aria-label="View all transactions">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <svg class="w-8 h-8 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Transactions</h4>
              </div>
              <button
                class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105"
                aria-label="View all pending transactions"
              >
                View All
              </button>
            </div>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $pendingTransactions ?? 0 }}</p>
            <div class="h-12 mt-4">
              <canvas id="pendingTransactionsTrend" class="w-full h-full"></canvas>
            </div>
          </a>
        </div>
        <div class="container-box bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl hover:border-indigo-500 border-2 border-transparent">
          <a href="{{ route('transactions.index') }}" class="block" aria-label="View total transaction amount">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <svg class="w-8 h-8 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Transaction Amount</h4>
              </div>
              <button
                class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105"
                aria-label="View all transactions"
              >
                View All
              </button>
            </div>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($totalTransactionAmount ?? 0, 2) }}</p>
            <div class="h-12 mt-4">
              <canvas id="totalTransactionAmountTrend" class="w-full h-full"></canvas>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- Order Overview -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        Order Overview
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="container-box bg-gradient-to-r from-purple-50 to-white dark:from-purple-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl hover:border-purple-500 border-2 border-transparent">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <svg class="w-8 h-8 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Orders</h4>
            </div>
            <a
              href="{{ route('orders.create') }}"
              class="bg-purple-600 text-white py-1 px-3 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-sm font-medium transition duration-200 transform hover:scale-105"
              aria-label="Create a new order"
            >
              Create Order
            </a>
          </div>
          <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $pendingOrders ?? 0 }}</p>
          <div class="mt-4">
            <div class="relative pt-1">
              <div class="flex mb-2 items-center justify-between">
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Completion</span>
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $completionPercentage ?? 0 }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-purple-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $completionPercentage ?? 0 }}%"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="container-box bg-gradient-to-r from-purple-50 to-white dark:from-purple-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl hover:border-purple-500 border-2 border-transparent">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <svg class="w-8 h-8 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Order Amount</h4>
            </div>
            <a
              href="{{ route('orders.index') }}"
              class="bg-purple-600 text-white py-1 px-3 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-sm font-medium transition duration-200 transform hover:scale-105"
              aria-label="View all orders"
            >
              View All
            </a>
          </div>
          <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($totalOrderAmount ?? 0, 2) }}</p>
          <div class="h-12 mt-4">
            <canvas id="totalOrderAmountTrend" class="w-full h-full"></canvas>
          </div>
        </div>
      </div>
    </section>

    <!-- Payroll Overview -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Payroll Overview
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="container-box bg-gradient-to-r from-teal-50 to-white dark:from-teal-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl hover:border-teal-500 border-2 border-transparent">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <svg class="w-8 h-8 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Payroll</h4>
            </div>
            <a
              href="{{ route('payroll.index') }}"
              class="bg-teal-600 text-white py-1 px-3 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-sm font-medium transition duration-200 transform hover:scale-105"
              aria-label="View all payroll records"
            >
              View All
            </a>
          </div>
          <p class="text-3xl font-bold text-teal-600 dark:text-teal-400">${{ number_format($totalPayroll ?? 0, 2) }}</p>
        </div>
        <div class="container-box bg-gradient-to-r from-teal-50 to-white dark:from-teal-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl hover:border-teal-500 border-2 border-transparent">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <svg class="w-8 h-8 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Payrolls</h4>
            </div>
            <a
              href="{{ route('payroll.index') }}"
              class="bg-teal-600 text-white py-1 px-3 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-sm font-medium transition duration-200 transform hover:scale-105"
              aria-label="View all pending payrolls"
            >
              View All
            </a>
          </div>
          <p class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ $pendingPayrolls ?? 0 }}</p>
        </div>
      </div>
    </section>

    <!-- Recent Sales -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Recent Sales
      </h2>
      <div class="container-box bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl">
        <div class="flex mb-4 space-x-2">
          <button
            class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-green-500 focus:border-green-500 transition duration-200"
            data-tab="egg"
            aria-label="Show egg sales"
          >
            Egg Sales
          </button>
          <button
            class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-green-500 focus:border-green-500 transition duration-200"
            data-tab="bird"
            aria-label="Show bird sales"
          >
            Bird Sales
          </button>
        </div>
        <div class="tab-content" id="egg-sales">
          <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
            <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Egg Sales</h4>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">${{ number_format($eggSales ?? 0, 2) }}</p>
          </div>
        </div>
        <div class="tab-content hidden" id="bird-sales">
          <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
            <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Bird Sales</h4>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">${{ number_format($birdSales ?? 0, 2) }}</p>
          </div>
        </div>
        <div class="chart-container mt-6">
          <div class="flex items-center justify-between mb-2">
            <h4 class="chart-title text-lg font-medium text-gray-700 dark:text-gray-300">Sales Comparison</h4>
            <select id="salesComparisonChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select sales comparison chart type">
              <option value="line">Line</option>
              <option value="bar">Bar</option>
            </select>
          </div>
          <div class="relative h-64">
            <canvas id="salesComparison" class="w-full h-full"></canvas>
          </div>
          @if (!isset($salesComparison) || $salesComparison->isEmpty())
            <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No sales comparison data available.</p>
          @endif
        </div>
      </div>
    </section>

    <!-- Recent Mortalities -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Recent Mortalities
      </h2>
      <div class="container-box bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl">
        <div class="flex justify-between mb-4">
          <div class="relative">
            <select
              class="border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              id="mortalityFilter"
              aria-label="Filter mortalities by cause"
            >
              <option value="all">All Causes</option>
              <option value="disease">Disease</option>
              <option value="injury">Injury</option>
            </select>
          </div>
          <a
            href="{{ route('mortalities.create') }}"
            class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 focus:ring-2 focus:ring-red-500 transition duration-200 transform hover:scale-105"
            aria-label="Log a new mortality"
          >
            Log Mortality
          </a>
        </div>
        <div class="chart-container">
          <div class="flex items-center justify-between mb-2">
            <h4 class="chart-title text-lg font-medium text-gray-700 dark:text-gray-300">Mortality Trend</h4>
            <select id="mortalityTrendChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select mortality trend chart type">
              <option value="line">Line</option>
              <option value="bar">Bar</option>
            </select>
          </div>
          <div class="relative h-64">
            <canvas id="mortalityTrend" class="w-full h-full"></canvas>
          </div>
          @if (!isset($mortalityTrend) || $mortalityTrend->isEmpty())
            <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No mortality data available.</p>
          @endif
        </div>
      </div>
    </section>
  </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@push('scripts')
    <script>
     // Chart instances
    let eggChart, feedChart, salesChart, invoiceStatusChart, expensesTrendChart, incomeTrendChart, profitTrendChart, pendingTransactionsTrendChart, totalTransactionAmountTrendChart, totalOrderAmountTrendChart, salesComparisonChart, mortalityTrendChart;

    /**
     * Creates a full chart with options for type selection.
     * @param {string} canvasId - The ID of the canvas element.
     * @param {string} typeSelectorId - The ID of the select element for chart type.
     * @param {object} data - The data for the chart.
     * @param {object} config - The configuration for the chart.
     */
    function createChart(canvasId, typeSelectorId, data, config) {
        try {
            globalLoader.show(`Loading ${config.title} chart...`);
            const ctx = document.getElementById(canvasId)?.getContext('2d');
            if (!ctx) {
                throw new Error(`Canvas element with ID '${canvasId}' not found.`);
            }
            // Destroy existing chart if it exists to avoid overlaps
            if (window[canvasId + 'Chart']) {
                window[canvasId + 'Chart'].destroy();
            }
            // Create new chart
            window[canvasId + 'Chart'] = new Chart(ctx, {
                type: document.getElementById(typeSelectorId)?.value || config.type || 'line',
                data: config.data || data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: config.title || '',
                            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151',
                            font: { size: 16, weight: 'bold' }
                        },
                        legend: {
                            labels: {
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151',
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#1f2937' : '#ffffff',
                            titleColor: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151',
                            bodyColor: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151',
                            borderColor: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#4b5563' : '#d1d5db',
                            borderWidth: 1,
                            padding: 12
                        }
                    },
                    scales: config.scales || {
                        x: {
                            title: { display: true, text: config.xAxis || 'Date' },
                            ticks: {
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151',
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#4b5563' : '#e5e7eb',
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: config.yAxis || '' },
                            ticks: {
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151'
                            },
                            grid: {
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#4b5563' : '#e5e7eb'
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuad',
                        onComplete: () => {
                            console.log(`Chart '${canvasId}' rendered successfully.`);
                            globalLoader.hide();
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });
            // Add change listener for chart type
            const typeSelector = document.getElementById(typeSelectorId);
            if (typeSelector) {
                typeSelector.addEventListener('change', () => {
                    window[canvasId + 'Chart'].destroy();
                    window[canvasId + 'Chart'] = new Chart(ctx, {
                        type: typeSelector.value,
                        data: config.data || data,
                        options: window[canvasId + 'Chart'].options
                    });
                });
            }
        } catch (error) {
            console.error(`Failed to create chart '${canvasId}':`, error);
            const container = document.getElementById(canvasId)?.parentElement;
            if (container && !container.querySelector('.no-data')) {
                container.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">Failed to load chart data.</p>');
            }
            globalLoader.hide();
        }
    }

    /**
     * Creates a sparkline (mini) chart for trends.
     * @param {string} canvasId - The ID of the canvas element.
     * @param {array} data - The data for the sparkline.
     * @param {string} color - The color for the line.
     */
    function createSparklineChart(canvasId, data, color) {
        try {
            globalLoader.show('Loading trend chart...');
            const ctx = document.getElementById(canvasId)?.getContext('2d');
            if (!ctx) {
                throw new Error(`Canvas element with ID '${canvasId}' not found.`);
            }
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        data: data.map(d => d.value || d.count || d.amount),
                        borderColor: color,
                        backgroundColor: color + '33',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutCubic',
                        onComplete: () => {
                            console.log(`Sparkline '${canvasId}' rendered successfully.`);
                            globalLoader.hide();
                        }
                    }
                }
            });
        } catch (error) {
            console.error(`Failed to create sparkline chart '${canvasId}':`, error);
            const container = document.getElementById(canvasId)?.parentElement;
            if (container && !container.querySelector('.no-data')) {
                container.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No data available.</p>');
            }
            globalLoader.hide();
        }
    }

    // Data definitions
    const eggTrendData = {
        labels: @json($eggTrend ? $eggTrend->pluck('date') : []),
        datasets: [{
            label: 'Egg Production (Crates)',
            data: @json($eggTrend ? $eggTrend->pluck('crates') : []),
            borderColor: '#10b981',
            backgroundColor: '#10b98188',
            fill: true,
            tension: 0.3
        }]
    };

    const feedTrendData = {
        labels: @json($feedTrend ? $feedTrend->pluck('date') : []),
        datasets: [{
            label: 'Feed Consumption (kg)',
            data: @json($feedTrend ? $feedTrend->pluck('kg') : []),
            borderColor: '#f97316',
            backgroundColor: '#f9731688',
            fill: true,
            tension: 0.3
        }]
    };

    const salesTrendData = {
        labels: @json($salesTrend ? $salesTrend->pluck('date') : []),
        datasets: [{
            label: 'Sales ($)',
            data: @json($salesTrend ? $salesTrend->pluck('amount') : []),
            borderColor: '#3b82f6',
            backgroundColor: '#3b82f688',
            fill: true,
            tension: 0.3
        }]
    };

    const invoiceStatusData = {
        labels: ['Pending', 'Paid', 'Partially Paid', 'Overdue'],
        datasets: [{
            label: 'Invoices',
            data: [
                @json($pendingSales ?? 0),
                @json($paidSales ?? 0),
                @json($partiallyPaidSales ?? 0),
                @json($overdueSales ?? 0)
            ],
            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
            borderColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
            borderWidth: 1
        }]
    };

    const expensesTrendData = {
        labels: @json($expenseTrend ? $expenseTrend->pluck('date') : []),
        datasets: [{
            label: 'Expenses ($)',
            data: @json($expenseTrend ? $expenseTrend->pluck('amount') : []),
            borderColor: '#ef4444',
            backgroundColor: '#ef444488',
            fill: true,
            tension: 0.3
        }]
    };

    const incomeTrendData = {
        labels: @json($incomeTrend ? $incomeTrend->pluck('date') : []),
        datasets: [{
            label: 'Income ($)',
            data: @json($incomeTrend ? $incomeTrend->pluck('amount') : []),
            borderColor: '#10b981',
            backgroundColor: '#10b98188',
            fill: true,
            tension: 0.3
        }]
    };

    const profitTrendData = {
        labels: @json($profitTrend ? $profitTrend->pluck('date') : []),
        datasets: [{
            label: 'Profit ($)',
            data: @json($profitTrend ? $profitTrend->pluck('amount') : []),
            borderColor: '#3b82f6',
            backgroundColor: '#3b82f688',
            fill: true,
            tension: 0.3
        }]
    };

    const pendingTransactionsTrendData = {
        labels: @json($pendingTransactionsTrend ? $pendingTransactionsTrend->pluck('date') : []),
        datasets: [{
            label: 'Pending Transactions',
            data: @json($pendingTransactionsTrend ? $pendingTransactionsTrend->pluck('count') : []),
            borderColor: '#eab308',
            backgroundColor: '#eab30888',
            fill: true,
            tension: 0.3
        }]
    };

    const totalTransactionAmountTrendData = {
        labels: @json($totalTransactionAmountTrend ? $totalTransactionAmountTrend->pluck('date') : []),
        datasets: [{
            label: 'Total Transaction Amount ($)',
            data: @json($totalTransactionAmountTrend ? $totalTransactionAmountTrend->pluck('amount') : []),
            borderColor: '#8b5cf6',
            backgroundColor: '#8b5cf688',
            fill: true,
            tension: 0.3
        }]
    };

    const totalOrderAmountTrendData = {
        labels: @json($totalOrderAmountTrend ? $totalOrderAmountTrend->pluck('date') : []),
        datasets: [{
            label: 'Total Order Amount ($)',
            data: @json($totalOrderAmountTrend ? $totalOrderAmountTrend->pluck('amount') : []),
            borderColor: '#6b7280',
            backgroundColor: '#6b728088',
            fill: true,
            tension: 0.3
        }]
    };

    const salesComparisonData = {
        labels: @json($salesComparison ? $salesComparison->pluck('date') : []),
        datasets: [
            {
                label: 'Egg Sales ($)',
                data: @json($salesComparison ? $salesComparison->pluck('egg_sales') : []),
                borderColor: '#10b981',
                backgroundColor: '#10b98188',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Bird Sales ($)',
                data: @json($salesComparison ? $salesComparison->pluck('bird_sales') : []),
                borderColor: '#3b82f6',
                backgroundColor: '#3b82f688',
                fill: true,
                tension: 0.3
            }
        ]
    };

    const mortalityTrendData = {
        labels: @json($mortalityTrend ? $mortalityTrend->pluck('date') : []),
        datasets: [{
            label: 'Mortalities',
            data: @json($mortalityTrend ? $mortalityTrend->pluck('count') : []),
            borderColor: '#ef4444',
            backgroundColor: '#ef444488',
            fill: true,
            tension: 0.3
        }]
    };

    // Initialize charts
    document.addEventListener('DOMContentLoaded', () => {
        // Fallback to hide loader after 15 seconds
        setTimeout(() => {
            if (globalLoader.isShowing) {
                console.warn('Fallback: Hiding loader after 15 seconds');
                globalLoader.hide();
            }
        }, 15000);

        // Load Alerts (Admin Only)
        @role('admin')
            try {
                const alertsContent = document.getElementById('alerts-content');
                if (alertsContent) {
                    globalLoader.show('Loading alerts...');
                    console.log('Loading alerts section');
                    const alertsExist = @json($alerts->isNotEmpty());
                    const sessionMessages = @json(session('error') || session('success'));
                    if (!alertsExist && !sessionMessages) {
                        document.getElementById('alerts-section').style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Failed to load alerts:', error);
            } finally {
                console.log('Alerts section processed');
                globalLoader.hide();
            }
        @endrole

        // Egg Trend Chart
        @if (isset($eggTrend) && $eggTrend->isNotEmpty())
            createChart('eggTrend', 'eggChartType', eggTrendData, {
                type: 'line',
                title: 'Egg Production Trend',
                yAxis: 'Crates',
                xAxis: 'Date',
                data: eggTrendData
            });
        @else
            try {
                const eggTrendContainer = document.getElementById('eggTrend')?.parentElement;
                if (eggTrendContainer && !eggTrendContainer.querySelector('.no-data')) {
                    eggTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No egg data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Feed Trend Chart
        @if (isset($feedTrend) && $feedTrend->isNotEmpty())
            createChart('feedTrend', 'feedChartType', feedTrendData, {
                type: 'line',
                title: 'Feed Consumption Trend',
                yAxis: 'Kilograms',
                xAxis: 'Date',
                data: feedTrendData
            });
        @else
            try {
                const feedTrendContainer = document.getElementById('feedTrend')?.parentElement;
                if (feedTrendContainer && !feedTrendContainer.querySelector('.no-data')) {
                    feedTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No feed data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Sales Trend Chart
        @if (isset($salesTrend) && $salesTrend->isNotEmpty())
            @role('admin')
                createChart('salesTrend', 'salesChartType', salesTrendData, {
                    type: 'line',
                    title: 'Sales Trend',
                    yAxis: 'Amount ($)',
                    xAxis: 'Date',
                    data: salesTrendData
                });
            @else
                try {
                    const salesTrendContainer = document.getElementById('salesTrend')?.parentElement;
                    if (salesTrendContainer && !salesTrendContainer.querySelector('.no-data')) {
                        salesTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No sales data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endrole
        @endif

        // Invoice Status Chart
        @if (isset($invoiceStatuses) && array_sum([$pendingSales, $paidSales, $partiallyPaidSales, $overdueSales]) > 0)
            createChart('invoiceStatus', 'invoiceChartType', invoiceStatusData, {
                type: 'bar',
                title: 'Invoice Status Distribution',
                yAxis: 'Count',
                xAxis: 'Status',
                data: invoiceStatusData
            });
        @else
            try {
                const invoiceContainer = document.getElementById('invoiceStatus')?.parentElement;
                if (invoiceContainer && !invoiceContainer.querySelector('.no-data')) {
                    invoiceContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No invoice status data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Expenses Trend Chart
        @if (isset($expenseTrend) && $expenseTrend->isNotEmpty())
            createSparklineChart('expensesTrend', @json($expenseTrend), '#ef4444');
        @else
            try {
                const expensesTrendContainer = document.getElementById('expensesTrend')?.parentElement;
                if (expensesTrendContainer && !expensesTrendContainer.querySelector('.no-data')) {
                    expensesTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No expenses data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Income Trend Chart
        @if (isset($incomeTrend) && $incomeTrend->isNotEmpty())
            createSparklineChart('incomeTrend', @json($incomeTrend), '#10b981');
        @else
            try {
                const incomeTrendContainer = document.getElementById('incomeTrend')?.parentElement;
                if (incomeTrendContainer && !incomeTrendContainer.querySelector('.no-data')) {
                    incomeTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No income data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Profit Trend Chart
        @if (isset($profitTrend) && $profitTrend->isNotEmpty())
            createSparklineChart('profitTrend', @json($profitTrend), @json(($profit ?? 0) >= 0 ? '#10b981' : '#ef4444'));
        @else
            try {
                const profitTrendContainer = document.getElementById('profitTrend')?.parentElement;
                if (profitTrendContainer && !profitTrendContainer.querySelector('.no-data')) {
                    profitTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No profit data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Pending Transactions Trend Chart
        @if (isset($pendingTransactionsTrend) && $pendingTransactionsTrend->isNotEmpty())
            createSparklineChart('pendingTransactionsTrend', @json($pendingTransactionsTrend), '#eab308');
        @else
            try {
                const pendingTransactionsTrendContainer = document.getElementById('pendingTransactionsTrend')?.parentElement;
                if (pendingTransactionsTrendContainer && !pendingTransactionsTrendContainer.querySelector('.no-data')) {
                    pendingTransactionsTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No pending transactions data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Total Transaction Amount Trend Chart
        @if (isset($totalTransactionAmountTrend) && $totalTransactionAmountTrend->isNotEmpty())
            createChart('totalTransactionAmountTrend', 'totalTransactionAmountChartType', totalTransactionAmountTrendData, {
                type: 'line',
                title: 'Total Transaction Amount Trend',
                yAxis: 'Amount ($)',
                xAxis: 'Date',
                data: totalTransactionAmountTrendData
            });
        @else
            try {
                const totalTransactionAmountTrendContainer = document.getElementById('totalTransactionAmountTrend')?.parentElement;
                if (totalTransactionAmountTrendContainer && !totalTransactionAmountTrendContainer.querySelector('.no-data')) {
                    totalTransactionAmountTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No transaction amount data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Total Order Amount Trend Chart
        @if (isset($totalOrderAmountTrend) && $totalOrderAmountTrend->isNotEmpty())
            createChart('totalOrderAmountTrend', 'totalOrderAmountChartType', totalOrderAmountTrendData, {
                type: 'line',
                title: 'Total Order Amount Trend',
                yAxis: 'Amount ($)',
                xAxis: 'Date',
                data: totalOrderAmountTrendData
            });
        @else
            try {
                const totalOrderAmountTrendContainer = document.getElementById('totalOrderAmountTrend')?.parentElement;
                if (totalOrderAmountTrendContainer && !totalOrderAmountTrendContainer.querySelector('.no-data')) {
                    totalOrderAmountTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No order amount data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Sales Comparison Chart
        @if (isset($salesComparison) && $salesComparison->isNotEmpty())
            createChart('salesComparison', 'salesComparisonChartType', salesComparisonData, {
                type: 'line',
                title: 'Egg vs. Bird Sales Comparison',
                yAxis: 'Amount ($)',
                xAxis: 'Date',
                data: salesComparisonData
            });
        @else
            try {
                const salesComparisonContainer = document.getElementById('salesComparison')?.parentElement;
                if (salesComparisonContainer && !salesComparisonContainer.querySelector('.no-data')) {
                    salesComparisonContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No sales comparison data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Mortality Trend Chart
        @if (isset($mortalityTrend) && $mortalityTrend->isNotEmpty())
            createChart('mortalityTrend', 'mortalityTrendChartType', mortalityTrendData, {
                type: 'line',
                title: 'Mortality Trend',
                yAxis: 'Quantity',
                xAxis: 'Date',
                data: mortalityTrendData
            });
        @else
            try {
                const mortalityTrendContainer = document.getElementById('mortalityTrend')?.parentElement;
                if (mortalityTrendContainer && !mortalityTrendContainer.querySelector('.no-data')) {
                    mortalityTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data text-center text-gray-500 dark:text-gray-400 mt-4">No mortality data available.</p>');
                }
            } finally {
                globalLoader.hide();
            }
        @endif

        // Tab switching for Recent Sales
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                const tab = button.getAttribute('data-tab');
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(`${tab}-sales`).classList.remove('hidden');
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('border-green-500');
                    btn.classList.add('border-transparent');
                });
                button.classList.remove('border-transparent');
                button.classList.add('border-green-500');
            });
        });

        // Table sorting function
        function sortTable(columnIndex, type) {
            const table = document.querySelector('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isAscending = table.dataset.sortOrder !== 'asc';
            table.dataset.sortOrder = isAscending ? 'asc' : 'desc';

            rows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();
                if (type === 'number') {
                    return isAscending ? parseFloat(aValue.replace(/[^0-9.-]+/g, '')) - parseFloat(bValue.replace(/[^0-9.-]+/g, '')) :
                        parseFloat(bValue.replace(/[^0-9.-]+/g, '')) - parseFloat(aValue.replace(/[^0-9.-]+/g, ''));
                } else if (type === 'date') {
                    return isAscending ? new Date(aValue) - new Date(bValue) : new Date(bValue) - new Date(aValue);
                }
                return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            });

            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        }

        // Dismiss alerts
        const alertsSection = document.getElementById('alerts-section');
        const closeAlertsBtn = document.getElementById('close-alerts');
        if (alertsSection && closeAlertsBtn) {
            closeAlertsBtn.addEventListener('click', () => {
                alertsSection.style.transition = 'opacity 0.3s ease';
                alertsSection.style.opacity = '0';
                setTimeout(() => alertsSection.classList.add('hidden'), 300);
            });
        }
    });

    // Global loader (mock implementation)
    const globalLoader = {
        show: (message) => console.log(message),
        hide: () => console.log('Loader hidden'),
        isShowing: false // Mock property for timeout check
    };

    // Dark mode detection and chart updates
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const charts = [
            eggChart, feedChart, salesChart, invoiceStatusChart, expensesTrendChart,
            incomeTrendChart, profitTrendChart, pendingTransactionsTrendChart,
            totalTransactionAmountTrendChart, totalOrderAmountTrendChart,
            salesComparisonChart, mortalityTrendChart
        ];
        charts.forEach(chart => {
            if (chart) {
                chart.options.plugins.title.color = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151';
                chart.options.plugins.legend.labels.color = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151';
                chart.options.plugins.tooltip.backgroundColor = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#1f2937' : '#ffffff';
                chart.options.plugins.tooltip.titleColor = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151';
                chart.options.plugins.tooltip.bodyColor = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151';
                chart.options.plugins.tooltip.borderColor = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#4b5563' : '#d1d5db';
                chart.options.scales.x.ticks.color = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151';
                chart.options.scales.x.grid.color = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#4b5563' : '#e5e7eb';
                chart.options.scales.y.ticks.color = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e5e7eb' : '#374151';
                chart.options.scales.y.grid.color = window.matchMedia('(prefers-color-scheme: dark)').matches ? '#4b5563' : '#e5e7eb';
                chart.update();
            }
        });
    });
    </script>
@endpush