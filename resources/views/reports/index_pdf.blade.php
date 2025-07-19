<!DOCTYPE html>
<html>
<head>
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2, h3 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ ucfirst($type) }} Report</h1>

    @if ($type === 'weekly' && !empty($data['weekly']))
        <h2>Weekly Egg Report</h2>
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
    @elseif ($type === 'monthly' && !empty($data['monthly']))
        <h2>Monthly Egg Report</h2>
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
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($type === 'custom')
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
    @elseif ($type === 'profitability' && !empty($data['profitability']))
        <h2>Profitability Report</h2>
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
                        <td>{{ $row->bird_id }}</td>
                        <td>{{ $row->breed }}</td>
                        <td>{{ number_format($row->sales, 2) }}</td>

                        <td>{{ number_format($row->feed_cost, 2) }}</td>
                        <td>{{ number_format($row->expenses, 2) }}</td>
                        <td>{{ number_format($row->profit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($type === 'profit-loss' && !empty($data['profit_loss']))
        <h2>Profit and Loss Report</h2>
        <p>Period: {{ $data['profit_loss']['start'] }} to {{ $data['profit_loss']['end'] }}</p>
        <table>
            <tr><th>Total Income</th><td>{{ number_format($data['profit_loss']['total_income'], 2) }}</td></tr>
            <tr><th>Total Expenses</th><td>{{ number_format($data['profit_loss']['total_expenses'], 2) }}</td></tr>
            <tr><th>Profit/Loss</th><td>{{ number_format($data['profit_loss']['profit_loss'], 2) }}</td></tr>
        </table>
    @elseif ($type === 'forecast' && !empty($data['forecast']))
        <h2>Financial Forecast</h2>
        <table>
            <tr><th>Forecasted Income</th><td>{{ number_format($data['forecast']['forecasted_income'], 2) }}</td></tr>
            <tr><th>Forecasted Expenses</th><td>{{ number_format($data['forecast']['forecasted_expenses'], 2) }}</td></tr>
        </table>
    @endif
</body>
</html>