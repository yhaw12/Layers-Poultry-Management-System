@extends('layouts.app')

@section('title', 'Vaccination Reminders')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Vaccination Reminders</h1>
    @if ($reminders->isEmpty())
        <p class="text-gray-600 dark:text-gray-400">No upcoming vaccination reminders.</p>
    @else
        <ul class="space-y-4">
            @foreach ($reminders as $reminder)
                <li class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <p><strong>Bird:</strong> {{ $reminder->bird ? $reminder->bird->name : 'Unknown' }}</p>
                    <p><strong>Vaccine:</strong> {{ $reminder->vaccine_name }}</p>
                    <p><strong>Next Vaccination:</strong> {{ \Carbon\Carbon::parse($reminder->next_vaccination_date)->format('Y-m-d') }}</p>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection