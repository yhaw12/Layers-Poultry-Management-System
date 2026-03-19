<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Poultry Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-[#070b19] dark:text-white font-sans transition-colors duration-300">
    
    <div id="global-loader" class="hidden fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white/70 dark:bg-[#070b19]/80 backdrop-blur-md transition-opacity duration-300" role="status" aria-live="polite">
        <div class="relative flex items-center justify-center">
            <div class="absolute inset-0 rounded-full blur-xl bg-blue-500/30 animate-pulse"></div>
            <svg class="relative w-14 h-14 text-blue-600 dark:text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                <path class="opacity-100" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <div id="global-loader-message" class="mt-6 text-xs font-black tracking-[0.2em] text-gray-800 dark:text-gray-300 uppercase">Processing...</div>
    </div>

    @auth
        <div class="flex h-screen relative overflow-hidden">
            @include('partials.sidebar')

            <div class="flex-1 flex flex-col h-screen">
                <header class="bg-white/80 dark:bg-[#0a0f25]/80 backdrop-blur-md shadow-sm dark:border-gray-800 dark:border-b sticky top-0 z-30 transition-all duration-300">
                    <nav class="px-4 py-3">
                        <div class="container mx-auto flex items-center justify-between gap-4 relative">
                            <div class="flex items-center gap-4">
                                <button id="mobile-menu-button" class="md:hidden p-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full" aria-label="Open sidebar">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                </button>
                                <a href="{{ route('dashboard') }}" class="text-xl font-black tracking-tight text-blue-600 dark:text-blue-400 flex items-center gap-2 hover:scale-105 transition-transform duration-200">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Poultry Tracker
                                </a>
                            </div>

                            <div class="flex items-center gap-3 md:gap-5">
                                <div class="relative flex items-center">
                                    <button id="search-toggle" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors focus:ring-2 focus:ring-blue-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </button>
                                    <div id="search-container" class="hidden absolute right-0 top-12 w-72 sm:w-80 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700 z-50 transform origin-top-right transition-all duration-200">
                                        <input type="text" id="search-input" placeholder="Search records..." class="w-full p-4 bg-transparent border-b border-gray-100 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none rounded-t-2xl">
                                        <div id="search-results" class="max-h-64 overflow-y-auto p-2"></div>
                                    </div>
                                </div>

                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('income.index') }}" class="hidden md:block text-sm font-bold text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Income</a>
                                    <a href="{{ route('reports.index') }}" class="hidden md:block text-sm font-bold text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Reports</a>
                                @endif

                                <div class="relative">
                                    <button id="notification-bell" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors focus:ring-2 focus:ring-blue-500">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1m5 0H7" /></svg>
                                        <span id="notification-count" class="hidden absolute top-0 right-0 bg-red-500 text-white text-[10px] font-black rounded-full h-4 w-4 flex items-center justify-center ring-2 ring-white dark:ring-[#0a0f25]">0</span>
                                    </button>
                                    <div id="notification-dropdown" class="hidden absolute right-0 mt-4 w-80 sm:w-96 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700 z-50">
                                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                            <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 dark:text-white">Notifications</h3>
                                            <button id="dismiss-all-notifications" class="text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400">Dismiss All</button>
                                        </div>
                                        <div id="notification-list" class="max-h-80 overflow-y-auto"></div>
                                        <a href="{{ route('notifications.index') }}" class="block p-3 text-center text-xs font-bold text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 bg-gray-50 dark:bg-gray-900/50 rounded-b-2xl transition-colors">View All History</a>
                                    </div>
                                </div>

                                <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-400 transition-colors focus:ring-2 focus:ring-blue-500">
                                    <svg id="icon-moon" class="h-5 w-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 116.707 2.707a6 6 0 1010.586 10.586z"/></svg>
                                    <svg id="icon-sun" class="h-5 w-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.03a1 1 0 10-1.44 1.44l.7.7a1 1 0 101.44-1.44l-.7-.7zM18 9a1 1 0 110 2h-1a1 1 0 110-2h1zM14.22 15.97a1 1 0 00-1.44-1.44l-.7.7a1 1 0 101.44 1.44l.7-.7zM11 18a1 1 0 10-2 0v-1a1 1 0 102 0v1zM6.78 15.97l-.7-.7a1 1 0 10-1.44 1.44l.7.7a1 1 0 001.44-1.44zM4 9a1 1 0 100 2H3a1 1 0 100-2h1zM6.78 4.03l.7.7a1 1 0 101.44-1.44l-.7-.7a1 1 0 00-1.44 1.44zM10 5a5 5 0 100 10A5 5 0 0010 5z"/></svg>
                                </button>

                                <div class="relative">
                                    <button id="user-menu-button" class="flex items-center gap-2 p-1 pl-2 pr-3 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors focus:ring-2 focus:ring-blue-500">
                                        <img src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" class="w-7 h-7 rounded-full object-cover">
                                        <span class="hidden md:block text-sm font-bold text-gray-700 dark:text-gray-200">{{ auth()->user()->name ?? 'User' }}</span>
                                    </button>
                                    <div id="user-menu" class="hidden absolute right-0 mt-4 w-48 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">
                                        <a href="{{ route('profile.edit') }}" class="block px-5 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Profile</a>
                                        <a href="{{ route('settings.index') }}" class="block px-5 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Settings</a>
                                        <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-5 py-3 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Sign Out</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>
                </header>

                <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                    @yield('content')
                </main>
            </div>
        </div>
    @endauth

    @guest
        <main>
            @yield('content')
        </main>
    @endguest
    
    <script>
        class Loader {
            constructor() {
                this.loader = document.getElementById('global-loader');
                this.messageEl = document.getElementById('global-loader-message');
            }
            show(message = 'Processing...') {
                if (!this.loader) return;
                this.loader.classList.remove('hidden');
                if (this.messageEl) this.messageEl.textContent = message;
            }
            hide() {
                if (!this.loader) return;
                this.loader.classList.add('hidden');
            }
        }
        window.globalLoader = new Loader();

        document.addEventListener('DOMContentLoaded', () => {
            document.addEventListener('submit', (event) => {
                if (event.target.tagName === 'FORM' && !event.target.classList.contains('no-loader')) {
                    window.globalLoader.show();
                }
            });

            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const html = document.documentElement;
                    const isDark = html.classList.toggle('dark');
                    localStorage.setItem('darkMode', isDark);
                });
            }

            const setupDropdown = (btnId, menuId) => {
                const btn = document.getElementById(btnId);
                const menu = document.getElementById(menuId);
                if(btn && menu) {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        menu.classList.toggle('hidden');
                    });
                    document.addEventListener('click', (e) => {
                        if (!btn.contains(e.target) && !menu.contains(e.target)) menu.classList.add('hidden');
                    });
                }
            };
            setupDropdown('user-menu-button', 'user-menu');
            setupDropdown('search-toggle', 'search-container');
        });
    </script>
    @stack('scripts')
</body>
</html>