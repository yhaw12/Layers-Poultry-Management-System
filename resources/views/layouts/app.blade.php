
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    @auth
        <div class="flex h-screen relative">
            <!-- Sidebar Overlay for Mobile -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

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
                                <button id="mobile-menu-button" class="md:hidden p-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full" aria-label="Open sidebar" aria-controls="sidebar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
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
                                    <button id="search-toggle" class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Toggle search" aria-expanded="false">
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

                                <!-- Notification Bell for Alerts -->
                                <div class="relative">
                                    <button id="notification-bell" class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="View notifications">
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
                                    <button id="user-menu-button" class="flex items-center gap-2 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="User menu">
                                        <img src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar . '?v=' . auth()->user()->updated_at->timestamp) : asset('images/default-avatar.png') }}" alt="{{ auth()->user()->name ?? 'User' }}'s avatar" class="h-5 w-5 rounded-full object-cover">
                                        <span class="hidden md:block text-blue-800 dark:text-blue-400 font-semibold text-sm">
                                            {{ auth()->user()->name ?? 'Unknown User' }}
                                        </span>
                                        <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 shadow-lg rounded-lg border dark:border-gray-700 z-50">
                                        <div class="p-4 border-b dark:border-gray-700">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name ?? 'Unknown User' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ auth()->user()->email ?? 'No email' }}</p>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    console.log('app.blade.php script loaded');

    // Centralized Loader Class
    class Loader {
        constructor() {
            this.loader = document.getElementById('global-loader');
            this.messageEl = document.getElementById('loader-message');
            this.timeoutId = null;
            this.defaultTimeout = 5000; // Reduced for better UX
            this.isShowing = false;
        }

        show(message = 'Loading...', timeout = this.defaultTimeout) {
            if (this.loader && !this.isShowing) {
                this.isShowing = true;
                this.loader.classList.remove('hidden');
                if (message && this.messageEl) {
                    this.messageEl.textContent = message;
                    this.messageEl.classList.remove('hidden');
                } else if (this.messageEl) {
                    this.messageEl.classList.add('hidden');
                }
                if (this.timeoutId) clearTimeout(this.timeoutId);
                if (timeout) {
                    this.timeoutId = setTimeout(() => {
                        this.hide();
                        console.warn('Loader timeout: Operation took too long.');
                        if (this.messageEl) {
                            this.messageEl.textContent = 'Operation timed out. Click to dismiss.';
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

    // Global Loader Instance
    const globalLoader = new Loader();

    // Notification Manager
    class NotificationManager {
        constructor() {
            this.dropdown = document.getElementById('notification-dropdown');
            this.notificationList = document.getElementById('notification-list');
            this.notificationCount = document.getElementById('notification-count');
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            this.notifications = [];
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 60000);
        }

        async fetchNotifications() {
            try {
                if (!this.csrfToken) throw new Error('CSRF token missing');
                globalLoader.show('Fetching notifications...');
                const response = await fetch('/alerts', {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) {
                    if (response.status === 401) {
                        this.show(Date.now().toString(), 'Please log in to view notifications.', 'critical', 5000);
                    } else {
                        throw new Error(`HTTP error: ${response.status}`);
                    }
                    return;
                }
                const data = await response.json();
                if (data.error) {
                    this.notifications = [];
                } else {
                    this.notifications = data.map(({ id, message, type, url }) => ({
                        id, message, type, url: url || '#'
                    }));
                }
                this.updateNotificationDropdown();
                this.updateNotificationCount();
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
                this.show(Date.now().toString(), 'Failed to load notifications. Please try again.', 'critical', 5000);
            } finally {
                globalLoader.hide();
            }
        }

        show(id, message, type = 'info', timeout = 5000, url = '#') {
            if (!this.notifications.some(n => n.id === id)) {
                this.notifications.push({ id, message, type, url });
                this.updateNotificationDropdown();
                this.updateNotificationCount();
                if (timeout > 0) {
                    setTimeout(() => this.dismiss(id), timeout);
                }
            }
        }

        dismiss(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
            this.updateNotificationDropdown();
            this.updateNotificationCount();
        }

        async markAsRead(id) {
            try {
                if (!this.csrfToken) throw new Error('CSRF token missing');
                const response = await fetch(`/alerts/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const result = await response.json();
                if (result.success) {
                    this.dismiss(id);
                } else {
                    throw new Error(result.error || 'Failed to mark as read');
                }
            } catch (error) {
                console.error(`Failed to mark notification ${id} as read:`, error);
                this.show(Date.now().toString(), 'Failed to mark notification as read.', 'critical', 5000);
            }
        }

        async dismissAll() {
            try {
                if (!this.csrfToken) throw new Error('CSRF token missing');
                const response = await fetch('/alerts/dismiss-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const result = await response.json();
                if (result.success) {
                    this.notifications = [];
                    this.updateNotificationDropdown();
                    this.updateNotificationCount();
                } else {
                    throw new Error(result.error || 'Failed to dismiss all notifications');
                }
            } catch (error) {
                console.error('Failed to dismiss all notifications:', error);
                this.show(Date.now().toString(), 'Failed to dismiss notifications.', 'critical', 5000);
            }
        }

        updateNotificationDropdown() {
            if (this.notificationList) {
                this.notificationList.innerHTML = this.notifications.length > 0
                    ? this.notifications.map(n => `
                        <div class="p-3 border-b dark:border-gray-700 ${n.type === 'critical' ? 'bg-red-50 dark:bg-red-900' : n.type === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900' : n.type === 'success' ? 'bg-green-50 dark:bg-green-900' : 'bg-blue-50 dark:bg-blue-900'}">
                            <a href="${n.url}" class="text-sm text-gray-800 dark:text-gray-200 hover:underline">${n.message}</a>
                            <button class="text-blue-600 dark:text-blue-400 hover:underline text-xs mt-1" onclick="notificationManager.markAsRead('${n.id}')">Mark as Read</button>
                        </div>
                    `).join('')
                    : '<p class="p-3 text-sm text-gray-600 dark:text-gray-400">No new notifications.</p>';
            }
        }

        updateNotificationCount() {
            if (this.notificationCount) {
                const count = this.notifications.length;
                this.notificationCount.textContent = count;
                this.notificationCount.classList.toggle('hidden', count === 0);
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
            console.log('SearchManager initialized:', { container: !!this.container, input: !!this.input, toggleButton: !!this.toggleButton });
        }

        init() {
            if (!this.input || !this.container || !this.results || !this.toggleButton) {
                console.error('SearchManager initialization failed: Missing elements');
                return;
            }

            this.toggleButton.addEventListener('click', () => {
                console.log('Search toggle clicked');
                const isHidden = this.container.classList.contains('hidden');
                if (isHidden) {
                    this.container.classList.remove('hidden');
                    this.container.classList.add('translate-x-0');
                    this.container.classList.remove('translate-x-full');
                    this.toggleButton.setAttribute('aria-expanded', 'true');
                    this.input.focus();
                } else {
                    this.container.classList.add('translate-x-full');
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

            // Add Escape key to close search
            this.input.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.container.classList.add('translate-x-full');
                    setTimeout(() => {
                        this.container.classList.add('hidden');
                        this.input.value = '';
                        this.results.innerHTML = '';
                    }, 300);
                    this.toggleButton.setAttribute('aria-expanded', 'false');
                }
            });
        }

        async performSearch(query) {
            try {
                if (!this.csrfToken) throw new Error('CSRF token missing');
                globalLoader.show('Searching...');
                const response = await fetch(`/search?q=${encodeURIComponent(query)}`, {
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
                notificationManager.show(Date.now().toString(), 'Failed to perform search.', 'critical', 5000);
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

    // Sidebar Manager
    class SidebarManager {
        constructor() {
            this.sidebar = document.getElementById('sidebar');
            this.sidebarToggle = document.getElementById('sidebar-toggle');
            this.mobileMenuButton = document.getElementById('mobile-menu-button');
            this.sidebarOverlay = document.getElementById('sidebar-overlay');
            this.openSubmenuId = null;
            console.log('SidebarManager initialized:', {
                sidebar: !!this.sidebar,
                sidebarToggle: !!this.sidebarToggle,
                mobileMenuButton: !!this.mobileMenuButton,
                sidebarOverlay: !!this.sidebarOverlay
            });
        }

        init() {
            if (!this.sidebar || !this.mobileMenuButton || !this.sidebarOverlay) {
                console.error('SidebarManager initialization failed: Missing elements');
                return;
            }

            this.initializeSidebar();
            window.addEventListener('resize', () => this.initializeSidebar());

            if (this.mobileMenuButton) {
                this.mobileMenuButton.addEventListener('click', () => this.toggleSidebar());
            }
            if (this.sidebarToggle) {
                this.sidebarToggle.addEventListener('click', () => this.toggleSidebar());
            }
            if (this.sidebarOverlay) {
                this.sidebarOverlay.addEventListener('click', () => this.toggleSidebar());
            }

            document.querySelectorAll('nav a, nav button[type="submit"]').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768) {
                        this.toggleSidebar();
                    }
                });
            });

            document.querySelectorAll('.toggle-btn').forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const submenuId = toggle.getAttribute('data-target');
                    this.toggleSubmenu(toggle, submenuId);
                });

                toggle.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        const submenuId = toggle.getAttribute('data-target');
                        this.toggleSubmenu(toggle, submenuId);
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.openSubmenuId) {
                    const submenu = document.getElementById(this.openSubmenuId);
                    const toggle = document.querySelector(`[data-target="${this.openSubmenuId}"]`);
                    submenu.classList.remove('open', 'opacity-100');
                    submenu.classList.add('hidden', 'opacity-0');
                    toggle.querySelector('svg').classList.remove('rotate-180');
                    toggle.setAttribute('aria-expanded', 'false');
                    this.openSubmenuId = null;
                }
            });

            document.querySelectorAll('.submenu').forEach(submenu => {
                submenu.classList.add('hidden', 'opacity-0');
            });
        }

        initializeSidebar() {
            if (window.innerWidth >= 768) {
                this.sidebar.classList.remove('-translate-x-full', 'hidden');
                this.sidebar.classList.add('translate-x-0');
                this.sidebarOverlay.classList.add('hidden');
                this.sidebar.style.opacity = '1';
            } else {
                this.sidebar.classList.add('-translate-x-full');
                this.sidebar.classList.remove('translate-x-0');
                this.sidebarOverlay.classList.add('hidden');
                this.sidebar.style.opacity = '1';
                if (this.openSubmenuId) {
                    const submenu = document.getElementById(this.openSubmenuId);
                    submenu.classList.remove('open', 'opacity-100');
                    submenu.classList.add('hidden', 'opacity-0');
                    const toggle = document.querySelector(`[data-target="${this.openSubmenuId}"]`);
                    toggle.querySelector('svg').classList.remove('rotate-180');
                    toggle.setAttribute('aria-expanded', 'false');
                    this.openSubmenuId = null;
                }
            }
        }

        toggleSidebar() {
            const isOpen = this.sidebar.classList.contains('translate-x-0');
            if (isOpen) {
                this.sidebar.classList.remove('translate-x-0');
                this.sidebar.classList.add('-translate-x-full');
                this.sidebarOverlay.classList.add('hidden');
                if (this.openSubmenuId) {
                    const submenu = document.getElementById(this.openSubmenuId);
                    submenu.classList.remove('open', 'opacity-100');
                    submenu.classList.add('hidden', 'opacity-0');
                    const toggle = document.querySelector(`[data-target="${this.openSubmenuId}"]`);
                    toggle.querySelector('svg').classList.remove('rotate-180');
                    toggle.setAttribute('aria-expanded', 'false');
                    this.openSubmenuId = null;
                }
            } else {
                this.sidebar.classList.add('translate-x-0');
                this.sidebar.classList.remove('-translate-x-full', 'hidden');
                this.sidebarOverlay.classList.remove('hidden');
                this.sidebar.style.opacity = '1';
            }
        }

        toggleSubmenu(toggle, submenuId) {
            const submenu = document.getElementById(submenuId);
            const chevron = toggle.querySelector('svg');
            const isOpen = submenu.classList.contains('open');

            if (this.openSubmenuId && this.openSubmenuId !== submenuId) {
                const prevSubmenu = document.getElementById(this.openSubmenuId);
                prevSubmenu.classList.remove('open', 'opacity-100');
                prevSubmenu.classList.add('hidden', 'opacity-0');
                const prevToggle = document.querySelector(`[data-target="${this.openSubmenuId}"]`);
                prevToggle.querySelector('svg').classList.remove('rotate-180');
                prevToggle.setAttribute('aria-expanded', 'false');
            }

            if (isOpen) {
                submenu.classList.remove('open', 'opacity-100');
                submenu.classList.add('hidden', 'opacity-0');
                chevron.classList.remove('rotate-180');
                toggle.setAttribute('aria-expanded', 'false');
                this.openSubmenuId = null;
            } else {
                submenu.classList.add('open');
                submenu.classList.remove('hidden');
                setTimeout(() => submenu.classList.add('opacity-100'), 10);
                chevron.classList.add('rotate-180');
                toggle.setAttribute('aria-expanded', 'true');
                this.openSubmenuId = submenuId;
            }
        }
    }

    // Global Error Handler
    window.onerror = function(message, source, lineno, colno, error) {
        console.error(`Error: ${message} at ${source}:${lineno}:${colno}`, error);
        notificationManager.show(Date.now().toString(), 'An error occurred. Please refresh.', 'critical', 5000);
        globalLoader.hide();
    };

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        globalLoader.show('Loading page...');

        // Initialize Managers
        const notificationManager = new NotificationManager();
        const searchManager = new SearchManager();
        const sidebarManager = new SidebarManager();
        notificationManager.fetchNotifications();
        searchManager.init();
        sidebarManager.init();

        // Notification Bell Toggle
        const notificationBell = document.getElementById('notification-bell');
        const notificationDropdown = document.getElementById('notification-dropdown');
        if (notificationBell && notificationDropdown) {
            notificationBell.addEventListener('click', () => {
                console.log('Notification bell toggled');
                notificationDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }

        // Dismiss All Notifications
        const dismissAllButton = document.getElementById('dismiss-all-notifications');
        if (dismissAllButton) {
            dismissAllButton.addEventListener('click', () => {
                console.log('Dismiss All Notifications clicked');
                notificationManager.dismissAll();
            });
        }

        // User Menu Toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', () => {
                console.log('User menu toggled');
                userMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }

        // Dark Mode Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const iconMoon = document.getElementById('icon-moon');
        const iconSun = document.getElementById('icon-sun');
        const root = document.documentElement;
        let isDark = localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);

        function applyTheme() {
            if (isDark) {
                root.classList.add('dark');
                iconMoon.classList.add('hidden');
                iconSun.classList.remove('hidden');
            } else {
                root.classList.remove('dark');
                iconSun.classList.add('hidden');
                iconMoon.classList.remove('hidden');
            }
        }

        applyTheme();

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                isDark = !isDark;
                localStorage.setItem('darkMode', isDark);
                console.log('Dark mode toggled:', isDark);
                applyTheme();
            });
        }

        // Handle form submissions
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (form.tagName === 'FORM') {
                globalLoader.show('Submitting form...');
            }
        });

        // Refresh avatar on profile update
        @if (session('success'))
            const avatarImg = document.querySelector('#user-menu-button img');
            if (avatarImg) {
                const newAvatar = '{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar . '?v=' . auth()->user()->updated_at->timestamp) : asset('images/default-avatar.png') }}';
                avatarImg.src = newAvatar;
                avatarImg.alt = '{{ auth()->user()->name ?? 'User' }}'s avatar';
            }
        @endif

        // Fallback: Hide loader after 5 seconds
        setTimeout(() => {
            if (globalLoader.isShowing) {
                console.warn('Fallback: Hiding loader after 5 seconds');
                globalLoader.hide();
            }
        }, 5000);
    });

    // Handle dynamic elements (e.g., Livewire)
    const observer = new MutationObserver(() => {
        const sidebarManager = new SidebarManager();
        sidebarManager.init();
    });
    observer.observe(document.body, { childList: true, subtree: true });
</script>
    @stack('scripts')
</body>
</html>
