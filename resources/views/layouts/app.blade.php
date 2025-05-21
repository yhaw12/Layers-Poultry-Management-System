<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Poultry Tracker</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

  @auth
  <div class="flex h-screen">
    {{-- Left column: Sidebar --}}
    @include('partials.sidebar')

    {{-- Right column: nav on top + content below --}}
    <div class="flex-1 flex flex-col overflow-hidden">
      
      {{-- Top nav --}}
      <header class="bg-white shadow-md">
        <nav class="p-4">
          <div class="container mx-auto flex flex-wrap gap-6">
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Dashboard</a>
            <a href="{{ route('expenses.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Expenses</a>
            <a href="{{ route('chicks.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Chicks</a>
            <a href="{{ route('hens.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Hens</a>
            <a href="{{ route('feed.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Feed</a>
            <a href="{{ route('eggs.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Eggs</a>
            <a href="{{ route('income.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Income</a>
            <a href="{{ route('logout') }}" class="text-red-600 hover:text-red-800 font-semibold">Logout</a>
          </div>
        </nav>
      </header>
      
      {{-- Main dashboard/content area --}}
      <main class="flex-1 overflow-y-auto container mx-auto px-4 py-6">
        @yield('content')
      </main>
    </div>
  </div>
  @endauth

  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</body>
</html>
