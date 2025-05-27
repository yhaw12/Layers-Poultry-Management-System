<aside class="fixed inset-y-0 left-0 w-64 h-screen bg-white dark:bg-gray-800 shadow-md transform transition-transform duration-300 ease-in-out md:translate-x-0 translate-x-full z-50" id="sidebar">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Poultry Tracker</h2>
        <button class="md:hidden text-gray-700 dark:text-gray-200 focus:outline-none" id="sidebar-toggle">
            <!-- close icon -->
        </button>
    </div>
    <nav class="p-4 space-y-4 text-sm font-medium text-gray-700 dark:text-gray-200 overflow-y-auto h-[calc(100vh-4rem)] custom-scrollbar">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition {{ Route::is('dashboard') ? 'bg-gray-200 dark:bg-gray-700' : '' }}"
           aria-current="{{ Route::is('dashboard') ? 'page' : 'false' }}">
            <span class="icon">🏠</span><span class="ml-2">Dashboard</span>
        </a>

        <!-- Eggs -->
        <div class="relative">
            <button data-target="eggs-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">🥚</span><span class="ml-2 uppercase font-semibold">Eggs</span>
                </div>
                <!-- caret icons -->
            </button>
            <div id="eggs-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('eggs.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('eggs.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Production
                </a>
                <a href="{{ route('eggs.sales') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('eggs.sales') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Sales
                </a>
            </div>f
        </div>

        <!-- Birds -->
        <div class="relative">
            <button data-target="birds-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">🐓</span><span class="ml-2 uppercase font-semibold">Birds</span>
                </div>
            </button>
            <div id="birds-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('birds.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('birds.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    All Birds
                </a>
                <a href="{{ route('birds.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('birds.create') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Add Batch
                </a>
            </div>
        </div>

        <!-- Feed -->
        <div class="relative">
            <button data-target="feed-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">🌾</span><span class="ml-2 uppercase font-semibold">Feed</span>
                </div>
            </button>
            <div id="feed-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('feed.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('feed.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Stock
                </a>
                <a href="{{ route('feed.consumption') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('feed.consumption') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Usage
                </a>
            </div>
        </div>

        <!-- Medicine Logs -->
        <div class="relative">
            <button data-target="medicine-logs-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">💊</span><span class="ml-2 uppercase font-semibold">Medicine Logs</span>
                </div>
            </button>
            <div id="medicine-logs-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('medicine-logs.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('medicine-logs.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    All Records
                </a>
                <a href="{{ route('medicine-logs.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('medicine-logs.create') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Add Record
                </a>
                <a href="{{ route('medicine-logs.buy') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('medicine-logs.buy') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Buy Medicine
                </a>
                <a href="{{ route('medicine-logs.use') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('medicine-logs.use') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Use Medicine
                </a>
            </div>
        </div>

        <!-- Inventory -->
        <div class="relative">
            <button data-target="inventory-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">📦</span><span class="ml-2 uppercase font-semibold">Inventory</span>
                </div>
            </button>
            <div id="inventory-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('inventory.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('inventory.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Items
                </a>
            </div>
        </div>

        <!-- Expenses -->
        <div class="relative">
            <button data-target="expenses-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">💰</span><span class="ml-2 uppercase font-semibold">Expenses</span>
                </div>
            </button>
            <div id="expenses-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('expenses.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('expenses.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    All
                </a>
                <a href="{{ route('expenses.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('expenses.create') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Add
                </a>
            </div>
        </div>

        <!-- Employees -->
        <div class="relative">
            <button data-target="employees-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">👷‍♂️</span><span class="ml-2 uppercase font-semibold">Employees</span>
                </div>
            </button>
            <div id="employees-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('employees.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('employees.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    All Employees
                </a>
                <a href="{{ route('employees.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('employees.create') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Add Employee
                </a>
            </div>
        </div>

        <!-- Payroll -->
        <div class="relative">
            <button data-target="payroll-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">💵</span><span class="ml-2 uppercase font-semibold">Payroll</span>
                </div>
            </button>
            <div id="payroll-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('payroll.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('payroll.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    All Payrolls
                </a>
                <a href="{{ route('payroll.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('payroll.create') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Add Payroll
                </a>
                <form method="POST" action="{{ route('payroll.generate') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                        Generate Monthly
                    </button>
                </form>
            </div>
        </div>

        <!-- Activity Logs -->
        @if(auth()->user()->is_admin)
        <div class="relative">
            <button data-target="logs-submenu" 
                    class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">📋</span>
                    <span class="ml-2 uppercase font-semibold">Activity Logs</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" ...></svg>
                    <svg class="minus-icon w-4 h-4 hidden" ...></svg>
                </div>
            </button>
            <div id="logs-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('activity-logs.index') }}"
                class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('activity-logs.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    View Logs
                </a>
            </div>
        </div>
        @endif

        <!-- Reports -->
        <div class="relative">
            <button data-target="reports-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">📊</span><span class="ml-2 uppercase font-semibold">Reports</span>
                </div>
            </button>
            <div id="reports-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('reports.daily') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('reports.daily') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Daily
                </a>
                <a href="{{ route('reports.weekly') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('reports.weekly') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Weekly
                </a>
                <a href="{{ route('reports.monthly') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('reports.monthly') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Monthly
                </a>
            </div>
        </div>

        <!-- Customers -->
        <div class="relative">
            <button data-target="customers-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <div class="flex items-center">
                    <span class="icon">🧑‍🤝‍🦶</span><span class="ml-2 uppercase font-semibold">Customers</span>
                </div>
            </button>
            <div id="customers-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('customers.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('customers.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    All Customers
                </a>
                <a href="{{ route('customers.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 {{ Route::is('customers.create') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                    Add Customer
                </a>
            </div>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center w-full px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 transition text-red-600 dark:text-red-400">
                <span class="icon">🚪</span><span class="ml-2">Logout</span>
            </button>
        </form>
    </nav>
</aside>


<!-- Mobile Menu Toggle Button -->
<button class="md:hidden fixed top-4 left-4 z-50 text-gray-700 dark:text-gray-200 focus:outline-none" id="mobile-menu-toggle">
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

    // Toggle submenu
    const toggleSubmenu = (toggle, submenuId) => {
        const submenu = document.getElementById(submenuId);
        const plusIcon = toggle.querySelector('.plus-icon');
        const minusIcon = toggle.querySelector('.minus-icon');
        const isOpen = submenu.classList.contains('open');

        // Close other open submenus
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

    // Handle submenu toggle clicks
    document.querySelectorAll('.toggle-btn').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const submenuId = toggle.getAttribute('data-target');
            toggleSubmenu(toggle, submenuId);
        });

        // Keyboard navigation
        toggle.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const submenuId = toggle.getAttribute('data-target');
                toggleSubmenu(toggle, submenuId);
            }
        });
    });

    // Toggle sidebar on mobile
    const toggleSidebar = () => {
        const isOpen = sidebar.classList.contains('translate-x-0');
        if (isOpen) {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('translate-x-full');
            sidebarOverlay.classList.add('hidden');
        } else {
            sidebar.classList.add('translate-x-0');
            sidebar.classList.remove('translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }
    };

    mobileMenuToggle.addEventListener('click', toggleSidebar);
    sidebarToggle?.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);

    // Close sidebar when clicking a link (mobile)
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                toggleSidebar();
            }
        });
    });

    // Ensure only one submenu is open at a time on page load
    document.querySelectorAll('.submenu').forEach(submenu => {
        submenu.classList.add('hidden', 'opacity-0');
    });
});
</script>