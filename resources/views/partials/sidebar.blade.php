{{-- partials/sidebar.blade.php --}}
{{-- NOTE: If you also have a #sidebar-overlay in layouts.app, remove that duplicate overlay to avoid conflicts. Keep only one #sidebar-overlay in your app. --}}

<aside id="sidebar" class="sidebar bg-white dark:bg-gray-900 shadow-lg h-screen fixed top-0 left-0 w-3/4 max-w-[280px] md:w-64 transform -translate-x-full md:translate-x-0 md:static z-50 transition-transform duration-300 ease-in-out hidden md:block" aria-hidden="true">
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
        @can('view_dashboard')
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 {{ Route::is('dashboard') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
               aria-current="{{ Route::is('dashboard') ? 'page' : 'false' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3M9 21v-6a1 1 0 011-1h4a1 1 0 011 1v6" />
                </svg>
                Dashboard
            </a>
        @endcan

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
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('birds.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('birds.*') ? 'page' : 'false' }}">
                    Birds
                </a>
                
                <a href="{{ route('eggs.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('eggs.index') || Route::is('eggs.sales') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('eggs.index') || Route::is('eggs.sales') ? 'page' : 'false' }}">
                    Eggs
                </a>
                <a href="{{ route('mortalities.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('mortalities.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('mortalities.*') ? 'page' : 'false' }}">
                    Mortalities
                </a>
                <a href="{{ route('vaccination-logs.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('vaccination-logs.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('vaccination-logs.*') ? 'page' : 'false' }}">
                    Vaccinations
                </a>
            </div>
        </div>

        <!-- Health & Diseases -->
        <div>
            <button data-target="health-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-expanded="false" aria-controls="health-submenu">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Health & Diseases
                </div>
                <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="health-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('health-checks.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('health-checks.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('health-checks.*') ? 'page' : 'false' }}">
                    Health Checks
                </a>
                <a href="{{ route('diseases.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('diseases.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('diseases.*') ? 'page' : 'false' }}">
                    Diseases
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
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('feed.index') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('feed.index') ? 'page' : 'false' }}">
                    Feed
                </a>
                
                <a href="{{ route('medicine-logs.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('medicine-logs.index') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('medicine-logs.index') ? 'page' : 'false' }}">
                    Medicine Logs
                </a>
                
                <a href="{{ route('inventory.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('inventory.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('inventory.*') ? 'page' : 'false' }}">
                    Inventory
                </a>
                
                <a href="{{ route('suppliers.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('suppliers.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('suppliers.*') ? 'page' : 'false' }}">
                    Suppliers
                </a>
            </div>
        </div>

        <!-- Sales & Customers -->
        @can('manage_sales')
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
                       class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('sales.index') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                       aria-current="{{ Route::is('sales.index') ? 'page' : 'false' }}">
                        Sales
                    </a>
                    {{-- <a href="{{ route('invoices.index') }}"
                       class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('invoices.index') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                       aria-current="{{ Route::is('invoices.index') ? 'page' : 'false' }}">
                        Invoices
                    </a> --}}
                    <a href="{{ route('customers.index') }}"
                       class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('customers.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                       aria-current="{{ Route::is('customers.*') ? 'page' : 'false' }}">
                        Customers
                    </a>
                    <a href="{{ route('orders.index') }}"
                       class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('orders.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                       aria-current="{{ Route::is('orders.*') ? 'page' : 'false' }}">
                        Orders
                    </a>
                </div>
            </div>
        @endcan

        <!-- Finances -->
        <div>
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
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('expenses.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('expenses.*') ? 'page' : 'false' }}">
                    Expenses
                </a>
                @can('manage_finances')
                    <a href="{{ route('income.index') }}"
                       class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('income.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                       aria-current="{{ Route::is('income.*') ? 'page' : 'false' }}">
                        Income
                    </a>
                @endcan
                <a href="{{ route('payroll.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('payroll.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('payroll.*') ? 'page' : 'false' }}">
                    Payroll
                </a>
                <a href="{{ route('transactions.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('transactions.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}"
                   aria-current="{{ Route::is('transactions.*') ? 'page' : 'false' }}">
                    Transactions
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
       

        <!-- Reports -->
        <a href="{{ route('reports.index') }}"
           class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 {{ Route::is('alerts.index') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
           aria-current="{{ Route::is('reports.index') ? 'page' : 'false' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1m5 0H7" />
            </svg>
            Reports
        </a>

         @endrole

        

        <!-- Alerts -->
        <a href="{{ route('notifications.index') }}"
           class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 {{ Route::is('alerts.index') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
           aria-current="{{ Route::is('notifications.index') ? 'page' : 'false' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V5a2 2 0 10-4 0v.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m5 0v1a3 3 0 11-6 0v-1m5 0H7" />
            </svg>
            Alerts
        </a>

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

<!-- Overlay for Mobile Sidebar (single source of truth) -->
<div class="fixed inset-0 bg-black bg-opacity-50 hidden z-40" id="sidebar-overlay" aria-hidden="true"></div>

<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle'); // close button inside sidebar (mobile)
    const mobileMenuButton = document.getElementById('mobile-menu-button'); // top-left hamburger (in layouts.app)
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const BODY = document.body;
    let openSubmenuId = null;
    let previouslyFocusedElement = null;
    let focusTrapHandler = null;

    // Utility: get all focusable elements inside sidebar
    function getFocusableElements() {
        if (!sidebar) return [];
        const selectors = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
        return Array.from(sidebar.querySelectorAll(selectors)).filter(el => el.offsetParent !== null);
    }

    // Open sidebar for mobile
    function openSidebar() {
        if (!sidebar) return;
        previouslyFocusedElement = document.activeElement;

        // Visual classes
        sidebar.classList.remove('hidden', '-translate-x-full');
        sidebar.classList.add('translate-x-0');
        if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');

        // Accessibility
        sidebar.setAttribute('aria-hidden', 'false');
        if (mobileMenuButton) mobileMenuButton.setAttribute('aria-expanded', 'true');

        // Prevent background scroll
        BODY.classList.add('overflow-hidden', 'touch-none');

        // Focus trap: focus first focusable in sidebar
        const focusables = getFocusableElements();
        if (focusables.length) focusables[0].focus();

        // Install focus trap handler
        focusTrapHandler = function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeSidebar();
                return;
            }
            if (e.key === 'Tab') {
                const focusables = getFocusableElements();
                if (!focusables.length) return;
                const first = focusables[0];
                const last = focusables[focusables.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        };
        document.addEventListener('keydown', focusTrapHandler, true);
    }

    // Close sidebar
    function closeSidebar() {
        if (!sidebar) return;

        // Animate out
        sidebar.classList.add('-translate-x-full');
        // After animation, hide completely
        setTimeout(() => {
            sidebar.classList.add('hidden');
            // remove classes that keep it visible on mobile
            sidebar.classList.remove('translate-x-0');
        }, 260); // match Tailwind transition (300ms in your markup, 260ms safe)

        if (sidebarOverlay) sidebarOverlay.classList.add('hidden');

        // Accessibility
        sidebar.setAttribute('aria-hidden', 'true');
        if (mobileMenuButton) mobileMenuButton.setAttribute('aria-expanded', 'false');

        // Restore scroll
        BODY.classList.remove('overflow-hidden', 'touch-none');

        // Remove focus trap
        if (focusTrapHandler) {
            document.removeEventListener('keydown', focusTrapHandler, true);
            focusTrapHandler = null;
        }

        // Close any open submenu
        if (openSubmenuId) {
            const prev = document.getElementById(openSubmenuId);
            if (prev) {
                prev.classList.remove('open', 'opacity-100');
                prev.classList.add('hidden', 'opacity-0');
            }
            const prevToggle = document.querySelector(`[data-target="${openSubmenuId}"]`);
            if (prevToggle) {
                const svg = prevToggle.querySelector('svg');
                if (svg) svg.classList.remove('rotate-180');
                prevToggle.setAttribute('aria-expanded', 'false');
            }
            openSubmenuId = null;
        }

        // restore focus for keyboard users
        if (previouslyFocusedElement && typeof previouslyFocusedElement.focus === 'function') {
            setTimeout(() => previouslyFocusedElement.focus(), 100);
            previouslyFocusedElement = null;
        }
    }

    // Toggle submenu with good mobile behavior (one open at a time)
    function toggleSubmenu(toggleEl, submenuId) {
        const submenu = document.getElementById(submenuId);
        if (!submenu) return;

        const chevron = toggleEl.querySelector('svg');
        const isOpen = submenu.classList.contains('open');

        // close previous if different
        if (openSubmenuId && openSubmenuId !== submenuId) {
            const prev = document.getElementById(openSubmenuId);
            if (prev) {
                prev.classList.remove('open', 'opacity-100');
                prev.classList.add('hidden', 'opacity-0');
            }
            const prevToggle = document.querySelector(`[data-target="${openSubmenuId}"]`);
            if (prevToggle) {
                const prevSvg = prevToggle.querySelector('svg');
                if (prevSvg) prevSvg.classList.remove('rotate-180');
                prevToggle.setAttribute('aria-expanded', 'false');
            }
            openSubmenuId = null;
        }

        if (isOpen) {
            submenu.classList.remove('open', 'opacity-100');
            submenu.classList.add('hidden', 'opacity-0');
            if (chevron) chevron.classList.remove('rotate-180');
            toggleEl.setAttribute('aria-expanded', 'false');
            openSubmenuId = null;
        } else {
            submenu.classList.add('open');
            submenu.classList.remove('hidden');
            // small delay so opacity transition works
            setTimeout(() => submenu.classList.add('opacity-100'), 10);
            if (chevron) chevron.classList.add('rotate-180');
            toggleEl.setAttribute('aria-expanded', 'true');
            openSubmenuId = submenuId;
        }
    }

    // Initialize mobile / desktop stable states
    function initializeSidebarState() {
        if (!sidebar) return;
        if (window.innerWidth >= 768) {
            // Desktop: show sidebar
            sidebar.classList.remove('hidden', '-translate-x-full');
            sidebar.classList.add('translate-x-0');
            sidebar.setAttribute('aria-hidden', 'false');
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
            BODY.classList.remove('overflow-hidden', 'touch-none');
        } else {
            // Mobile: hide sidebar by default
            sidebar.classList.add('hidden', '-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            sidebar.setAttribute('aria-hidden', 'true');
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
        }

        // ensure submenus start hidden (for consistent animation)
        document.querySelectorAll('.submenu').forEach(s => {
            if (!s.classList.contains('open')) {
                s.classList.add('hidden', 'opacity-0');
                s.classList.remove('opacity-100');
            }
        });
    }

    // Event wiring
    function wireEvents() {
        // Header hamburger opens mobile sidebar
        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                openSidebar();
            });
        }

        // Close button inside sidebar
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', (e) => {
                e.preventDefault();
                closeSidebar();
            });
        }

        // overlay click closes
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', (e) => {
                e.preventDefault();
                closeSidebar();
            });
        }

        // Escape at document level should also close when sidebar open
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('hidden')) {
                closeSidebar();
            }
        });

        // Submenu toggles (keyboard accessible)
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

        // Links inside nav should close menu on mobile after click
        document.querySelectorAll('#sidebar nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) closeSidebar();
            });
        });

        // Keep layout responsive on resize
        window.addEventListener('resize', () => {
            // if desktop breakpoint reached, ensure overlay removed and body scroll restored
            if (window.innerWidth >= 768) {
                if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
                BODY.classList.remove('overflow-hidden', 'touch-none');
                // ensure sidebar visible for desktop
                sidebar.classList.remove('hidden', '-translate-x-full');
                sidebar.classList.add('translate-x-0');
                sidebar.setAttribute('aria-hidden', 'false');
            } else {
                // mobile: hide sidebar by default
                sidebar.classList.add('hidden', '-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                sidebar.setAttribute('aria-hidden', 'true');
            }
        });
    }

    // expose functions globally so layout can call window.openSidebar()
    window.openSidebar = openSidebar;
    window.closeSidebar = closeSidebar;

    // optional: expose for debugging
    window.__sidebar = {
        openSidebar,
        closeSidebar,
        toggleSubmenu,
        getSidebarElement: () => sidebar
    };

    // Boot
    initializeSidebarState();
    wireEvents();
})();
</script>
