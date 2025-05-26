 
@extends('layouts.app')
@section('content')
<div class="p-6 bg-white dark:bg-gray-800 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Egg Sales</h1>
  <table class="w-full table-auto">
    <thead><tr><th>Date</th><th>Sold</th></tr></thead>
    <tbody>
      @forelse($sales as $s)
      <tr>
        <td>{{ $s->date }}</td>
        <td>{{ $s->sold }}</td>
      </tr>
      @empty
      <tr><td colspan="2">No sales yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
