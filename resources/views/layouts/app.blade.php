<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Poultry Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-[#0a0a23] dark:text-white font-sans">
    <!-- Centralized Loader Overlay -->
    <div id="global-loader" class="loader-overlay hidden" onclick="globalLoader && globalLoader.hide()">
        <div class="flex flex-col items-center">
            <div class="loader-spinner"></div>
            <!-- Removed loader-message element -->
        </div>
    </div>

    @auth
        <div class="flex h-screen relative">

            <!-- Sidebar -->
            @include('partials.sidebar')

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
                                    <button id="notification-bell" 
                                            class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                            aria-label="View notifications" 
                                            aria-controls="notification-dropdown" 
                                            aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1m5 0H7" />
                                        </svg>
                                        <span id="notification-count" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center hidden">0</span>
                                    </button>
                                    
                                    <!-- Notification Dropdown -->
                                    <!-- Notification Dropdown (improved look) -->
                                    <div id="notification-dropdown"
                                        class="hidden absolute right-0 mt-2 w-96 bg-white dark:bg-gray-900 shadow-2xl rounded-xl border dark:border-gray-700 z-50"
                                        role="menu" aria-labelledby="notification-bell" aria-hidden="true">
                                        <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifications</h3>
                                            <span id="notification-count" class="text-xs bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200 rounded-full px-2 py-0.5 hidden">0</span>
                                        </div>

                                        <div id="notification-list" class="max-h-80 overflow-y-auto divide-y dark:divide-gray-800"></div>

                                        <div class="p-3 border-t dark:border-gray-700 flex items-center justify-between">
                                            <button id="dismiss-all-notifications" class="text-sm hover:underline">Dismiss All</button>
                                            <a href="{{ route('notifications.index') }}" class="text-sm hover:underline">View all</a>
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
    
    <script>
        // Centralized Loader Class
        class Loader {
            constructor() {
                this.loader = document.getElementById('global-loader');
                this.timeoutId = null;
                this.defaultTimeout = 10000;
                this.isShowing = false;
            }

            show(timeout = this.defaultTimeout) {
                if (this.loader && !this.isShowing) {
                    this.isShowing = true;
                    this.loader.classList.remove('hidden');
                    if (this.timeoutId) clearTimeout(this.timeoutId);
                    if (timeout) {
                        this.timeoutId = setTimeout(() => {
                            this.hide();
                            console.warn('Loader timeout: Page took too long to load.');
                        }, timeout);
                    }
                }
            }

            hide() {
                if (this.loader && this.isShowing) {
                    this.isShowing = false;
                    this.loader.classList.add('hidden');
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
                this.dropdown = document.getElementById('notification-dropdown');
                this.notificationList = document.getElementById('notification-list');
                this.notificationCount = document.getElementById('notification-count');
                this.dismissAllButton = document.getElementById('dismiss-all-notifications');
                this.bellButton = document.getElementById('notification-bell');
                this.notifications = [];
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                this.baseUrl = window.location.origin;
                this.pollInterval = null;
                this.dropdownOpen = false;
                
                if (!this.csrfToken) {
                    console.error('CSRF token missing');
                }
                
                if (!this.dropdown || !this.notificationList || !this.notificationCount || !this.bellButton) {
                    console.error('Notification DOM elements missing');
                    return;
                }
                
                this.init();
            }

            init() {
                this.bellButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleDropdown();
                });
                
                if (this.dismissAllButton) {
                    this.dismissAllButton.addEventListener('click', () => {
                        this.dismissAll();
                    });
                }
                
                document.addEventListener('click', (e) => {
                    if (!this.dropdown.contains(e.target) && !this.bellButton.contains(e.target)) {
                        this.closeDropdown();
                    }
                });
                
                this.fetchNotifications();
                this.startPolling();
            }

            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
                if (this.dropdownOpen) {
                    this.dropdown.classList.remove('hidden');
                    this.bellButton.setAttribute('aria-expanded', 'true');
                    this.fetchNotifications();
                } else {
                    this.dropdown.classList.add('hidden');
                    this.bellButton.setAttribute('aria-expanded', 'false');
                }
            }

            closeDropdown() {
                this.dropdownOpen = false;
                this.dropdown.classList.add('hidden');
                this.bellButton.setAttribute('aria-expanded', 'false');
            }

            async fetchNotifications() {
                if (!this.csrfToken || !this.notificationList) {
                    console.error('CSRF token or notification list missing');
                    return;
                }
                
                try {
                    const response = await fetch(`${this.baseUrl}/alerts`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error: ${response.status}`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    
                    const notifications = await response.json();
                    
                    if (Array.isArray(notifications)) {
                        this.notifications = notifications;
                        this.updateNotificationDropdown();
                        this.updateNotificationCount();
                    } else if (notifications.error) {
                        console.error('Server error:', notifications.error);
                        this.showError('Failed to load notifications');
                    } else {
                        console.warn('Unexpected response format:', notifications);
                    }
                } catch (error) {
                    console.error('Failed to fetch notifications:', error);
                    this.showError('Failed to load notifications. Please refresh.');
                }
            }

            async markAsRead(id) {
                if (!this.csrfToken) return;
                
                try {
                    const response = await fetch(`${this.baseUrl}/alerts/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error: ${response.status}`);
                    }
                    
                    const result = await response.json();
                    if (result.success) {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                        this.updateNotificationDropdown();
                        this.updateNotificationCount();
                    }
                } catch (error) {
                    console.error(`Failed to mark notification ${id} as read:`, error);
                    this.showError('Failed to mark as read. Please try again.');
                }
            }

            async dismissAll() {
                if (!this.csrfToken) return;
                
                try {
                    const response = await fetch(`${this.baseUrl}/alerts/dismiss-all`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error: ${response.status}`);
                    }
                    
                    const result = await response.json();
                    if (result.success) {
                        this.notifications = [];
                        this.updateNotificationDropdown();
                        this.updateNotificationCount();
                    }
                } catch (error) {
                    console.error('Failed to dismiss all notifications:', error);
                    this.showError('Failed to dismiss notifications. Please try again.');
                }
            }

            updateNotificationDropdown() {
                if (!this.notificationList) return;
                
                if (this.notifications.length > 0) {
                    this.notificationList.innerHTML = this.notifications.map(n => {
                        const bgClass = this.getBackgroundClass(n.type);
                        const textClass = this.getTextClass(n.type);
                        
                        return `
                            <div class="p-3 border-b dark:border-gray-700 ${bgClass}">
                                <p class="text-sm text-gray-800 dark:text-gray-200">${this.escapeHtml(n.message)}</p>
                                <div class="flex gap-2 mt-1">
                                    ${n.url && n.url !== '#' ? `<a href="${this.escapeHtml(n.url)}" class="text-blue-600 dark:text-blue-400 hover:underline text-xs">View</a>` : ''}
                                    <button class="text-blue-600 dark:text-blue-400 hover:underline text-xs" onclick="window.notificationManager.markAsRead('${n.id}')">Mark as Read</button>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    this.notificationList.innerHTML = '<p class="p-3 text-sm text-gray-600 dark:text-gray-400">No new notifications.</p>';
                }
            }

            updateNotificationCount() {
                if (!this.notificationCount) return;
                
                const count = this.notifications.length;
                this.notificationCount.textContent = count;
                
                if (count > 0) {
                    this.notificationCount.classList.remove('hidden');
                } else {
                    this.notificationCount.classList.add('hidden');
                }
            }

            getBackgroundClass(type) {
                const classes = {
                    'critical': 'bg-red-50 dark:bg-red-900/20',
                    'warning': 'bg-yellow-50 dark:bg-yellow-900/20',
                    'success': 'bg-green-50 dark:bg-green-900/20',
                    'info': 'bg-blue-50 dark:bg-blue-900/20',
                    'inventory': 'bg-indigo-50 dark:bg-indigo-900/20',
                    'sale': 'bg-green-50 dark:bg-green-900/20',
                    'mortality': 'bg-red-50 dark:bg-red-900/20',
                    'backup_success': 'bg-green-50 dark:bg-green-900/20',
                    'backup_failed': 'bg-red-50 dark:bg-red-900/20'
                };
                return classes[type] || classes['info'];
            }

            getTextClass(type) {
                const classes = {
                    'critical': 'text-red-600 dark:text-red-400',
                    'warning': 'text-yellow-600 dark:text-yellow-400',
                    'success': 'text-green-600 dark:text-green-400',
                    'info': 'text-blue-600 dark:text-blue-400'
                };
                return classes[type] || classes['info'];
            }

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            showError(message) {
                if (this.notificationList) {
                    this.notificationList.innerHTML = `<p class="p-3 text-sm text-red-600 dark:text-red-400">${this.escapeHtml(message)}</p>`;
                }
            }

            startPolling(interval = 30000) {
                if (this.pollInterval) {
                    clearInterval(this.pollInterval);
                }
                this.pollInterval = setInterval(() => {
                    this.fetchNotifications();
                }, interval);
            }

            stopPolling() {
                if (this.pollInterval) {
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                }
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
                    globalLoader.show();
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
            globalLoader.show();

            // Initialize notifications and start polling
            if (notificationManager.container && notificationManager.notificationList) {
                notificationManager.fetchNotifications();
                notificationManager.startPolling();
            }

            // Initialize search
            searchManager.init();

            // Notification Bell Toggle
           // Guard: ensure NotificationManager will handle bell (no extra handlers)
                const notificationBell = document.getElementById('notification-bell');
                const notificationDropdown = document.getElementById('notification-dropdown');
                if (!window.notificationManager && notificationBell && notificationDropdown) {
                    // fallback: no-op — NotificationManager should be present
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
                    globalLoader.show();
                }
            });

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

            // Mobile Menu Toggle for Sidebar
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.openSidebar(); // Call the openSidebar function from partials.sidebar
                });
            }

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