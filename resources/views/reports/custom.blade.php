<!-- resources/views/reports/custom.blade.php -->
<h1>Custom Report ({{ $validated['start_date'] }} to {{ $validated['end_date'] }})</h1>
@if(isset($data['eggs']))
    <h2>Eggs</h2>
    <table>
        <thead>
            <tr><th>Date Laid</th><th>Crates</th><th>Sold</th></tr>
        </thead>
        <tbody>
            @foreach($data['eggs'] as $egg)
                <tr>
                    <td>{{ $egg->date_laid->format('Y-m-d') }}</td>
                    <td>{{ $egg->crates }}</td>
                    <td>{{ $egg->sold_quantity ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
<!-- Similar sections for sales, expenses -->