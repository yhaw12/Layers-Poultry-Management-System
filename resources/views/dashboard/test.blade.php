{{-- document.addEventListener('DOMContentLoaded', function () {
            const alerts = @json($alerts->items());
            const alertCard = document.getElementById('alert-card');
            const alertMessage = document.getElementById('alert-message');
            const alertDate = document.getElementById('alert-date');
            const markReadBtn = document.getElementById('mark-read-btn');
            const alertCount = document.getElementById('alert-count');
            let currentAlertIndex = 0;

            function displayAlert(index) {
                if (!alerts.length || !alertCard) return;
                const alert = alerts[index];
                alertMessage.textContent = alert.message;
                alertDate.textContent = new Date(alert.created_at).toLocaleDateString('en-US', {
                    month: 'short', day: 'numeric', year: 'numeric'
                });
                markReadBtn.dataset.id = alert.id;
                alertCard.classList.remove('animate-slide-in');
                void alertCard.offsetWidth; // Trigger reflow
                alertCard.classList.add('animate-slide-in');
            }

            if (alerts.length && alertCard) {
                displayAlert(currentAlertIndex);
                setInterval(() => {
                    currentAlertIndex = (currentAlertIndex + 1) % alerts.length;
                    displayAlert(currentAlertIndex);
                }, 5000); // Cycle every 5 seconds
            }

            if (markReadBtn) {
                markReadBtn.addEventListener('click', function () {
                    const alertId = this.dataset.id;
                    fetch('/alerts/mark-read/' + alertId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alerts.splice(currentAlertIndex, 1);
                            alertCount.textContent = `${alerts.length} unread`;
                            if (alerts.length) {
                                currentAlertIndex = currentAlertIndex % alerts.length;
                                displayAlert(currentAlertIndex);
                            } else {
                                alertCard.innerHTML = '<p class="text-sm text-gray-600 dark:text-gray-400">No new alerts.</p>';
                            }
                        }
                    })
                    .catch(error => console.error('Error marking alert as read:', error));
                });
            }
        });

        
        document.addEventListener("DOMContentLoaded", function () {
            let index = 0;
            const reminders = document.querySelectorAll("#reminder-rotator [data-reminder]");
            const dots = document.querySelectorAll(".reminder-dot");
            const total = reminders.length;

            function showReminder(i) {
                reminders.forEach((el, j) => {
                    el.classList.toggle("opacity-100", j === i);
                    el.classList.toggle("opacity-0", j !== i);
                });
                dots.forEach((dot, j) => {
                    dot.classList.toggle("bg-blue-600", j === i);
                    dot.classList.toggle("bg-gray-400", j !== i);
                });
            }

            function nextReminder() {
                index = (index + 1) % total;
                showReminder(index);
            }

            // Auto-rotate every 5s
            let interval = setInterval(nextReminder, 5000);

            // Allow manual navigation
            dots.forEach(dot => {
                dot.addEventListener("click", () => {
                    index = parseInt(dot.dataset.index);
                    showReminder(index);
                    clearInterval(interval);
                    interval = setInterval(nextReminder, 5000);
                });
            });
        });

        const apiKey = @json($openWeatherApiKey ?? '');
  console.log('OpenWeather API Key:', apiKey ? apiKey.slice(0, 4) + '...' : 'Missing');

  // Clear localStorage for debugging
  localStorage.removeItem('weatherData');

  // Fetch weather
  function fetchWeather() {
    
    if (!apiKey) {
      console.error('OpenWeather API key is missing');
      weatherWidget.innerHTML = '<p class="text-red-600 dark:text-red-400 text-sm mt-2">Weather API key is missing.</p>';
      return;
    }
    const location = 'Accra,GH';
    console.log('Fetching weather for:', location);

    // Inside fetchWeather, before the fetch call, add:
const hardcodedData = {
  cod: 200,
  main: { temp: 24.23 },
  weather: [{ description: "clear sky" }],
  name: "Accra"
};
const weatherData = {
  temperature: Math.round(hardcodedData.main.temp),
  condition: hardcodedData.weather[0].description,
  location: hardcodedData.name + ', GH'
};
console.log('Using hardcoded weather data:', weatherData);
updateUI(weatherData, false);
return; // Skip actual fetch
    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${location}&units=metric&appid=${apiKey}`)
      .then(response => {
        console.log('Weather API response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Weather API data:', data);
        if (data.cod === 200) {
          const weatherData = {
            temperature: Math.round(data.main.temp),
            condition: data.weather[0].description,
            location: data.name + ', GH'
          };
          console.log('Processed weather data:', weatherData);
          updateUI(weatherData, false);
          localStorage.setItem('weatherData', JSON.stringify(weatherData));
        } else {
          console.error('Weather API error:', data.message);
          weatherWidget.innerHTML = '<p class="text-red-600 dark:text-red-400 text-sm mt-2">Failed to fetch weather: ' + data.message + '</p>';
        }
      })
      .catch(error => {
        console.error('Weather API fetch error:', error);
        weatherWidget.innerHTML = '<p class="text-red-600 dark:text-red-400 text-sm mt-2">Failed to fetch weather: ' + error.message + '</p>';
      });
  }

  // Update UI with animation
  function updateUI(weatherData, isFromCache) {
    if (!tempElement || !conditionElement || !locationElement) {
      console.error('Weather UI elements missing during update');
      return;
    }
    console.log('Updating weather UI:', weatherData, 'Cached:', isFromCache);
    animateNumber(tempElement, weatherData.temperature);
    conditionElement.textContent = weatherData.condition;
    locationElement.textContent = weatherData.location;
    if (!isFromCache) {
      conditionElement.classList.add('animate-fade-in-delay');
      locationElement.classList.add('animate-fade-in-delay-2');
      setTimeout(() => {
        conditionElement.classList.remove('animate-fade-in-delay');
        locationElement.classList.remove('animate-fade-in-delay-2');
      }, 800);
    }
  }

  // Animate number for temperature
  function animateNumber(el, target) {
    let current = parseInt(el.textContent) || 0;
    const step = target > current ? 1 : -1;
    const interval = setInterval(() => {
      current += step;
      el.textContent = current + '°C';
      if (current === target) clearInterval(interval);
    }, 40);
  }

  // Fetch weather on load if online
  console.log('Navigator online:', navigator.onLine);
  if (navigator.onLine) {
    fetchWeather();
  } else {
    console.warn('Browser is offline, no initial weather fetch');
    weatherWidget.innerHTML = '<p class="text-red-600 dark:text-red-400 text-sm mt-2">Offline: Please connect to the internet.</p>';
  }

  // Auto-refresh every 30 minutes if online
  setInterval(() => {
    if (navigator.onLine) {
      console.log('Periodic weather refresh triggered');
      fetchWeather();
    }
  }, 1800000);

  // Listen for online event
  window.addEventListener('online', () => {
    console.log('Browser went online, fetching weather...');
    fetchWeather();
  });
});

// calender reminder --}}


<!-- Alert Calendar Card -->
        {{-- <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 w-full max-w-md mx-auto">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-2">🔔 Reminders</h2>

            <!-- Reminder Container -->
            <div id="reminder-rotator" class="relative overflow-hidden h-32">
                @foreach($reminders as $i => $reminder)
                    <div class="absolute inset-0 transition-opacity duration-700 ease-in-out 
                                {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
                        data-reminder="{{ $i }}">
                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $reminder->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $reminder->message }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                Due: {{ \Carbon\Carbon::parse($reminder->due_date)->toFormattedDateString() }}
                            </p>
                            <span class="inline-block mt-2 px-2 py-1 text-xs font-bold rounded-full
                                @if($reminder->severity === 'critical') bg-red-100 text-red-700
                                @elseif($reminder->severity === 'warning') bg-yellow-100 text-yellow-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ strtoupper($reminder->severity) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Navigation dots -->
            <div class="flex justify-center mt-3 space-x-2">
                @foreach($reminders as $i => $reminder)
                    <button class="w-2.5 h-2.5 rounded-full reminder-dot {{ $i === 0 ? 'bg-blue-600' : 'bg-gray-400' }}" 
                            data-index="{{ $i }}"></button>
                @endforeach
            </div>
        </div> --}}

        <!-- Pending Approvals (Admins or Finance Managers) -->
        {{-- @can('manage_finances')
            <section class="mb-8">
                <div class="container-box bg-gradient-to-r from-yellow-100 to-gray-50 dark:from-yellow-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl animate-fade-slide-up">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pending Approvals
                        </h2>
                        <svg class="w-8 h-8 text-yellow-500 dark:text-yellow-400 approval-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div id="pending-approvals">
                        @if (isset($pendingApprovals) && $pendingApprovals->isNotEmpty())
                            <ul class="space-y-3">
                                @foreach ($pendingApprovals->take(3) as $approval)
                                    <li class="transition-all duration-300">
                                        <div class="flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900">
                                            <div>
                                                <p class="text-base text-gray-600 dark:text-gray-400">{{ $approval->date }}</p>
                                                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                                    ₵{{ number_format($approval->amount, 2) }}
                                                    <span class="text-sm ml-2 px-2 py-1 rounded-full {{ $approval->type === 'expense' ? 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' : 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' }}">
                                                        {{ ucfirst($approval->type) }}
                                                    </span>
                                                </p>
                                                <p class="text-base text-gray-600 dark:text-gray-300">{{ Str::limit($approval->description, 40) }}</p>
                                            </div>
                                            <div class="flex space-x-3">
                                                <a href="{{ route('transactions.approve', $approval->id) }}" class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105">Approve</a>
                                                <a href="{{ route('transactions.reject', $approval->id) }}" class="bg-red-600 text-white py-1 px-3 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-sm font-medium transition duration-200 transform hover:scale-105">Reject</a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($pendingApprovals->count() > 3)
                                <div class="mt-4 text-right">
                                    <a href="{{ route('transactions.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline transition duration-200">View All →</a>
                                </div>
                            @endif
                        @else
                            <p class="text-base text-gray-600 dark:text-gray-400 text-center py-4">No pending approvals.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endcan

        <!-- Payroll Status -->
        <section class="mb-8">
            <div class="container-box bg-gradient-to-r from-blue-100 to-gray-50 dark:from-blue-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow hover:shadow-xl animate-fade-slide-up">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Payroll Status
                    </h2>
                    <svg class="w-8 h-8 text-blue-500 dark:text-blue-400 payroll-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div id="payroll-status">
                    @if (isset($payrollStatus) && $payrollStatus->isNotEmpty())
                        @php
                            $latest = $payrollStatus->first();
                        @endphp
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900">
                                <p class="text-base text-gray-600 dark:text-gray-400">Latest Pay Date</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $latest->date }}</p>
                            </div>
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900">
                                <p class="text-base text-gray-600 dark:text-gray-400">Employees</p>
                                <p class="text-2xl font-bold">{{ $latest->employees }}</p>
                            </div>
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900">
                                <p class="text-base text-gray-600 dark:text-gray-400">Total Paid</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">₵{{ number_format($latest->total, 2) }}</p>
                            </div>
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300">
                                <p class="text-base text-gray-600 dark:text-gray-400 mb-1">Status</p>
                                <span class="px-2 py-1 text-sm font-medium rounded-full {{ $latest->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' }}">
                                    {{ ucfirst($latest->status) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-base text-gray-600 dark:text-gray-400 text-center py-4">No payroll activity yet.</p>
                    @endif
                    <div class="mt-4 text-right">
                        <a href="{{ route('payroll.index') }}" class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105">View All</a>
                    </div>
                </div>
            </div>
        </section> --}}