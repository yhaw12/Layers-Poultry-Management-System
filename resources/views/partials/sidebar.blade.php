<!-- resources/views/partials/sidebar.blade.php -->
<aside class="w-64 bg-white shadow-md h-screen sticky top-0">
  <div class="flex items-center justify-between px-6 py-4 border-b">
    <div class="font-bold text-xl">EPMS</div>
    <button onclick="this.closest('aside').classList.toggle('hidden')" aria-label="Close sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 hover:text-gray-800" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 011.414 
                 1.414L11.414 10l4.293 4.293a1 1 0 
                 01-1.414 1.414L10 11.414l-4.293 
                 4.293a1 1 0-1.414-1.414L8.586 10 
                 4.293 5.707a1 1 0-1.414-1.414z"
              clip-rule="evenodd" />
      </svg>
    </button>
  </div>

  <nav class="px-2 py-4 overflow-y-auto">
    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg bg-gray-200 font-semibold text-gray-900">
      <i class="fa fa-home w-5 h-5"></i>
      <span>Dashboard</span>
    </a>

    {{-- Expenses section --}}
    <h2 class="text-gray-400 text-xs font-semibold mt-4 mb-2 uppercase">Expenses</h2>
    <a href="{{ route('expenses.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-product-hunt w-5 h-5"></i>
      <span>All Expenses</span>
    </a>
    <a href="{{ route('expenses.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-plus w-5 h-5"></i>
      <span>Add Expense</span>
    </a>

    {{-- Chicks --}}
    <h2 class="text-gray-400 text-xs font-semibold mt-4 mb-2 uppercase">Chicks</h2>
    <a href="{{ route('chicks.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-shopping-cart w-5 h-5"></i>
      <span>All Chicks</span>
    </a>
    <a href="{{ route('chicks.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-plus w-5 h-5"></i>
      <span>Add Chick</span>
    </a>

    {{-- Hens --}}
    <h2 class="text-gray-400 text-xs font-semibold mt-4 mb-2 uppercase">Hens</h2>
    <a href="{{ route('hens.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-shopping-cart w-5 h-5"></i>
      <span>All Hens</span>
    </a>
    <a href="{{ route('hens.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-plus w-5 h-5"></i>
      <span>Add Hen</span>
    </a>

    {{-- Feed --}}
    <h2 class="text-gray-400 text-xs font-semibold mt-4 mb-2 uppercase">Feed</h2>
    <a href="{{ route('feed.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-cutlery w-5 h-5"></i>
      <span>All Feed</span>
    </a>
    <a href="{{ route('feed.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-plus w-5 h-5"></i>
      <span>Add Feed</span>
    </a>

    {{-- Eggs --}}
    <h2 class="text-gray-400 text-xs font-semibold mt-4 mb-2 uppercase">Eggs</h2>
    <a href="{{ route('eggs.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-egg w-5 h-5"></i>
      <span>All Eggs</span>
    </a>
    <a href="{{ route('eggs.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-plus w-5 h-5"></i>
      <span>Add Egg</span>
    </a>

    {{-- Income --}}
    <h2 class="text-gray-400 text-xs font-semibold mt-4 mb-2 uppercase">Income</h2>
    <a href="{{ route('income.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-dollar-sign w-5 h-5"></i>
      <span>All Income</span>
    </a>
    <a href="{{ route('income.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700">
      <i class="fa fa-plus w-5 h-5"></i>
      <span>Add Income</span>
    </a>

    {{-- Logout --}}
    <div class="mt-6">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-200 text-gray-700 w-full text-left">
          <i class="fa fa-power-off w-5 h-5"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </nav>
</aside>
