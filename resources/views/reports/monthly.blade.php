 
@extends('layouts.app')
@section('content')
<div class="p-6 bg-white dark:bg-gray-800 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Monthly Report</h1>
  <table class="w-full table-auto">
    <thead><tr><th>Month #</th><th>Total Eggs</th></tr></thead>
    <tbody>
      @forelse($data as $row)
        <tr>
          <td>{{ $row->month }}</td>
          <td>{{ $row->total }}</td>
        </tr>
      @empty
        <tr><td colspan="2">No data.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
