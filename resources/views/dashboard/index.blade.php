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

        <!-- Quick Actions -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Admin Quick Actions -->
                @role('admin')
                    @can('create_birds')
                        <a href="{{ route('birds.create') }}" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200">Add Bird</a>
                    @endcan
                    @can('create_eggs')
                        <a href="{{ route('eggs.create') }}" class="bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200">Record Egg Production</a>
                    @endcan
                    @can('create_sales')
                        <a href="{{ route('sales.create') }}" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200">Add Sale</a>
                    @endcan
                    @can('create_expenses')
                        <a href="{{ route('expenses.create') }}" class="bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200">Log Expense</a>
                    @endcan
                    @can('create_income')
                        <a href="{{ route('income.create') }}" class="bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200">Log Income</a>
                    @endcan
                    @can('create_users')
                        <a href="{{ route('users.create') }}" class="bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-center transition duration-200">Add User</a>
                    @endcan
                    @can('create_employees')
                        <a href="{{ route('employees.create') }}" class="bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600 text-center transition duration-200">Add Employee</a>
                    @endcan
                @endrole

                <!-- Farm Manager Quick Actions -->
                @role('farm_manager')
                    @can('create_birds')
                        <a href="{{ route('birds.create') }}" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200">Add Bird</a>
                    @endcan
                    @can('create_eggs')
                        <a href="{{ route('eggs.create') }}" class="bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200">Record Egg Production</a>
                    @endcan
                    @can('create_mortalities')
                        <a href="{{ route('mortalities.create') }}" class="bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200">Log Mortality</a>
                    @endcan
                    @can('create_feed')
                        <a href="{{ route('feed.create') }}" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200">Log Feed</a>
                    @endcan
                    @can('create_inventory')
                        <a href="{{ route('inventory.create') }}" class="bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200">Add Inventory</a>
                    @endcan
                @endrole

                <!-- Accountant Quick Actions -->
                @role('accountant')
                    @can('create_expenses')
                        <a href="{{ route('expenses.create') }}" class="bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200">Log Expense</a>
                    @endcan
                    @can('create_income')
                        <a href="{{ route('income.create') }}" class="bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200">Log Income</a>
                    @endcan
                    @can('create_payroll')
                        <a href="{{ route('payroll.create') }}" class="bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600 text-center transition duration-200">Log Payroll</a>
                    @endcan
                @endrole

                <!-- Sales Manager Quick Actions -->
                @role('sales_manager')
                    @can('create_sales')
                        <a href="{{ route('sales.create') }}" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200">Add Sale</a>
                    @endcan
                    @can('create_customers')
                        <a href="{{ route('customers.create') }}" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200">Add Customer</a>
                    @endcan
                    @can('create_orders')
                        <a href="{{ route('orders.create') }}" class="bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200">Create Order</a>
                    @endcan
                    @can('generate_invoices')
                        <a href="{{ route('invoices.create') }}" class="bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200">Generate Invoice</a>
                    @endcan
                @endrole

                <!-- Inventory Manager Quick Actions -->
                @role('inventory_manager')
                    @can('create_inventory')
                        <a href="{{ route('inventory.create') }}" class="bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200">Add Inventory</a>
                    @endcan
                    @can('create_suppliers')
                        <a href="{{ route('suppliers.create') }}" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200">Add Supplier</a>
                    @endcan
                    @can('create_feed')
                        <a href="{{ route('feed.create') }}" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200">Log Feed</a>
                    @endcan
                    @can('create_medicine_logs')
                        <a href="{{ route('medicine-logs.create') }}" class="bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200">Log Medicine</a>
                    @endcan
                @endrole

                <!-- Veterinarian Quick Actions -->
                @role('veterinarian')
                    @can('create_health_checks')
                        <a href="{{ route('health-checks.create') }}" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200">Log Health Check</a>
                    @endcan
                    @can('create_diseases')
                        <a href="{{ route('diseases.create') }}" class="bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200">Log Disease</a>
                    @endcan
                    @can('create_vaccination_logs')
                        <a href="{{ route('vaccination-logs.create') }}" class="bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200">Log Vaccination</a>
                    @endcan
                    @can('create_medicine_logs')
                        <a href="{{ route('medicine-logs.create') }}" class="bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200">Log Medicine</a>
                    @endcan
                @endrole

                <!-- Labourer Quick Actions -->
                @role('labourer')
                    @can('create_eggs')
                        <a href="{{ route('eggs.create') }}" class="bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200">Record Egg Production</a>
                    @endcan
                    @can('create_mortalities')
                        <a href="{{ route('mortalities.create') }}" class="bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200">Log Mortality</a>
                    @endcan
                @endrole
            </div>
        </section>

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



        <!-- Key Performance Indicators (KPIs) -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Performance Indicators (KPIs)</h2>
            @php
                $groupedKpis = [
                    'Flock Statistics' => [
                        ['label' => 'Chicks', 'value' => $chicks ?? 0, 'icon' => '🐤'],
                        ['label' => 'Layers', 'value' => $layerBirds ?? 0, 'icon' => '🐓'],
                        ['label' => 'Broilers', 'value' => $broilerBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'Total Birds', 'value' => $totalBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'Mortality %', 'value' => number_format($mortalityRate ?? 0, 2), 'icon' => '⚰️'],
                    ],
                    'Production' => [
                        ['label' => 'Egg Crates', 'value' => $metrics['egg_crates'] ?? 0, 'icon' => '🥚'],
                        ['label' => 'Feed (kg)', 'value' => $metrics['feed_kg'] ?? 0, 'icon' => '🌾'],
                        ['label' => 'FCR', 'value' => number_format($fcr ?? 0, 2), 'icon' => '⚖️'],
                    ],
                    'Operations' => [
                        ['label' => 'Employees', 'value' => $employees ?? 0, 'icon' => '👨‍🌾'],
                        ['label' => 'Payroll', 'value' => number_format($totalPayroll ?? 0, 2), 'icon' => '💵'],
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

                <!-- Pending Approvals (Admins or Finance Managers) -->
       @can('manage_finances')
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pending Approvals
            </h2>

            @if (isset($pendingApprovals) && $pendingApprovals->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($pendingApprovals->take(3) as $approval)
                        <div class="container-box p-4  rounded-xl flex justify-between items-center">
                            <div>
                                <p class="text-xl text-gray-500 dark:text-gray-400 text-base">{{ $approval->date }}</p>
                                <p class="font-semibold text-gray-800 dark:text-white text-base">
                                    ${{ number_format($approval->amount, 2) }} 
                                    <span class="text-xl ml-2 px-2 py-1 rounded-xl 
                                        {{ $approval->type === 'expense' ? 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' : 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' }}">
                                        {{ ucfirst($approval->type) }}
                                    </span>
                                </p>
                                <p class="text-lg text-gray-600 dark:text-gray-300 text-base">{{ Str::limit($approval->description, 40) }}</p>
                            </div>
                            <div class="flex space-x-3 text-base">
                                <a href="{{ route('transactions.approve', $approval->id) }}" 
                                class="text-green-600 hover:underline text-xl font-medium">Approve</a>
                                <a href="{{ route('transactions.reject', $approval->id) }}" 
                                class="text-red-600 hover:underline text-xl font-medium">Reject</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($pendingApprovals->count() > 3)
                    <div class="mt-4 text-right">
                        <a href="{{ route('transactions.index') }}" 
                        class="text-lg text-blue-600 dark:text-blue-400 hover:underline">
                        View All Pending Approvals →
                        </a>
                    </div>
                @endif
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-6">No pending approvals.</p>
            @endif
        </section>
        @endcan


        <!-- Payroll Status (Admin/Accountant) -->
        <section class="mb-8">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
        </svg>
        Payroll Status
    </h2>

    <div class="container-box grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @if (isset($payrollStatus) && $payrollStatus->isNotEmpty())
            @php
                $latest = $payrollStatus->first();
            @endphp
            <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow">
                <p class="text-xl text-gray-500 dark:text-gray-400">Latest Pay Date</p>
                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $latest->date }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow">
                <p class="text-xl text-gray-500 dark:text-gray-400">Employees</p>
                <p class="text-lg font-bold">{{ $latest->employees }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow">
                <p class="text-xl text-gray-500 dark:text-gray-400">Total Paid</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                    ${{ number_format($latest->total, 2) }}
                </p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow">
                <p class="text-lg text-gray-500 dark:text-gray-400 mb-1">Status</p>
                <span class="px-2 py-1 mt-4 text-xs font-medium rounded-full
                    {{ $latest->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' }}">
                    {{ ucfirst($latest->status) }}
                </span>
            </div>
        @else
            <div class="col-span-4 text-center py-6 text-gray-500 dark:text-gray-400">
                No payroll activity yet.
            </div>
        @endif
    </div>

    <div class="mt-4 text-right">
        <a href="{{ route('payroll.index') }}" 
           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
           View Full Payroll History →
        </a>
    </div>
    </section>


        

   <!-- Dashboard Charts -->
  <section class="mb-8">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Dashboard Charts</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Egg Trend -->
        <div class="chart-container">
            <div class="flex items-center justify-between mb-2">
                <h4 class="chart-title">Egg Trend</h4>
                <select id="eggChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="line">Line</option>
                    <option value="bar">Bar</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="eggTrend" class="w-full h-full"></canvas>
            </div>
            @if (!isset($eggTrend) || $eggTrend->isEmpty())
                <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No egg data available.</p>
            @endif
        </div>

        <!-- Feed Trend -->
        <div class="chart-container">
            <div class="flex items-center justify-between mb-2">
                <h4 class="chart-title">Feed Trend</h4>
                <select id="feedChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="line">Line</option>
                    <option value="bar">Bar</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="feedTrend" class="w-full h-full"></canvas>
            </div>
            @if (!isset($feedTrend) || $feedTrend->isEmpty())
                <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No feed data available.</p>
            @endif
        </div>

        <!-- Sales Trend (Admin Only) -->
        @role('admin')
        <div class="chart-container">
            <div class="flex items-center justify-between mb-2">
                <h4 class="chart-title">Sales Trend</h4>
                <select id="salesChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="line">Line</option>
                    <option value="bar">Bar</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="salesTrend" class="w-full h-full"></canvas>
            </div>
            @if (!isset($salesTrend) || $salesTrend->isEmpty())
                <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No sales data available.</p>
            @endif
        </div>
        @endrole

        <!-- Income Trend -->
        <div class="chart-container">
            <div class="flex items-center justify-between mb-2">
                <h4 class="chart-title">Income Trend</h4>
                <select id="incomeChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="line">Line</option>
                    <option value="bar">Bar</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="incomeChart" class="w-full h-full"></canvas>
            </div>
            @if (!isset($incomeTrend) || $incomeTrend->isEmpty())
                <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No income data available.</p>
            @endif
        </div>

        <!-- Invoice Status (Admin Only) -->
        @role('admin')
        <div class="chart-container col-span-1 lg:col-span-2">
            <div class="flex items-center justify-between mb-2">
                <h4 class="chart-title">Invoice Status Distribution</h4>
                <select id="invoiceChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="bar">Bar</option>
                    <option value="line">Line</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="invoiceStatus" class="w-full h-full"></canvas>
            </div>
            @if (!isset($invoiceStatuses) || array_sum($invoiceStatuses) == 0)
                <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No invoice status data available.</p>
            @endif
        </div>
        @endrole

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
                            <button class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all pending transactions">
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
                            <button class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all transactions">
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
                        <a href="{{ route('orders.create') }}" class="bg-purple-600 text-white py-1 px-3 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="Create a new order">
                            Create Order
                        </a>
                    </div>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $pendingOrders ?? 0 }}</p>
                    <div class="mt-4">
                        <div class="flex mb-2 items-center justify-between">
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Completion</span>
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $completionPercentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700" aria-hidden="true">
                            <div id="completionProgressBar" class="bg-purple-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $completionPercentage ?? 0 }}%"></div>
                        </div>
                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex items-center gap-2">
                            <span id="completionPercentageValue">{{ $completionPercentage ?? 0 }}%</span>
                            <span class="text-[10px]">Completion</span>
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
                        <a href="{{ route('orders.index') }}" class="bg-purple-600 text-white py-1 px-3 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all orders">
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
                        <a href="{{ route('payroll.index') }}" class="bg-teal-600 text-white py-1 px-3 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all payroll records">
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
                        <a href="{{ route('payroll.index') }}" class="bg-teal-600 text-white py-1 px-3 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all pending payrolls">
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
                    <button class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-green-500 focus:border-green-500 transition duration-200" data-tab="egg" aria-label="Show egg sales">
                        Egg Sales
                    </button>
                    <button class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-green-500 focus:border-green-500 transition duration-200" data-tab="bird" aria-label="Show bird sales">
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
                        <select class="border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" id="mortalityFilter" aria-label="Filter mortalities by cause">
                            <option value="all">All Causes</option>
                            <option value="disease">Disease</option>
                            <option value="injury">Injury</option>
                        </select>
                    </div>
                    <a href="{{ route('mortalities.create') }}" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 focus:ring-2 focus:ring-red-500 transition duration-200 transform hover:scale-105" aria-label="Log a new mortality">
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



<script>
/* ---------- Robust Dashboard Chart Init ---------- */

/* Normalize and handle many server-side shapes (Collections, arrays, assoc maps) */
function normalizeSeries(raw) {
  try {
    if (!raw) return { labels: [], values: [] };
    // If JSON string
    if (typeof raw === 'string') {
      try { raw = JSON.parse(raw); } catch (e) { return { labels: [], values: [] }; }
    }
    // If object (assoc map like { "2025-07": 100, ... })
    if (!Array.isArray(raw) && typeof raw === 'object') {
      const keys = Object.keys(raw);
      if (!keys.length) return { labels: [], values: [] };
      return { labels: keys, values: keys.map(k => Number(raw[k] ?? 0)) };
    }
    // If array:
    if (Array.isArray(raw)) {
      if (!raw.length) return { labels: [], values: [] };
      const first = raw[0];
      // array of objects [{date:..., value:...}, ...]
      if (first && typeof first === 'object' && !Array.isArray(first)) {
        const labels = raw.map(r => r.date ?? r.label ?? r.name ?? '');
        const values = raw.map(r => Number(r.value ?? r.v ?? r.y ?? Object.values(r).find(v => typeof v === 'number' || !isNaN(Number(v))) ?? 0));
        return { labels, values };
      }
      // array of numbers or strings
      if (typeof first === 'number' || !isNaN(Number(first))) {
        const values = raw.map(v => Number(v ?? 0));
        const labels = values.map((_, i) => i + 1);
        return { labels, values };
      }
      // fallback: convert to labels
      return { labels: raw.map((r,i) => String(r)), values: raw.map(() => 0) };
    }
    return { labels: [], values: [] };
  } catch (err) {
    console.error('normalizeSeries error', err, raw);
    return { labels: [], values: [] };
  }
}

/* ---------- Embed server-provided variables (fallbacks included) ---------- */
const RAW = {
  eggProduction: @json($eggTrend ?? $eggProduction ?? $eggProduction ?? []),
  feedConsumption: @json($feedTrend ?? $feedConsumption ?? []),
  salesData: @json($salesTrend ?? $salesData ?? []),
  incomeData: @json($incomeTrend ?? $incomeData ?? []),
  invoiceStatuses: @json($invoiceStatuses ?? $invoiceStatusesAssoc ?? $invoiceStatusesAssoc ?? []),
  monthlyIncome: @json($monthlyIncome ?? []),
  pendingTransactionsTrend: @json($pendingTransactionsTrend ?? []),
  totalTransactionAmountTrend: @json($totalTransactionAmountTrend ?? []),
  salesComparison: @json($salesComparison ?? []),
  mortalityTrend: @json($mortalityTrend ?? []),
  expenseData: @json($expenseData ?? []),
  profitTrend: @json($profitTrend ?? []),
  profitValue: Number(@json($profit ?? 0)),
  // single-value fallbacks shown in template
  eggSales: Number(@json($eggSales ?? 0)),
  birdSales: Number(@json($birdSales ?? 0)),
  pendingTransactions: Number(@json($pendingTransactions ?? 0)),
  totalTransactionAmount: Number(@json($totalTransactionAmount ?? 0)),
  totalOrderAmount: Number(@json($totalOrderAmount ?? 0))
};

console.log('RAW chart payload:', RAW);

/* Color tokens */
const COLORS = {
  green: '#10B981',
  blue: '#3B82F6',
  red: '#EF4444',
  yellow: '#F59E0B',
  purple: '#7C3AED',
  grayText: '#374151'
};

/* Safe chart creation helper */
function safeCreateChart(canvasId, type, labels, data, datasetOptions = {}, extraOptions = {}) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) {
    console.warn(`Canvas #${canvasId} not found - skipped.`);
    return null;
  }
  const ctx = canvas.getContext('2d');
  return new Chart(ctx, {
    type: type,
    data: {
      labels: labels,
      datasets: [{
        label: datasetOptions.label || '',
        data: data,
        borderColor: datasetOptions.borderColor || COLORS.green,
        backgroundColor: datasetOptions.backgroundColor || 'rgba(16,185,129,0.12)',
        fill: datasetOptions.fill ?? false,
        tension: datasetOptions.tension ?? 0.2,
        ...datasetOptions
      }]
    },
    options: Object.assign({
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: datasetOptions.hideLegend ? false : true, labels: { color: COLORS.grayText } },
        tooltip: { enabled: datasetOptions.hideTooltip ? false : true }
      },
      scales: {
        x: { ticks: { color: COLORS.grayText }, grid: { color: 'rgba(0,0,0,0.04)' } },
        y: { ticks: { color: COLORS.grayText }, grid: { color: 'rgba(0,0,0,0.04)' } }
      }
    }, extraOptions)
  });
}

