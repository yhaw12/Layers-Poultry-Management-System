<aside class="fixed inset-y-0 left-0 w-64 h-screen bg-gray-100 dark:bg-gray-900 shadow-md transform transition-transform duration-300 ease-in-out md:translate-x-0 translate-x-full z-50" id="sidebar">
    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Poultry Tracker</h2>
        <button class="md:hidden text-gray-700 dark:text-gray-200 focus:outline-none" id="sidebar-toggle">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <nav class="p-4 space-y-4 text-sm font-medium text-gray-800 dark:text-gray-200 overflow-y-auto h-[calc(100vh-4rem)] custom-scrollbar">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent {{ Route::is('dashboard') ? 'bg-gray-200 dark:bg-gray-800 border-blue-500' : '' }}"
           aria-current="{{ Route::is('dashboard') ? 'page' : 'false' }}">
            <span class="icon">🏠</span><span class="ml-2">Dashboard</span>
        </a>

        <!-- Eggs -->
        <div class="relative">
            <button data-target="eggs-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">🥚</span><span class="ml-2 uppercase font-semibold">Eggs</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="eggs-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('eggs.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('eggs.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Production
                </a>
                <a href="{{ route('eggs.sales') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('eggs.sales') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Sales
                </a>
            </div>
        </div>

        <!-- Birds -->
        <div class="relative">
            <button data-target="birds-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">🐓</span><span class="ml-2 uppercase font-semibold">Birds</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="birds-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('birds.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('birds.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    All Birds
                </a>
                <a href="{{ route('birds.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('birds.create') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Add Batch
                </a>
            </div>
        </div>

        <!-- Feed -->
        <div class="relative">
            <button data-target="feed-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">🌾</span><span class="ml-2 uppercase font-semibold">Feed</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="feed-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('feed.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('feed.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Stock
                </a>
                <a href="{{ route('feed.consumption') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('feed.consumption') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Usage
                </a>
            </div>
        </div>

        <!-- Medicine Logs -->
        <div class="relative">
            <button data-target="medicine-logs-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">💊</span><span class="ml-2 uppercase font-semibold">Medicine Logs</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="medicine-logs-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('medicine-logs.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('medicine-logs.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    All Records
                </a>
                <a href="{{ route('medicine-logs.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('medicine-logs.create') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Add Record
                </a>
                <a href="{{ route('medicine-logs.buy') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('medicine-logs.buy') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Buy Medicine
                </a>
                <a href="{{ route('medicine-logs.use') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('medicine-logs.use') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Use Medicine
                </a>
            </div>
        </div>

        <!-- Inventory -->
        <div class="relative">
            <button data-target="inventory-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">📦</span><span class="ml-2 uppercase font-semibold">Inventory</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="inventory-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('inventory.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('inventory.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Items
                </a>
            </div>
        </div>

        <!-- Expenses -->
        <div class="relative">
            <button data-target="expenses-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">💰</span><span class="ml-2 uppercase font-semibold">Expenses</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="expenses-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('expenses.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('expenses.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    All
                </a>
                <a href="{{ route('expenses.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('expenses.create') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Add
                </a>
            </div>
        </div>

        <!-- Employees -->
        <div class="relative">
            <button data-target="employees-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">👷‍♂️</span><span class="ml-2 uppercase font-semibold">Employees</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="employees-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('employees.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('employees.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    All Employees
                </a>
                <a href="{{ route('employees.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('employees.create') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Add Employee
                </a>
            </div>
        </div>

        <!-- Payroll -->
        <div class="relative">
            <button data-target="payroll-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">💵</span><span class="ml-2 uppercase font-semibold">Payroll</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="payroll-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('payroll.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('payroll.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    All Payrolls
                </a>
                <a href="{{ route('payroll.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('payroll.create') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Add Payroll
                </a>
                <form method="POST" action="{{ route('payroll.generate') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent">
                        Generate Monthly
                    </button>
                </form>
            </div>
        </div>

        <!-- Activity Logs -->
        @if(auth()->user()->is_admin)
        <div class="relative">
            <button data-target="logs-submenu" 
                    class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">📋</span>
                    <span class="ml-2 uppercase font-semibold">Activity Logs</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="logs-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('activity-logs.index') }}"
                class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('activity-logs.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    View Logs
                </a>
            </div>
        </div>
        @endif

        <!-- Reports -->
        <div class="relative">
            <button data-target="reports-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">📊</span><span class="ml-2 uppercase font-semibold">Reports</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="reports-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('reports.daily') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('reports.daily') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Daily
                </a>
                <a href="{{ route('reports.weekly') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('reports.weekly') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Weekly
                </a>
                <a href="{{ route('reports.monthly') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('reports.monthly') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Monthly
                </a>
            </div>
        </div>

        <!-- Customers -->
        <div class="relative">
            <button data-target="customers-submenu" class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-800 transition border-l-4 border-transparent">
                <div class="flex items-center">
                    <span class="icon">🧑‍🤝‍🦶</span><span class="ml-2 uppercase font-semibold">Customers</span>
                </div>
                <div class="icons">
                    <svg class="plus-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <svg class="minus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
            </button>
            <div id="customers-submenu" class="submenu hidden mt-2 ml-6 space-y-1 opacity-0 transition-opacity duration-300">
                <a href="{{ route('customers.index') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('customers.index') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    All Customers
                </a>
                <a href="{{ route('customers.create') }}"
                   class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-transparent {{ Route::is('customers.create') ? 'bg-gray-100 dark:bg-gray-700 border-blue-500' : '' }}">
                    Add Customer
                </a>
            </div>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center w-full px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition text-red-600 dark:text-red-400 border-l-4 border-transparent">
                <span class="icon">🚪</span><span class="ml-2">Logout</span>
            </button>
        </form>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButtons = document.querySelectorAll('.toggle-btn');
        window.expandedSubmenu = null;

        toggleButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const submenu = document.getElementById(targetId);
                const plusIcon = button.querySelector('.plus-icon');
                const minusIcon = button.querySelector('.minus-icon');

                if (window.expandedSubmenu && window.expandedSubmenu !== targetId) {
                    const prevSubmenu = document.getElementById(window.expandedSubmenu);
                    const prevBtn = document.querySelector(`[data-target="${window.expandedSubmenu}"]`);
                    prevSubmenu.classList.add('hidden');
                    prevSubmenu.classList.remove('opacity-100');
                    prevSubmenu.classList.add('opacity-0');
                    prevBtn.querySelector('.plus-icon').classList.remove('hidden');
                    prevBtn.querySelector('.minus-icon').classList.add('hidden');
                }

                submenu.classList.toggle('hidden');
                if (!submenu.classList.contains('hidden')) {
                    submenu.classList.remove('opacity-0');
                    submenu.classList.add('opacity-100');
                    plusIcon.classList.add('hidden');
                    minusIcon.classList.remove('hidden');
                    window.expandedSubmenu = targetId;
                } else {
                    submenu.classList.remove('opacity-100');
                    submenu.classList.add('opacity-0');
                    plusIcon.classList.remove('hidden');
                    minusIcon.classList.add('hidden');
                    window.expandedSubmenu = null;
                }
            });
        });
    });
</script>