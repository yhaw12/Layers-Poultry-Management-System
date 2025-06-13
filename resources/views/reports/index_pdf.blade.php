<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($reportType) }} Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 24px;
            color: #007bff;
            margin: 0;
        }
        h2 {
            font-size: 18px;
            margin: 20px 0 10px;
            color: #444;
        }
        p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Replace with your farm logo -->
        <img src="{{ public_path('images/farm_logo.png') }}" alt="Farm Logo">
        <h1>{{ ucfirst($reportType) }} Report</h1>
        <p>Generated on: {{ now()->format('F d, Y') }}</p>
        @if (isset($validated['start_date']))
            <p>Date Range: {{ $validated['start_date'] }} to {{ $validated['end_date'] }}</p>
            <p>Metrics: {{ implode(', ', $validated['metrics']) }}</p>
        @endif
    </div>

    @if ($reportType === 'daily' && !empty($data['daily']))
        <h2>Daily Egg Production</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Eggs</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['daily'] as $row)
                    <tr>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($reportType === 'weekly' && !empty($data['weekly']))
        <h2>Weekly Egg Production</h2>
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
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($reportType === 'monthly' && !empty($data['monthly']))
        <h2>Monthly Egg Production</h2>
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
                        <td>{{ $row->month }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($reportType === 'custom')
        @if (!empty($data['eggs']))
            <h2>Egg Production</h2>
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
                            <td>{{ $egg->quantity ?? 1 }}</td>
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
                        <th>Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['sales'] as $sale)
                        <tr>
                            <td>{{ $sale->sale_date }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A' }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>{{ number_format($sale->total_amount, 2) }}</td>
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
                        <th>Amount ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['expenses'] as $expense)
                        <tr>
                            <td>{{ $expense->date }}</td>
                            <td>{{ $expense->description ?? 'N/A' }}</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    @if ($reportType === 'profitability' && !empty($data['profitability']))
        <h2>Profitability by Bird</h2>
        <table>
            <thead>
                <tr>
                    <th>Bird ID</th>
                    <th>Breed</th>
                    <th>Sales ($)</th>
                    <th>Feed Cost ($)</th>
                    <th>Expenses ($)</th>
                    <th>Profit ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['profitability'] as $row)
                    <tr>
                        <td>{{ $row['bird_id'] }}</td>
                        <td>{{ $row['breed'] }}</td>
                        <td>{{ number_format($row['sales'], 2) }}</td>
                        <td>{{ number_format($row['feed_cost'], 2) }}</td>
                        <td>{{ number_format($row['expenses'], 2) }}</td>
                        <td style="color: {{ $row['profit'] >= 0 ? 'green' : 'red' }}">{{ number_format($row['profit'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Generated by Farm Management System</p>
        <p>&copy; {{ now()->year }} Your Farm Name</p>
    </div>
</body>
</html>