/* ---------- DOM ready: create charts and wire controls ---------- */
document.addEventListener('DOMContentLoaded', function () {
  // normalize inputs
  const egg = normalizeSeries(RAW.eggProduction);
  const feed = normalizeSeries(RAW.feedConsumption);
  const sales = normalizeSeries(RAW.salesData);
  const income = normalizeSeries(RAW.incomeData);
  // invoice (assoc or array)
  let invoiceLabels = [], invoiceValues = [];
  if (Array.isArray(RAW.invoiceStatuses) && RAW.invoiceStatuses.length) {
    // array of objects or numbers
    const tmp = normalizeSeries(RAW.invoiceStatuses);
    invoiceLabels = tmp.labels; invoiceValues = tmp.values;
  } else if (RAW.invoiceStatuses && typeof RAW.invoiceStatuses === 'object') {
    invoiceLabels = Object.keys(RAW.invoiceStatuses);
    invoiceValues = Object.values(RAW.invoiceStatuses).map(v => Number(v || 0));
  }

  const monthly = (function(){
    if (!RAW.monthlyIncome) return { labels: [], values: [] };
    if (Array.isArray(RAW.monthlyIncome)) return normalizeSeries(RAW.monthlyIncome);
    if (typeof RAW.monthlyIncome === 'object') return { labels: Object.keys(RAW.monthlyIncome), values: Object.values(RAW.monthlyIncome).map(v => Number(v || 0)) };
    return normalizeSeries(RAW.monthlyIncome);
  })();

  const pendingTx = normalizeSeries(RAW.pendingTransactionsTrend);
  const totalTx = normalizeSeries(RAW.totalTransactionAmountTrend);
  const salesCompRaw = RAW.salesComparison || [];
  let salesCompLabels = [], salesCompEgg = [], salesCompBird = [];
  if (Array.isArray(salesCompRaw) && salesCompRaw.length) {
    salesCompLabels = salesCompRaw.map(r => r.date ?? '');
    salesCompEgg = salesCompRaw.map(r => Number(r.egg_sales ?? r.eggSales ?? r.eggs ?? 0));
    salesCompBird = salesCompRaw.map(r => Number(r.bird_sales ?? r.birdSales ?? r.birds ?? 0));
  } else {
    const sc = normalizeSeries(RAW.salesComparison);
    salesCompLabels = sc.labels; salesCompEgg = sc.values; salesCompBird = [];
  }
  const mortality = normalizeSeries(RAW.mortalityTrend);
  const expenseMini = normalizeSeries(RAW.expenseData);
  const profitMini = normalizeSeries(RAW.profitTrend);

  // create charts (only if their canvas exists)
  window.eggChart = safeCreateChart('eggTrend', 'line', egg.labels, egg.values, { label: 'Egg Crates', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });
  window.feedChart = safeCreateChart('feedTrend', 'line', feed.labels, feed.values, { label: 'Feed (kg)', borderColor: COLORS.blue, backgroundColor: 'rgba(59,130,246,0.12)' });
  window.salesChart = safeCreateChart('salesTrend', 'line', sales.labels, sales.values, { label: 'Sales', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });
  window.incomeChart = safeCreateChart('incomeChart', 'line', income.labels, income.values, { label: 'Income', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });

  if (document.getElementById('invoiceStatus')) {
    window.invoiceChart = new Chart(document.getElementById('invoiceStatus').getContext('2d'), {
      type: 'bar',
      data: { labels: invoiceLabels, datasets: [{ label: 'Invoices', data: invoiceValues, backgroundColor: [COLORS.yellow, COLORS.green, COLORS.purple, COLORS.red] }] },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
  }

  window.monthlyIncomeChart = safeCreateChart('monthlyIncomeChart', 'line', monthly.labels, monthly.values, { label: 'Monthly Income', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });

  window.pendingTxChart = safeCreateChart('pendingTransactionsTrend', 'line', pendingTx.labels, pendingTx.values, { label: 'Pending Tx', borderColor: COLORS.yellow, backgroundColor: 'rgba(245,158,11,0.12)', hideLegend: true, hideTooltip: true }, { scales: { x: { display: false }, y: { display: false } } });
  window.totalTxChart = safeCreateChart('totalTransactionAmountTrend', 'line', totalTx.labels, totalTx.values, { label: 'Total Tx', borderColor: COLORS.blue, backgroundColor: 'rgba(59,130,246,0.12)', hideLegend: true, hideTooltip: true }, { scales: { x: { display: false }, y: { display: false } } });

  if (document.getElementById('salesComparison')) {
    window.salesComparisonChart = new Chart(document.getElementById('salesComparison').getContext('2d'), {
      type: 'line',
      data: { labels: salesCompLabels, datasets: [
        { label: 'Egg Sales', data: salesCompEgg, borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)', fill: false },
        { label: 'Bird Sales', data: salesCompBird, borderColor: COLORS.blue, backgroundColor: 'rgba(59,130,246,0.12)', fill: false }
      ]},
      options: { responsive: true, maintainAspectRatio: false }
    });
  }

  window.mortalityChart = safeCreateChart('mortalityTrend', 'line', mortality.labels, mortality.values, { label: 'Mortality', borderColor: COLORS.red, backgroundColor: 'rgba(239,68,68,0.12)' });

  window.expensesMini = safeCreateChart('expensesTrend', 'line', expenseMini.labels, expenseMini.values, { hideLegend: true, hideTooltip: true, borderColor: COLORS.red, backgroundColor: 'rgba(239,68,68,0.12)' }, { scales: { x: { display: false }, y: { display: false } } });
  window.incomeMini = safeCreateChart('incomeTrend', 'line', income.labels, income.values, { hideLegend: true, hideTooltip: true, borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' }, { scales: { x: { display: false }, y: { display: false } } });
  window.profitMini = safeCreateChart('profitMiniChart', 'line', profitMini.labels, profitMini.values, { hideLegend: true, hideTooltip: true, borderColor: (RAW.profitValue >= 0 ? COLORS.green : COLORS.red), backgroundColor: (RAW.profitValue >= 0 ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)') }, { scales: { x: { display: false }, y: { display: false } } });

  /* Map for toggles (chart type selects) */
  const chartMap = {
    eggChartType: window.eggChart,
    feedChartType: window.feedChart,
    salesChartType: window.salesChart,
    incomeChartType: window.incomeChart,
    invoiceChartType: window.invoiceChart,
    monthlyIncomeChartType: window.monthlyIncomeChart,
    salesComparisonChartType: window.salesComparisonChart,
    mortalityTrendChartType: window.mortalityChart,
    // Note: pending/total transaction mini-charts don't have selects in this template
  };

  /* Wire selects to change chart type */
  Object.keys(chartMap).forEach(selectId => {
    const sel = document.getElementById(selectId);
    if (!sel) return;
    sel.addEventListener('change', (e) => {
      const chart = chartMap[selectId];
      if (!chart) return;
      chart.config.type = e.target.value;
      chart.update();
    });
  });

  /* Tabs for recent sales */
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      // deactivate all buttons
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('border-b-2','border-green-500'));
      // hide all tab-content sections (IDs used in your template)
      document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
      const tab = this.dataset.tab;
      // highlight
      this.classList.add('border-b-2','border-green-500');
      // show content matching id mapping
      if (tab === 'egg') { document.getElementById('egg-sales')?.classList.remove('hidden'); }
      if (tab === 'bird') { document.getElementById('bird-sales')?.classList.remove('hidden'); }
    });
  });

  /* Dark mode observer - update tick/legend color */
  function applyDarkToCharts(isDark) {
    const textColor = isDark ? '#D1D5DB' : '#374151';
    const grid = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
    [window.eggChart, window.feedChart, window.salesChart, window.incomeChart, window.invoiceChart, window.monthlyIncomeChart, window.salesComparisonChart, window.mortalityChart, window.expensesMini, window.incomeMini, window.profitMini, window.pendingTxChart, window.totalTxChart].forEach(c => {
      if (!c) return;
      if (!c.options.scales) c.options.scales = {};
      c.options.scales.x = c.options.scales.x || {};
      c.options.scales.y = c.options.scales.y || {};
      c.options.scales.x.ticks = c.options.scales.x.ticks || {};
      c.options.scales.y.ticks = c.options.scales.y.ticks || {};
      c.options.scales.x.ticks.color = textColor;
      c.options.scales.y.ticks.color = textColor;
      c.options.scales.x.grid = c.options.scales.x.grid || {};
      c.options.scales.y.grid = c.options.scales.y.grid || {};
      c.options.scales.x.grid.color = grid;
      c.options.scales.y.grid.color = grid;
      if (c.options.plugins && c.options.plugins.legend && c.options.plugins.legend.labels) {
        c.options.plugins.legend.labels.color = textColor;
      }
      c.update();
    });
  }
  applyDarkToCharts(document.documentElement.classList.contains('dark') || document.body.classList.contains('dark'));
  const observer = new MutationObserver(() => applyDarkToCharts(document.documentElement.classList.contains('dark') || document.body.classList.contains('dark')));
  observer.observe(document.documentElement, { attributes: true });

}); // DOMContentLoaded
</script>
