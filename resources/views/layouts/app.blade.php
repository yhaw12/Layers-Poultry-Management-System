<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}"> <!-- Added for reliable base URL -->
    <title>Poultry Tracker</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-[#0a0a23] dark:text-white font-sans">
    <!-- Centralized Loader Overlay -->
    <div id="global-loader" class="loader-overlay hidden" onclick="globalLoader && globalLoader.hide()">
        <div class="flex flex-col items-center">
            <div class="loader-spinner"></div>
            <p id="loader-message" class="loader-message hidden"></p>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification-container" class="notification-container"></div>

    @auth
        <div class="flex h-screen relative">
            <!-- Sidebar Overlay for Mobile -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

            <!-- Sidebar -->
            <aside id="sidebar" class="sidebar bg-white dark:bg-gray-900 shadow-lg h-screen fixed top-0 left-0 w-3/4 max-w-[280px] md:w-64 transform -translate-x-full md:translate-x-0 md:static z-50 transition-transform duration-300 ease-in-out hidden md:block">
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-900 z-10">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Poultry Tracker</h2>
                    <button class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full" id="sidebar-toggle" aria-label="Close sidebar" aria-controls="sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="p-4 space-y-1 text-sm font-medium text-gray-600 dark:text-gray-300 overflow-y-auto h-[calc(100vh-4rem)]">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 {{ Route::is('dashboard') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
                       aria-current="{{ Route::is('dashboard') ? 'page' : 'false' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3M9 21v-6a1 1 0 011-1h4a1 1 0 011 1v6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}"
                       class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 {{ Route::is('notifications.index') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
                       aria-current="{{ Route::is('notifications.index') ? 'page' : 'false' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1m5 0H7" />
                        </svg>
                        Notifications
                    </a>

                    <!-- Farm Management -->
                    <div>
                        <button data-target="farm-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-expanded="false" aria-controls="farm-submenu">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                Farm Management
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="farm-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                            <a href="{{ route('birds.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('birds.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Birds
                            </a>
                            <a href="{{ route('eggs.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('eggs.index') || Route::is('eggs.sales') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Eggs
                            </a>
                            <a href="{{ route('mortalities.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('mortalities.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Mortalities
                            </a>
                            <a href="{{ route('vaccination-logs.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('vaccination-logs.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Vaccinations
                            </a>
                        </div>
                    </div>

                    <!-- Resources -->
                    <div>
                        <button data-target="resources-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-expanded="false" aria-controls="resources-submenu">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v6l-8 4-8-4V7m16 0l-8 4-8-4" />
                                </svg>
                                Resources
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="resources-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                            <a href="{{ route('feed.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('feed.index') || Route::is('feed.consumption') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Feed
                            </a>
                            <a href="{{ route('medicine-logs.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('medicine-logs.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Medicine Logs
                            </a>
                            <a href="{{ route('inventory.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('inventory.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Inventory
                            </a>
                            <a href="{{ route('suppliers.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('suppliers.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Suppliers
                            </a>
                        </div>
                    </div>

                    <!-- Sales & Customers (Admin Only) -->
                    @role('admin')
                        <div>
                            <button data-target="sales-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-expanded="false" aria-controls="sales-submenu">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Sales & Customers
                                </div>
                                <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="sales-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                                <a href="{{ route('sales.index') }}"
                                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('sales.index') || Route::is('sales.birds') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                    Sales
                                </a>
                                <a href="{{ route('customers.index') }}"
                                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('customers.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                    Customers
                                </a>
                                <a href="{{ route('orders.index') }}"
                                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('orders.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                    Orders
                                </a>
                            </div>
                        </div>
                    @endrole

                    <!-- Finances (Permission-Based) -->
                    <div class="mb-4">
                        <button data-target="finances-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-expanded="false" aria-controls="finances-submenu">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Finances
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="finances-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                            <a href="{{ route('expenses.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('expenses.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Expenses
                            </a>
                            @role('admin')
                                <a href="{{ route('income.index') }}"
                                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('income.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                    Income
                                </a>
                            @endrole
                            <a href="{{ route('payroll.index') }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('payroll.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Payroll
                            </a>
                        </div>
                    </div>

                    <!-- Employees (Admin Only) -->
                    @role('admin')
                        <a href="{{ route('employees.index') }}"
                           class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 {{ Route::is('employees.*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
                           aria-current="{{ Route::is('employees.*') ? 'page' : 'false' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Employees
                        </a>
                    @endrole

                    <!-- Reports -->
                    <div>
                        <button data-target="reports-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-expanded="false" aria-controls="reports-submenu">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Reports
                            </div>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="reports-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                            <a href="{{ route('reports.index', ['type' => 'custom']) }}"
                               class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ request()->query('type') === 'custom' ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                Analytics
                            </a>
                            @can('manage finances')
                                <a href="{{ route('reports.index', ['type' => 'profitability']) }}"
                                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ request()->query('type') === 'profitability' ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                    Profitability
                                </a>
                            @endcan
                            @role('admin')
                                <a href="{{ route('activity-logs.index') }}"
                                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('activity-logs.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                                    Activity Logs
                                </a>
                            @endrole
                        </div>
                    </div>

                    <!-- Users (Admin Only) -->
                    @role('admin')
                        <a href="{{ route('users.index') }}"
                           class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 {{ Route::is('users.*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
                           aria-current="{{ Route::is('users.*') ? 'page' : 'false' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users
                        </a>
                    @endrole

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="absolute bottom-4 w-full">
                        @csrf
                        <button type="submit"
                                class="flex items-center w-full px-4 py-3 rounded-lg text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </nav>
            </aside>

            <!-- Main Area -->
            <div class="flex-1 flex flex-col h-screen">
                <!-- Header -->
                <header class="bg-white shadow-md dark:border-gray-700 dark:bg-[#0a0a23] dark:text-white dark:border-b-2 dark:border-gray sticky top-0 z-30 transition-shadow duration-200">
                    <nav class="px-4 py-3">
                        <div class="container mx-auto flex items-center justify-between gap-4 relative">
                            <!-- Left Section: Mobile Menu and Logo -->
                            <div class="flex items-center gap-4">
                                <!-- Mobile Menu Button -->
                                <button id="mobile-menu-button" class="md:hidden p-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full" aria-label="Open sidebar" aria-controls="sidebar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <!-- Logo -->
                                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2 hover:text-blue-800 dark:hover:text-blue-300 transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Poultry Tracker
                                </a>
                            </div>

                            <!-- Right Section: Search, Navigation, Notifications, Dark Mode, User -->
                            <div class="flex items-center gap-4">
                                <!-- Search Icon and Container -->
                                <div class="relative flex items-center">
                                    <button id="search-toggle" class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Toggle search" aria-expanded="false" aria-controls="search-container">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                    <div id="search-container" class="hidden absolute right-[40px] top-0 w-80 bg-white dark:bg-gray-900 shadow-lg rounded-lg border dark:border-gray-700 z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
                                        <input type="text" id="search-input" placeholder="Search records..." class="w-full p-3 border-b dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-t-lg" aria-label="Search records">
                                        <div id="search-results" class="max-h-64 overflow-y-auto"></div>
                                    </div>
                                </div>

                                <!-- Navigation Links -->
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('income.index') }}" class="hidden md:block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold transition duration-200">Income</a>
                                    <a href="{{ route('reports.index') }}" class="hidden md:block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold transition duration-200">Reports</a>
                                @endif

                                <!-- Notification Bell -->
                                <div class="relative">
                                    <button id="notification-bell" class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="View notifications" aria-controls="notification-dropdown" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1m5 0H7" />
                                        </svg>
                                        <span id="notification-count" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center hidden">0</span>
                                    </button>
                                    <!-- Notification Dropdown -->
                                    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-900 shadow-lg rounded-lg border dark:border-gray-700 z-50">
                                        <div class="p-4 border-b dark:border-gray-700">
                                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifications</h3>
                                        </div>
                                        <div id="notification-list" class="max-h-64 overflow-y-auto"></div>
                                        <div class="p-4 border-t dark:border-gray-700">
                                            <button id="dismiss-all-notifications" class="text-blue-600 dark:text-blue-400 hover:underline text-sm w-full text-left">Dismiss All</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dark Mode Toggle -->
                                <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Toggle dark mode">
                                    <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.293 13.293A8 8 0 116.707 2.707a6 6 0 1010.586 10.586z"/>
                                    </svg>
                                    <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.03a1 1 0 10-1.44 1.44l.7.7a1 1 0 101.44-1.44l-.7-.7zM18 9a1 1 0 110 2h-1a1 1 0 110-2h1zM14.22 15.97a1 1 0 00-1.44-1.44l-.7.7a1 1 0 101.44 1.44l.7-.7zM11 18a1 1 0 10-2 0v-1a1 1 0 102 0v1zM6.78 15.97l-.7-.7a1 1 0 10-1.44 1.44l.7.7a1 1 0 001.44-1.44zM4 9a1 1 0 100 2H3a1 1 0 100-2h1zM6.78 4.03l.7.7a1 1 0 101.44-1.44l-.7-.7a1 1 0 00-1.44 1.44zM10 5a5 5 0 100 10A5 5 0 0010 5z"/>
                                    </svg>
                                </button>

                                <!-- User Profile Dropdown -->
                                <div class="relative">
                                    <button id="user-menu-button" class="flex items-center gap-2 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="User menu" aria-controls="user-menu" aria-expanded="false">
                                        <img src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" alt="User Avatar" class="w-8 h-8 rounded-full object-cover">
                                        <span class="hidden md:block text-blue-800 dark:text-blue-400 font-semibold text-sm">
                                            {{ auth()->user()->name ?? 'Unknown User' }}
                                        </span>
                                        <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 shadow-lg rounded-lg border dark:border-gray-700 z-50">
                                        <div class="p-4 border-b dark:border-gray-700 flex items-center gap-3">
                                            <img src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" alt="User Avatar" class="w-10 h-10 rounded-full object-cover">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name ?? 'Unknown User' }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ auth()->user()->email ?? 'No email' }}</p>
                                            </div>
                                        </div>
                                        <div class="py-1">
                                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                                            <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>
                </header>

                <!-- Main Content Area -->
                <main class="flex-1 overflow-y-auto p-4">
                    @yield('content')
                </main>
            </div>
        </div>
    @endauth

    @guest
        <main class="min-h-screen flex items-center justify-center">
            @yield('content')
        </main>
    @endguest

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.min.js"></script>
    <script>
        // Centralized Loader Class
        class Loader {
            constructor() {
                this.loader = document.getElementById('global-loader');
                this.messageEl = document.getElementById('loader-message');
                this.timeoutId = null;
                this.defaultTimeout = 10000;
                this.isShowing = false;
            }

            show(message = 'Loading...', timeout = this.defaultTimeout) {
                if (this.loader && !this.isShowing) {
                    this.isShowing = true;
                    this.loader.classList.remove('hidden');
                    if (this.messageEl) {
                        if (message) {
                            this.messageEl.textContent = message;
                            this.messageEl.classList.remove('hidden');
                        } else {
                            this.messageEl.classList.add('hidden');
                        }
                    }
                    if (this.timeoutId) clearTimeout(this.timeoutId);
                    if (timeout) {
                        this.timeoutId = setTimeout(() => {
                            this.hide();
                            console.warn('Loader timeout: Page took too long to load.');
                            if (this.messageEl) {
                                this.messageEl.textContent = 'Loading timed out. Click to dismiss.';
                                this.messageEl.classList.remove('hidden');
                            }
                        }, timeout);
                    }
                }
            }

            hide() {
                if (this.loader && this.isShowing) {
                    this.isShowing = false;
                    this.loader.classList.add('hidden');
                    if (this.messageEl) {
                        this.messageEl.classList.add('hidden');
                        this.messageEl.textContent = '';
                    }
                    if (this.timeoutId) {
                        clearTimeout(this.timeoutId);
                        this.timeoutId = null;
                    }
                }
            }
        }

        // Notification Manager
        class NotificationManager {
            constructor() {
                this.container = document.getElementById('notification-container');
                this.dropdown = document.getElementById('notification-dropdown');
                this.notificationList = document.getElementById('notification-list');
                this.notificationCount = document.getElementById('notification-count');
                this.dismissAllButton = document.getElementById('dismiss-all-notifications');
                this.bellButton = document.getElementById('notification-bell');
                this.notifications = [];
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || window.location.origin;
                this.pollInterval = null;
                if (!this.csrfToken) console.error('CSRF token missing');
                if (!this.container || !this.dropdown || !this.notificationList || !this.notificationCount || !this.bellButton) {
                    console.error('Notification DOM elements missing');
                }
            }

            show(id, message, type = 'info', timeout = 5000, url = '#') {
                if (!this.container) return;
                if (this.notifications.some(n => n.id === id)) return;

                const notification = document.createElement('div');
                notification.className = `notification ${type} translate-x-full`;
                notification.dataset.id = id;
                notification.innerHTML = `
                    <span>${message}</span>
                    <button class="notification-close" aria-label="Dismiss notification">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                this.container.appendChild(notification);
                this.notifications.push({ id, element: notification, message, type, url });

                // Add click and keydown events to close button
                const closeButton = notification.querySelector('.notification-close');
                closeButton.addEventListener('click', () => this.dismiss(id));
                closeButton.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.dismiss(id);
                    }
                });

                this.updateNotificationDropdown();
                setTimeout(() => notification.classList.remove('translate-x-full'), 100);
                if (timeout && type !== 'critical') {
                    setTimeout(() => this.dismiss(id), timeout);
                }
            }

            dismiss(id) {
                const notification = this.notifications.find(n => n.id === id);
                if (notification) {
                    notification.element.classList.add('notification-exit');
                    setTimeout(() => {
                        notification.element.remove();
                        this.notifications = this.notifications.filter(n => n.id !== id);
                        this.updateNotificationDropdown();
                        this.updateNotificationCount();
                    }, 300);
                }
            }

            async fetchNotifications() {
    if (!this.csrfToken || !this.notificationList) {
        console.error('CSRF token or notification list missing');
        return;
    }
    try {
        globalLoader.show('Fetching notifications...');
        const response = await fetch(`${this.baseUrl}/alerts`, {
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'include'
        });
        console.log('Response status:', response.status, 'Content-Type:', response.headers.get('Content-Type'));
        const text = await response.text();
        console.log('Raw response:', text);
        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status} - ${response.statusText}`);
        }
        let notifications;
        try {
            notifications = JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error:', e.message, 'Raw text:', text);
            throw new Error('Invalid JSON response');
        }
        if (Array.isArray(notifications)) {
            notifications.forEach(({ id, message, type, url }) => {
                if (!this.notifications.some(n => n.id === id)) {
                    this.show(id, message, type, type === 'critical' ? 0 : 5000, url);
                }
            });
        } else {
            console.warn('Notifications response is not an array:', notifications);
        }
        this.updateNotificationDropdown();
        this.updateNotificationCount();
    } catch (error) {
        console.error('Failed to fetch notifications:', error.message);
        this.notificationList.innerHTML = '<p class="p-3 text-sm text-red-600 dark:text-red-400">Failed to load notifications. Please refresh.</p>';
    } finally {
        globalLoader.hide();
    }
}

            async markAsRead(id) {
                if (!this.csrfToken || !this.notificationList) return;
                try {
                    const response = await fetch(`${this.baseUrl}/alerts/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error(`HTTP error: ${response.status} - ${response.statusText}`);
                    const result = await response.json();
                    if (result.success) {
                        this.dismiss(id);
                        this.updateNotificationDropdown();
                        this.updateNotificationCount();
                    }
                } catch (error) {
                    console.error(`Failed to mark notification ${id} as read:`, error.message);
                    this.notificationList.innerHTML = '<p class="p-3 text-sm text-red-600 dark:text-red-400">Failed to mark as read. Please try again.</p>';
                }
            }

            async dismissAll() {
                if (!this.csrfToken || !this.notificationList) return;
                try {
                    const response = await fetch(`${this.baseUrl}/alerts/dismiss-all`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error(`HTTP error: ${response.status} - ${response.statusText}`);
                    const result = await response.json();
                    if (result.success) {
                        this.notifications.forEach(notification => {
                            notification.element.classList.add('notification-exit');
                            setTimeout(() => notification.element.remove(), 300);
                        });
                        this.notifications = [];
                        this.updateNotificationDropdown();
                        this.updateNotificationCount();
                    }
                } catch (error) {
                    console.error('Failed to dismiss all notifications:', error.message);
                    this.notificationList.innerHTML = '<p class="p-3 text-sm text-red-600 dark:text-red-400">Failed to dismiss notifications. Please try again.</p>';
                }
            }

            updateNotificationDropdown() {
                if (!this.notificationList) return;
                this.notificationList.innerHTML = this.notifications.length > 0
                    ? this.notifications.map(n => `
                        <div class="p-3 border-b dark:border-gray-700 ${n.type === 'critical' ? 'bg-red-50 dark:bg-red-900' : n.type === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900' : n.type === 'success' ? 'bg-green-50 dark:bg-green-900' : 'bg-blue-50 dark:bg-blue-900'}">
                            <p class="text-sm text-gray-800 dark:text-gray-200">${n.message}</p>
                            <a href="${n.url || '#'}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs mt-1">View</a>
                            <button class="text-blue-600 dark:text-blue-400 hover:underline text-xs mt-1 ml-2" onclick="notificationManager.markAsRead('${n.id}')">Mark as Read</button>
                        </div>
                    `).join('')
                    : '<p class="p-3 text-sm text-gray-600 dark:text-gray-400">No new notifications.</p>';
                this.updateNotificationCount();
            }

            updateNotificationCount() {
                if (!this.notificationCount) return;
                const count = this.notifications.length;
                this.notificationCount.textContent = count;
                this.notificationCount.classList.toggle('hidden', count === 0);
            }

            startPolling(interval = 30000) {
                if (this.pollInterval) clearInterval(this.pollInterval);
                this.pollInterval = setInterval(() => this.fetchNotifications(), interval);
            }

            stopPolling() {
                if (this.pollInterval) clearInterval(this.pollInterval);
            }
        }

        // Search Manager
        class SearchManager {
            constructor() {
                this.container = document.getElementById('search-container');
                this.input = document.getElementById('search-input');
                this.results = document.getElementById('search-results');
                this.toggleButton = document.getElementById('search-toggle');
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                this.debounceTimeout = null;
                this.debounceDelay = 300;
                if (!this.csrfToken) console.error('CSRF token missing');
                if (!this.container || !this.input || !this.results || !this.toggleButton) {
                    console.error('Search DOM elements missing');
                }
            }

            init() {
                if (!this.container || !this.input || !this.results || !this.toggleButton) return;
                this.toggleButton.addEventListener('click', () => {
                    const isHidden = this.container.classList.contains('hidden');
                    this.container.classList.toggle('hidden', !isHidden);
                    this.container.classList.toggle('translate-x-full', !isHidden);
                    if (isHidden) {
                        this.toggleButton.setAttribute('aria-expanded', 'true');
                        this.input.focus();
                    } else {
                        setTimeout(() => {
                            this.container.classList.add('hidden');
                            this.input.value = '';
                            this.results.innerHTML = '';
                        }, 300);
                        this.toggleButton.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!this.toggleButton.contains(e.target) && !this.container.contains(e.target)) {
                        if (!this.container.classList.contains('hidden')) {
                            this.container.classList.add('translate-x-full');
                            setTimeout(() => {
                                this.container.classList.add('hidden');
                                this.input.value = '';
                                this.results.innerHTML = '';
                            }, 300);
                            this.toggleButton.setAttribute('aria-expanded', 'false');
                        }
                    }
                });

                this.input.addEventListener('input', () => {
                    clearTimeout(this.debounceTimeout);
                    this.debounceTimeout = setTimeout(() => {
                        const query = this.input.value.trim();
                        if (query.length > 0) {
                            this.performSearch(query);
                        } else {
                            this.results.innerHTML = '<p class="p-3 text-sm text-gray-600 dark:text-gray-400">Enter a search term.</p>';
                        }
                    }, this.debounceDelay);
                });
            }

            async performSearch(query) {
                if (!this.csrfToken || !this.results) return;
                try {
                    globalLoader.show('Searching...');
                    const response = await fetch(`${this.baseUrl}/search?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                    const results = await response.json();
                    this.displayResults(results);
                } catch (error) {
                    console.error('Search failed:', error);
                    this.results.innerHTML = '<p class="p-3 text-sm text-red-600 dark:text-red-400">Search failed. Please try again.</p>';
                } finally {
                    globalLoader.hide();
                }
            }

            displayResults(results) {
                if (!this.results) return;
                this.results.innerHTML = results.length > 0
                    ? results.map(result => `
                        <a href="${result.url}" class="block p-3 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <p class="text-sm text-gray-800 dark:text-gray-200">${result.name}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">${result.type.charAt(0).toUpperCase() + result.type.slice(1)}</p>
                        </a>
                    `).join('')
                    : '<p class="p-3 text-sm text-gray-600 dark:text-gray-400">No results found.</p>';
            }
        }

        // Global Instances
        const globalLoader = new Loader();
        const notificationManager = new NotificationManager();
        const searchManager = new SearchManager();

        // Consolidated DOMContentLoaded Handler
        document.addEventListener('DOMContentLoaded', () => {
            globalLoader.show('Loading page...');

            // Initialize notifications and start polling
            if (notificationManager.container && notificationManager.notificationList) {
                notificationManager.fetchNotifications();
                notificationManager.startPolling();
            }

            // Initialize search
            searchManager.init();

            // Notification Bell Toggle
            const notificationBell = document.getElementById('notification-bell');
            const notificationDropdown = document.getElementById('notification-dropdown');
            if (notificationBell && notificationDropdown) {
                notificationBell.addEventListener('click', () => {
                    notificationDropdown.classList.toggle('hidden');
                    notificationBell.setAttribute('aria-expanded', !notificationDropdown.classList.contains('hidden'));
                });
                document.addEventListener('click', (e) => {
                    if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.classList.add('hidden');
                        notificationBell.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Dismiss All Notifications
            const dismissAllButton = document.getElementById('dismiss-all-notifications');
            if (dismissAllButton) {
                dismissAllButton.addEventListener('click', () => notificationManager.dismissAll());
            }

            // User Menu Toggle
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', () => {
                    userMenu.classList.toggle('hidden');
                    userMenuButton.setAttribute('aria-expanded', !userMenu.classList.contains('hidden'));
                });
                document.addEventListener('click', (e) => {
                    if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                        userMenuButton.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Handle form submissions
            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (form.tagName === 'FORM') {
                    globalLoader.show('Submitting form...');
                }
            });

            // Sidebar Toggle Script
            (function() {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const sidebarOverlay = document.getElementById('sidebar-overlay');
                let openSubmenuId = null;

                const initializeSidebar = () => {
                    if (window.innerWidth >= 768) {
                        sidebar.classList.remove('hidden', '-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        sidebarOverlay.classList.add('hidden');
                    } else {
                        sidebar.classList.add('hidden', '-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                        sidebarOverlay.classList.add('hidden');
                    }
                };

                initializeSidebar();

                window.addEventListener('resize', () => {
                    initializeSidebar();
                    if (window.innerWidth >= 768 && openSubmenuId) {
                        const prevSubmenu = document.getElementById(openSubmenuId);
                        prevSubmenu.classList.remove('open', 'opacity-100');
                        prevSubmenu.classList.add('hidden', 'opacity-0');
                        const prevToggle = document.querySelector(`[data-target="${openSubmenuId}"]`);
                        prevToggle.querySelector('svg').classList.remove('rotate-180');
                        prevToggle.setAttribute('aria-expanded', 'false');
                        openSubmenuId = null;
                    }
                });

                const toggleSubmenu = (toggle, submenuId) => {
                    const submenu = document.getElementById(submenuId);
                    const chevron = toggle.querySelector('svg');
                    const isOpen = submenu.classList.contains('open');

                    if (openSubmenuId && openSubmenuId !== submenuId) {
                        const prevSubmenu = document.getElementById(openSubmenuId);
                        prevSubmenu.classList.remove('open', 'opacity-100');
                        prevSubmenu.classList.add('hidden', 'opacity-0');
                        const prevToggle = document.querySelector(`[data-target="${openSubmenuId}"]`);
                        prevToggle.querySelector('svg').classList.remove('rotate-180');
                        prevToggle.setAttribute('aria-expanded', 'false');
                    }

                    if (isOpen) {
                        submenu.classList.remove('open', 'opacity-100');
                        submenu.classList.add('hidden', 'opacity-0');
                        chevron.classList.remove('rotate-180');
                        toggle.setAttribute('aria-expanded', 'false');
                        openSubmenuId = null;
                    } else {
                        submenu.classList.add('open');
                        submenu.classList.remove('hidden');
                        setTimeout(() => submenu.classList.add('opacity-100'), 10);
                        chevron.classList.add('rotate-180');
                        toggle.setAttribute('aria-expanded', 'true');
                        openSubmenuId = submenuId;
                    }
                };

                document.querySelectorAll('.toggle-btn').forEach(toggle => {
                    toggle.addEventListener('click', () => {
                        const submenuId = toggle.getAttribute('data-target');
                        toggleSubmenu(toggle, submenuId);
                    });
                    toggle.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            const submenuId = toggle.getAttribute('data-target');
                            toggleSubmenu(toggle, submenuId);
                        }
                    });
                });

                const toggleSidebar = () => {
                    const isOpen = !sidebar.classList.contains('hidden');
                    if (isOpen) {
                        sidebar.classList.add('-translate-x-full');
                        setTimeout(() => {
                            sidebar.classList.add('hidden');
                            sidebar.classList.remove('translate-x-0');
                        }, 300);
                        sidebarOverlay.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-label', 'Open sidebar');
                        sidebarToggle.setAttribute('aria-label', 'Open sidebar');
                        if (openSubmenuId) {
                            const prevSubmenu = document.getElementById(openSubmenuId);
                            prevSubmenu.classList.remove('open', 'opacity-100');
                            prevSubmenu.classList.add('hidden', 'opacity-0');
                            const prevToggle = document.querySelector(`[data-target="${openSubmenuId}"]`);
                            prevToggle.querySelector('svg').classList.remove('rotate-180');
                            prevToggle.setAttribute('aria-expanded', 'false');
                            openSubmenuId = null;
                        }
                        mobileMenuButton.focus();
                    } else {
                        sidebar.classList.remove('hidden');
                        sidebar.classList.add('translate-x-0');
                        sidebar.classList.remove('-translate-x-full');
                        sidebarOverlay.classList.remove('hidden');
                        mobileMenuButton.setAttribute('aria-label', 'Close sidebar');
                        sidebarToggle.setAttribute('aria-label', 'Close sidebar');
                        sidebar.querySelector('a, button').focus();
                    }
                };

                if (mobileMenuButton) mobileMenuButton.addEventListener('click', toggleSidebar);
                if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
                if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

                document.querySelectorAll('nav a, nav button[type="submit"]').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 768) {
                            toggleSidebar();
                        }
                    });
                });

                document.querySelectorAll('.submenu').forEach(submenu => {
                    submenu.classList.add('hidden', 'opacity-0');
                });
            })();

            // Dark Mode Script
            (function() {
                const themeToggle = document.getElementById('theme-toggle');
                const iconMoon = document.getElementById('icon-moon');
                const iconSun = document.getElementById('icon-sun');
                const root = document.documentElement;
                const stored = localStorage.getItem('darkMode');

                let isDark = stored === 'true' ? true : (stored === 'false' ? false : window.matchMedia('(prefers-color-scheme: dark)').matches);

                const applyTheme = () => {
                    if (isDark) {
                        root.classList.add('dark');
                        iconMoon.classList.add('hidden');
                        iconSun.classList.remove('hidden');
                    } else {
                        root.classList.remove('dark');
                        iconSun.classList.add('hidden');
                        iconMoon.classList.remove('hidden');
                    }
                };

                applyTheme();

                if (themeToggle) {
                    themeToggle.addEventListener('click', () => {
                        isDark = !isDark;
                        localStorage.setItem('darkMode', isDark);
                        applyTheme();
                    });
                }
            })();

            // Global Error Handler
            window.onerror = function(message, source, lineno, colno, error) {
                console.error(`Error: ${message} at ${source}:${lineno}:${colno}`, error);
                globalLoader.hide();
            };

            globalLoader.hide();
        });
    </script>
    @stack('scripts')
</body>
</html>