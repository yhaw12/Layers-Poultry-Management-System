<aside class="w-64 h-screen bg-white dark:bg-gray-800 shadow-md">
  <nav class="p-4 space-y-6 text-sm font-medium text-gray-700 dark:text-gray-200">
    
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
       class="flex items-center px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
      <span class="icon">🏠</span>
      <span class="ml-2">Dashboard</span>
    </a>

    <!-- Eggs Section -->
    <div class="relative">
      <button data-target="eggs-submenu"
              class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
              aria-expanded="false">
        <div class="flex items-center">
          <span class="icon">🥚</span>
          <span class="ml-2 uppercase font-semibold">Eggs</span>
        </div>
        <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
        <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
      </button>
      <div id="eggs-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
        <a href="{{ route('eggs.index') }}"
           class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
          Production
        </a>
        <a href="{{ route('sales.index') }}"
           class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
          Sales
        </a>
      </div>
    </div>

    <!-- Birds Section -->
<div class="relative">
  <button data-target="birds-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">🐓</span>
      <span class="ml-2 uppercase font-semibold">Birds</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="birds-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">All Birds</a>
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Add Batch</a>
  </div>
</div> 

<!-- Feed Section -->
<div class="relative">
  <button data-target="feed-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">🌾</span>
      <span class="ml-2 uppercase font-semibold">Feed</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="feed-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="{{ route('feed.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Stock</a>
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Usage</a>
  </div>
</div>

<!-- Medicine Section -->
<div class="relative">
  <button data-target="medicine-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">💊</span>
      <span class="ml-2 uppercase font-semibold">Medicine</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="medicine-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Inventory</a>
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Log</a>
  </div>
</div>

<!-- Inventory Section -->
<div class="relative">
  <button data-target="inventory-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">📦</span>
      <span class="ml-2 uppercase font-semibold">Inventory</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="inventory-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Items</a>
  </div>
</div>

<!-- Expenses Section -->
<div class="relative">
  <button data-target="expenses-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">💰</span>
      <span class="ml-2 uppercase font-semibold">Expenses</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="expenses-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="{{ route('expenses.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">All</a>
    <a href="{{ route('expenses.create') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Add</a>
  </div>
</div>

<!-- Vet Logs Section -->
<div class="relative">
  <button data-target="vet-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">🩺</span>
      <span class="ml-2 uppercase font-semibold">Vet Logs</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="vet-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Records</a>
  </div>
</div>

<!-- Reports Section -->
<div class="relative">
  <button data-target="reports-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">📊</span>
      <span class="ml-2 uppercase font-semibold">Reports</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="reports-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Daily</a>
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Weekly</a>
    <a href="" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">Monthly</a>
  </div>
</div>

<!-- Customers Section -->
<div class="relative">
  <button data-target="customers-submenu"
          class="toggle-btn flex items-center justify-between w-full px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          aria-expanded="false">
    <div class="flex items-center">
      <span class="icon">🧑‍🤝‍🧑</span>
      <span class="ml-2 uppercase font-semibold">Customers</span>
    </div>
    <svg class="w-4 h-4 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
    <svg class="w-4 h-4 minus-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
  <div id="customers-submenu" class="submenu hidden mt-2 ml-6 space-y-1">
    <a href="{{ route('customers.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">All Customers</a>
  </div>
</div>


    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}" class="mt-6">
      @csrf
      <button type="submit"
              class="flex items-center w-full px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 transition text-red-600 dark:text-red-400">
        <span class="icon">🚪</span>
        <span class="ml-2">Logout</span>
      </button>
    </form>

  </nav>
</aside>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    let openSub = null;

    document.querySelectorAll('.toggle-btn').forEach(toggle => {
      toggle.addEventListener('click', () => {
        const targetId = toggle.dataset.target;
        const submenu = document.getElementById(targetId);
        const plus = toggle.querySelector('.plus-icon');
        const minus = toggle.querySelector('.minus-icon');

        if (openSub === targetId) {
          submenu.classList.add('hidden');
          plus.classList.remove('hidden');
          minus.classList.add('hidden');
          toggle.setAttribute('aria-expanded', 'false');
          openSub = null;
        } else {
          if (openSub) {
            const prevToggle = document.querySelector(`[data-target="${openSub}"]`);
            const prevSubmenu = document.getElementById(openSub);
            prevSubmenu.classList.add('hidden');
            prevToggle.querySelector('.plus-icon').classList.remove('hidden');
            prevToggle.querySelector('.minus-icon').classList.add('hidden');
            prevToggle.setAttribute('aria-expanded', 'false');
          }
          submenu.classList.remove('hidden');
          plus.classList.add('hidden');
          minus.classList.remove('hidden');
          toggle.setAttribute('aria-expanded', 'true');
          openSub = targetId;
        }
      });
    });
  });
</script>
