<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Poultry Tracker</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

  {{-- Authenticated layout --}}
  @auth
    <div class="flex h-screen">
      {{-- Sidebar --}}
      @include('partials.sidebar')

      {{-- Main Area --}}
      <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top nav --}}
        <header class="bg-white shadow-md">
          <nav class="p-4">
            <div class="container mx-auto flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Dashboard</a>
                <a href="{{ route('expenses.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Expenses</a>
                <a href="{{ route('chicks.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Chicks</a>
                <a href="{{ route('hens.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Hens</a>
                <a href="{{ route('feed.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Feed</a>
                <a href="{{ route('eggs.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Eggs</a>

                @if(auth()->user()->is_admin)
                <a href="{{ route('income.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Income</a>
                @endif

                {{-- Spacer --}}
                <div class="flex-1"></div>

                {{-- User role label --}}
                <div class="text-gray-600 font-medium text-sm flex items-center mr-8 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Logged in as: 
                <span class="text-blue-800 font-semibold">
                    {{ auth()->user()->is_admin ? 'Admin' : 'User' }}
                </span>
                </div>
            </div>
        </header>

        {{-- Flash message --}}
        @if(session('status'))
          <div class="mx-4 mt-4 p-4 bg-blue-100 text-blue-800 rounded shadow">
              {{ session('status') }}
          </div>
        @endif

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto container mx-auto px-4 py-6">
          @yield('content')
        </main>
      </div>
    </div>
  @endauth

  {{-- Guest layout (login/register) --}}
  @guest
    <main class="min-h-screen flex items-center justify-center">
      @yield('content')
    </main>
  @endguest

  {{-- Fallback CDN --}}
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</body>
</html>
