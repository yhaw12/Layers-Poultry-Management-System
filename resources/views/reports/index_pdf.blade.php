<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($reportType === 'custom' ? 'analytics' : $reportType) }} Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        h1 { font-size: 2em; margin-bottom: 0.5em; color: #1e40af; }
        h2 { font-size: 1.5em; margin: 1em 0 0.5em; color: #1e40af; }
        h3 { font-size: 1.2em; margin: 0.5em 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5em; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #e5e7eb; font-weight: bold; }
        .header { margin-bottom: 20px; }
        .header p { margin: 0.2em 0; }
        .text-green { color: #15803d; }
        .text-red { color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($reportType === 'custom' ? 'Analytics' : $reportType) }} Report</h4>
        <p>Generated on: {{ now()->format('Y-m-d') }}</p>
        @if ($reportType === 'custom')
            <p>Date Range: {{ $validated['start_date'] }} to {{ $validated['end_date'] }}</p>
            <p>Fields: {{ implode(', ', array_map('ucfirst', $validated['metrics'] ?? [])) }}</p>
        @endif
    </div>

    @if ($reportType === 'weekly')
        <h2>Weekly Egg Report</h2>
        @if (empty($data['weekly']) || $data['weekly']->isEmpty())
            <p>No egg data found for the last 8 weeks.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Week</th>
                        <th>Total Eggs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['weekly'] as $row)
                        <tr>
                            <td>{{ $row->year }}</td>
                            <td>{{ $row->week }}</td>
                            <td>{{ number_format($row->total) }}</td>
                        </tr>
                    </tbody>
                @endtable
            @endif
        @endif

    @if ($reportType === 'monthly')
        <h2>Monthly Egg Report</h2>
        @if (empty($data['monthly']) || $data['monthly']->isEmpty())
            <p>No egg data found for the last 6 months.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Month</th>
                        <th>Total Eggs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['monthly'] as $row)
                        <tr>
                            <td>{{ $row->year }}</td>
                            <td>{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                            <td>{{ number_format($row->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    @if ($reportType === 'custom')
        @if (!empty($data['eggs']))
            <h2>Eggs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date Laid</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['eggs'] as $egg)
                        <tr>
                            <td>{{ $egg->date_laid }}</td>
                            <td>{{ number_format($egg->quantity ?? 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if (!empty($data['sales']))
            <h2>Sales</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['sales'] as $sale)
                        <tr>
                            <td>{{ $sale->sale_date }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A' }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>${{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if (!empty($data['expenses']))
            <h2>Expenses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['expenses'] as $expense)
                        <tr>
                            <td>{{ $expense->date }}</td>
                            <td>{{ $expense->description ?? 'N/A' }}</td>
                            <td>${{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    @if ($reportType === 'profitability')
        <h2>Profitability Report</h2>
        @if (empty($data['profitability']) || $data['profitability']->isEmpty())
            <p>No profitability data found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Bird ID</th>
                        <th>Breed</th>
                        <th>Sales</th>
                        <th>Feed Cost</th>
                        <th>Expenses</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['profitability'] as $row)
                        <tr>
                            <td>{{ $row->bird_id }}</td>
                            <td>{{ $row->breed }}</td>
                            <td>${{ number_format($row->sales, 2) }}</td>
                            <td>${{ number_format($row->feed_cost, 2) }}</td>
                            <td>${{ number_format($row->expenses, 2) }}</td>
                            <td class="{{ $row->profit >= 0 ? 'text-green' : 'text-red' }}">${{ number_format($row->profit, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</body>
</html>