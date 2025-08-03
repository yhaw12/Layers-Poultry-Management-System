@extends('layouts.app')

   @section('content')
   <div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
       <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Egg Record Details</h2>
       <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
           <p><strong>Date Laid:</strong> {{ $egg->date_laid->format('Y-m-d') }}</p>
           <p><strong>Pen:</strong> {{ $egg->pen->name ?? 'N/A' }}</p>
           <p><strong>Crates:</strong> {{ number_format($egg->crates, 2) }}</p>
           <p><strong>Small Eggs:</strong> {{ $egg->small_eggs }}</p>
           <p><strong>Medium Eggs:</strong> {{ $egg->medium_eggs }}</p>
           <p><strong>Large Eggs:</strong> {{ $egg->large_eggs }}</p>
           <p><strong>Cracked Eggs:</strong> {{ $egg->cracked_eggs }}</p>
           <p><strong>Collected By:</strong> {{ $egg->collectedBy->name ?? 'N/A' }}</p>
           <a href="{{ route('eggs.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Back to List</a>
       </div>
   </div>
   @endsection
   