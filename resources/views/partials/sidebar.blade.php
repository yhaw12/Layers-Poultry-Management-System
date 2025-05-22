{{-- <aside class="w-64 bg-white shadow-md h-screen sticky top-0 overflow-y-auto">
  <nav class="p-4 space-y-2 text-sm font-medium text-gray-700">

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
       class="block px-4 py-2 rounded hover:bg-gray-200">
      🏠 Dashboard
    </a>

    <!-- EGGS Section -->
    <h2 class="mt-6 mb-2 text-xs font-semibold uppercase text-gray-500">Eggs</h2>
    <a href="{{ route('eggs.index') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      📦 Production
    </a>
    {{-- Assuming you have a route for sales --}}
    {{-- <a href="{{ route('egg.sales') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      💲 Sales
    </a> --}}

    <!-- BIRDS Section -->
    {{-- <h2 class="mt-6 mb-2 text-xs font-semibold uppercase text-gray-500">Birds</h2>
    <a href="{{ route('chicks.index') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      🐣 Purchase
    </a>
    <a href="{{ route('deaths.index') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      ☠️ Mortality
    </a> --}}

    <!-- FEED Section -->
    {{-- <h2 class="mt-6 mb-2 text-xs font-semibold uppercase text-gray-500">Feed</h2>
    <a href="{{ route('feed.index') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      🛒 Purchase
    </a> --}}
    {{-- Assuming you track feed consumption --}}
    {{-- <a href="{{ route('feed.consumption') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      🍽️ Consumption
    </a> --}}

    <!-- PAYROLL Section -->
    {{-- <h2 class="mt-6 mb-2 text-xs font-semibold uppercase text-gray-500">Payroll</h2>
    <a href="{{ route('employees.index') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      👷 Employees
    </a>
    <a href="{{ route('payroll.index') }}"
       class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
      💼 Payroll
    </a> --}}

    <!-- Payments (Admins only) -->
    {{-- @if(auth()->user()->isAdmin())
      <h2 class="mt-6 mb-2 text-xs font-semibold uppercase text-gray-500">Finance</h2>
      <a href="{{ route('expenses.index') }}"
         class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
        💸 Expenses
      </a>
      <a href="{{ route('income.index') }}"
         class="block px-4 py-2 pl-6 rounded hover:bg-gray-100">
        💰 Income
      </a> --}}
    {{-- @endif --}}

    <!-- Logout -->
    {{-- <form method="POST" action="{{ route('logout') }}" class="mt-8">
      @csrf
      <button type="submit"
              class="w-full text-left px-4 py-2 rounded hover:bg-gray-200">
        🔌 Logout
      </button>
    </form>

  </nav>
</aside>  --}}