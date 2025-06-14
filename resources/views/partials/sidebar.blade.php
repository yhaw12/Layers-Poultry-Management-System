<aside class="fixed inset-y-0 left-0 w-64 h-screen bg-white dark:bg-gray-900 shadow-lg transform transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full z-50" id="sidebar">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Poultry Tracker</h2>
        <button class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none" id="sidebar-toggle">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <nav class="p-4 space-y-1 text-sm font-medium text-gray-600 dark:text-gray-300 overflow-y-auto h-[calc(100vh-4rem)]">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ Route::is('dashboard') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
           aria-current="{{ Route::is('dashboard') ? 'page' : 'false' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3M9 21v-6a1 1 0 011-1h4a1 1 0 011 1v6" />
            </svg>
            Dashboard
        </a>

        <!-- Farm Management -->
        <div>
            <button data-target="farm-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-expanded="false">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    Farm Management
                </div>
                <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>
            <div id="farm-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('birds.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('birds.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                    Birds
                </a>
                <a href="{{ route('chicks.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('chicks.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                    Chicks
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
            <button data-target="resources-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-expanded="false">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v6l-8 4-8-4V7m16 0l-8 4-8-4" />
                    </svg>
                    Resources
                </div>
                <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
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

        <!-- Sales & Customers -->
        <div>
            <button data-target="sales-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-expanded="false">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Sales & Customers
                </div>
                <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
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

        <!-- Finances -->
        <div>
            <button data-target="finances-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-expanded="false">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Finances
                </div>
                <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>
            <div id="finances-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('expenses.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('expenses.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                    Expenses
                </a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('income.index') }}"
                       class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('income.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                        Income
                    </a>
                @endif
                <a href="{{ route('payroll.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('payroll.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                    Payroll
                </a>
            </div>
        </div>

        <!-- Employees -->
        <a href="{{ route('employees.index') }}"
           class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ Route::is('employees.*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300' : '' }}"
           aria-current="{{ Route::is('employees.*') ? 'page' : 'false' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Employees
        </a>

        <!-- Reports -->
        <div>
            <button data-target="reports-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-expanded="false">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a9 2 0 01-2 2z" />
                    </svg>
                    Reports
                </div>
                <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke="round" stroke-linejoin="round" stroke-width="2" stroke-width="2" d="M12 4v16m8-8h-16" />
                </svg>
                <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke="round" stroke-linejoin="round" stroke-width="2" stroke-width="2" d="M20" 12H4 />
                </svg>
            </button>
            <div id="reports-submenu" class="submenu hidden mt-1 ml-8 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('reports.index', ['type' => 'custom']) }}"
                   class="block px-4 py-4 py-2 rounded-lg" hover:bg-gray-50 dark:hover:bg-gray-700 {{ request()->query('type') === 'custom' ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                    Analytics
                </a>
                <a href="{{ route('reports.index', ['type' => 'profitability']) }}"
                   class="block px-4 py-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg" hover:bg-gray-50 {{ request()->query('type') === 'profitability' ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                    Profitability
                </a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('activity-logs.index') }}"
                       class="block px-4 py-4 py-2 rounded-lg" hover:bg-gray-50 dark:hover:bg-gray-700 {{ Route::is('activity-logs.*') ? 'bg-gray-50 dark:bg-gray-700 text-blue-600 dark:text-blue-300' : '' }}">
                        Activity Logs
                    </a>
                @endif
            </div>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center w-full px-4 py-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </nav>
</aside>

<!-- Mobile Menu Toggle Button -->
<button class="md:hidden fixed top-4 left-4 z-50 text-gray-600 dark:text-gray-300 focus:outline-none" id="mobile-menu-toggle">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<!-- Overlay for Mobile Sidebar -->
<div class="fixed inset-0 bg-black bg-opacity-50 hidden z-40" id="sidebar-overlay"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    let openSubmenuId = null;

    const toggleSubmenu = (toggle, submenuId) => {
        const submenu = document.getElementById(submenuId);
        const plusIcon = toggle.querySelector('.plus-icon');
        const minusIcon = toggle.querySelector('.minus-icon');
        const isOpen = submenu.classList.contains('open');

        if (openSubmenuId && openSubmenuId !== submenuId) {
            const prevSubmenu = document.getElementById(openSubmenuId);
            prevSubmenu.classList.remove('open', 'opacity-100');
            prevSubmenu.classList.add('hidden', 'opacity-0');
            const prevToggle = document.querySelector(`[data-target="${openSubmenuId}"]`);
            prevToggle.querySelector('.plus-icon').classList.remove('hidden');
            prevToggle.querySelector('.minus-icon').classList.add('hidden');
            prevToggle.setAttribute('aria-expanded', 'false');
        }

        if (isOpen) {
            submenu.classList.remove('open', 'opacity-100');
            submenu.classList.add('hidden', 'opacity-0');
            plusIcon.classList.remove('hidden');
            minusIcon.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
            openSubmenuId = null;
        } else {
            submenu.classList.add('open');
            submenu.classList.remove('hidden');
            setTimeout(() => submenu.classList.add('opacity-100'), 10);
            plusIcon.classList.add('hidden');
            minusIcon.classList.remove('hidden');
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
        const isOpen = sidebar.classList.contains('translate-x-0');
        if (isOpen) {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        } else {
            sidebar.classList.add('translate-x-0');
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }
    };

    mobileMenuToggle.addEventListener('click', toggleSidebar);
    sidebarToggle?.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);

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
});
</script>