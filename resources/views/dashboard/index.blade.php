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

        <!-- Recent Activity -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Recent Activity</h2>
            <div class="container-box">
                <div id="recent-activity-content">
                    @if ($recentActivities->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($recentActivities as $item)
                                <li class="list-item">
                                    <span class="highlight">{{ $item->action }}</span> by {{ $item->user_name }} on {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No recent activity.</p>
                    @endif
                </div>
            </div>
        </section>

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

        <!-- Flock Breakdown -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Flock Breakdown</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div class="container-box">
                    <div class="flex items-center justify-between">
                        <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">Total Birds</h4>
                        <span class="text-xl sm:text-2xl">🐔</span>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $totalBirds ?? 0 }}</p>
                </div>
                <div class="container-box">
                    <div class="flex items-center justify-between">
                        <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">Layer Birds</h4>
                        <span class="text-xl sm:text-2xl">🐔</span>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $layerBirds ?? 0 }}</p>
                </div>
                <div class="container-box">
                    <div class="flex items-center justify-between">
                        <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">Broiler Birds</h4>
                        <span class="text-xl sm:text-2xl">🐔</span>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $broilerBirds ?? 0 }}</p>
                </div>
            </div>
        </section>

        <!-- Monthly Income Summary -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Monthly Income Summary</h2>
            <div class="container-box">
                @if (!empty($monthlyIncome))
                    <ul class="space-y-3">
                        @foreach ($monthlyIncome as $month => $amount)
                            <li class="list-item">
                                <span class="highlight">{{ $month }}</span>: ${{ number_format($amount, 2) }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data">No monthly income data available.</p>
                @endif
            </div>
        </section>

        <!-- Production Data -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Production Data</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="container-box">
                    <h4 class="chart-title">Egg Production</h4>
                    <div class="relative h-64">
                        <canvas id="eggProductionChart" class="w-full h-full"></canvas>
                    </div>
                    @if (!isset($eggProduction) || $eggProduction->isEmpty())
                        <p class="no-data">No egg production data available.</p>
                    @endif
                </div>
                <div class="container-box">
                    <h4 class="chart-title">Feed Consumption</h4>
                    <div class="relative h-64">
                        <canvas id="feedConsumptionChart" class="w-full h-full"></canvas>
                    </div>
                    @if (!isset($feedConsumption) || $feedConsumption->isEmpty())
                        <p class="no-data">No feed consumption data available.</p>
                    @endif
                </div>
            </div>
        </section>

        <!-- Vaccination Overview -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Vaccination Overview</h2>
            <div class="container-box">
                <div class="mb-4">
                    <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Upcoming Vaccinations</h4>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $upcomingVaccinations ?? 0 }}</p>
                </div>
                {{-- @if ($vaccinationLogs->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach ($vaccinationLogs as $log)
                            <li class="list-item">
                                <span class="highlight">{{ $log->vaccine_name }}</span> for Bird #{{ $log->bird->id }} on {{ $log->date_administered }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data">No recent vaccination logs.</p>
                @endif --}}
            </div>
        </section>

        <!-- Transaction Overview -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Transaction Overview</h2>
            <div class="container-box">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Transactions</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $pendingTransactions ?? 0 }}</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Transaction Amount</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($totalTransactionAmount ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Order Overview -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Order Overview</h2>
            <div class="container-box">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Orders</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $pendingOrders ?? 0 }}</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Order Amount</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($totalOrderAmount ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Payroll Overview -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Payroll Overview</h2>
            <div class="container-box">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Payroll</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($totalPayroll ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Payrolls</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $pendingPayrolls ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Sales -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Recent Sales</h2>
            <div class="container-box">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4">
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Egg Sales</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($eggSales ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Bird Sales</h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($birdSales ?? 0, 2) }}</p>
                    </div>
                </div>
                @if ($recentSales->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach ($recentSales as $sale)
                            <li class="list-item">
                                <span class="highlight">Sale #{{ $sale->id }}</span> to {{ $sale->customer->name }} for ${{ number_format($sale->total_amount, 2) }} on {{ $sale->sale_date->format('Y-m-d') }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data">No recent sales.</p>
                @endif
            </div>
        </section>

        <!-- Recent Mortalities -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Recent Mortalities</h2>
            <div class="container-box">
                @if ($recentMortalities->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach ($recentMortalities as $mortality)
                            <li class="list-item">
                                <span class="highlight">{{ $mortality->quantity }} mortalities</span> for Bird #{{ $mortality->bird->id }} on {{ $mortality->date->format('Y-m-d') }} (Cause: {{ $mortality->cause ?? 'Unknown' }})
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data">No recent mortalities.</p>
                @endif
            </div>
        </section>
    </div>
@endsection


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@push('scripts')
    <script>
        // Chart instances
        let eggChart, feedChart, salesChart, invoiceStatusChart, expensesTrendChart, incomeTrendChart, profitTrendChart;

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
                    type: document.getElementById(typeSelectorId)?.value || config.type,
                    data: config.data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: { display: true, text: config.title, font: { size: 16 } },
                            tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(0,0,0,0.8)', padding: 12 }
                        },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: config.yAxis }, grid: { color: 'rgba(0, 0, 0, 0.1)' } },
                            x: { title: { display: true, text: config.xAxis }, grid: { display: false } }
                        },
                        animation: {
                            duration: 1500, // Smooth animation
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
                    typeSelector.addEventListener('change', () => createChart(canvasId, typeSelectorId, data, config));
                }
            } catch (error) {
                console.error(`Failed to create chart '${canvasId}':`, error);
                // Display no-data message if chart fails
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
                // Create sparkline
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.date),
                        datasets: [{
                            data: data.map(d => d.value),
                            borderColor: color,
                            backgroundColor: color + '33', // Semi-transparent fill for better UX
                            fill: true, // Fill under line for visual appeal
                            tension: 0.4, // Smoother curve
                            pointRadius: 0,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false } // Disable tooltip for sparkline
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
                globalLoader.hide();
            }
        }

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

            // Load Recent Activity
            try {
                const recentActivityContent = document.getElementById('recent-activity-content');
                if (recentActivityContent) {
                    globalLoader.show('Loading recent activity...');
                    console.log('Loading recent activity section');
                    if (!@json($recentActivities->isNotEmpty())) {
                        recentActivityContent.innerHTML = '<p class="no-data">No recent activity.</p>';
                    }
                }
            } catch (error) {
                console.error('Failed to load recent activity:', error);
            } finally {
                console.log('Recent activity section processed');
                globalLoader.hide();
            }

            // Load Charts with improved UX
            @if (isset($eggTrend) && $eggTrend->isNotEmpty())
                createChart('eggTrend', 'eggChartType', @json($eggTrend), {
                    type: 'line',
                    title: 'Egg Production Trend',
                    yAxis: 'Crates',
                    xAxis: 'Date',
                    data: {
                        labels: @json($eggTrend->pluck('date')),
                        datasets: [{
                            label: 'Egg Crates',
                            data: @json($eggTrend->pluck('value')),
                            fill: true, // Fill for better visualization
                            borderColor: '#10b981',
                            backgroundColor: '#10b98188', // Semi-transparent
                            tension: 0.3 // Smoother lines
                        }]
                    }
                });
            @else
                try {
                    const eggTrendContainer = document.getElementById('eggTrend')?.parentElement;
                    if (eggTrendContainer && !eggTrendContainer.querySelector('.no-data')) {
                        eggTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No egg data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($feedTrend) && $feedTrend->isNotEmpty())
                createChart('feedTrend', 'feedChartType', @json($feedTrend), {
                    type: 'line',
                    title: 'Feed Consumption Trend',
                    yAxis: 'Kilograms',
                    xAxis: 'Date',
                    data: {
                        labels: @json($feedTrend->pluck('date')),
                        datasets: [{
                            label: 'Feed (kg)',
                            data: @json($feedTrend->pluck('value')),
                            fill: true,
                            borderColor: '#f97316',
                            backgroundColor: '#f9731688',
                            tension: 0.3
                        }]
                    }
                });
            @else
                try {
                    const feedTrendContainer = document.getElementById('feedTrend')?.parentElement;
                    if (feedTrendContainer && !feedTrendContainer.querySelector('.no-data')) {
                        feedTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No feed data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($salesTrend) && $salesTrend->isNotEmpty())
                @role('admin')
                    createChart('salesTrend', 'salesChartType', @json($salesTrend), {
                        type: 'line',
                        title: 'Sales Trend',
                        yAxis: 'Amount ($)',
                        xAxis: 'Date',
                        data: {
                            labels: @json($salesTrend->pluck('date')),
                            datasets: [{
                                label: 'Sales ($)',
                                data: @json($salesTrend->pluck('value')),
                                fill: true,
                                borderColor: '#3b82f6',
                                backgroundColor: '#3b82f688',
                                tension: 0.3
                            }]
                        }
                    });
                @else
                    try {
                        const salesTrendContainer = document.getElementById('salesTrend')?.parentElement;
                        if (salesTrendContainer && !salesTrendContainer.querySelector('.no-data')) {
                            salesTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No sales data available.</p>');
                        }
                    } finally {
                        globalLoader.hide();
                    }
                @endrole
            @endif

            @if (isset($incomeLabels) && !empty($incomeLabels))
                createChart('incomeChart', 'incomeChartType', @json($incomeData), {
                    type: 'bar',
                    title: 'Income Trend',
                    yAxis: 'Amount ($)',
                    xAxis: 'Month',
                    data: {
                        labels: @json($incomeLabels),
                        datasets: [{
                            label: 'Monthly Income',
                            data: @json($incomeData),
                            borderColor: '#4A90E2',
                            backgroundColor: '#4A90E288',
                            fill: false,
                            tension: 0.1
                        }]
                    }
                });
            @else
                try {
                    const incomeChartContainer = document.getElementById('incomeChart')?.parentElement;
                    if (incomeChartContainer && !incomeChartContainer.querySelector('.no-data')) {
                        incomeChartContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No income data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($invoiceStatuses) && array_sum($invoiceStatuses) > 0)
                createChart('invoiceStatus', 'invoiceChartType', @json($invoiceStatuses), {
                    type: 'bar',
                    title: 'Invoice Status Distribution',
                    yAxis: 'Count',
                    xAxis: 'Status',
                    data: {
                        labels: ['Pending', 'Paid', 'Partially Paid', 'Overdue'],
                        datasets: [{
                            label: 'Invoice Count',
                            data: [
                                @json($invoiceStatuses['pending'] ?? 0),
                                @json($invoiceStatuses['paid'] ?? 0),
                                @json($invoiceStatuses['partially_paid'] ?? 0),
                                @json($invoiceStatuses['overdue'] ?? 0)
                            ],
                            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
                            borderColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
                            borderWidth: 1
                        }]
                    }
                });
            @else
                try {
                    const invoiceContainer = document.getElementById('invoiceStatus')?.parentElement;
                    if (invoiceContainer && !invoiceContainer.querySelector('.no-data')) {
                        invoiceContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No invoice status data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($expenseTrend) && !empty($expenseTrend))
                createSparklineChart('expensesTrend', @json($expenseTrend), '#ef4444');
            @else
                globalLoader.hide();
            @endif
            @if (isset($incomeTrend) && !empty($incomeTrend))
                createSparklineChart('incomeTrend', @json($incomeTrend), '#10b981');
            @else
                globalLoader.hide();
            @endif
            @if (isset($profitTrend) && !empty($profitTrend))
                createSparklineChart('profitTrend', @json($profitTrend), @json(($profit ?? 0) >= 0 ? '#10b981' : '#ef4444'));
            @else
                globalLoader.hide();
            @endif

            // Close Alerts Section if Empty
            const alertsSection = document.getElementById('alerts-section');
            const closeAlertsBtn = document.getElementById('close-alerts');
            if (alertsSection && closeAlertsBtn) {
                closeAlertsBtn.addEventListener('click', () => {
                    alertsSection.style.display = 'none';
                });
            }
        });
    </script>
@endpush