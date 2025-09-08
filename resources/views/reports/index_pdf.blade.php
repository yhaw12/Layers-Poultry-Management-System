<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Report - {{ strtoupper($type ?? 'report') }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; font-size: 12px; }
        header { margin-bottom: 12px; }
        h1 { font-size: 18px; margin: 0 0 8px 0; }
        .meta { font-size: 11px; color: #555; margin-bottom: 16px; }
        .summary { margin-bottom: 14px; }
        .summary .item { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #dcdcdc; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #f4f4f4; }
        .chart { margin: 10px 0; text-align: center; page-break-inside: avoid; }
        .chart img { max-width: 100%; height: auto; }
        .section { margin-top: 18px; page-break-inside: avoid; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <header>
        <h1>Report: {{ ucfirst($type ?? 'report') }}</h1>
        <div class="meta">
            Generated: {{ now()->format('Y-m-d H:i') }}
            @if(isset($data['profit_loss']['start']))
                — Period: {{ $data['profit_loss']['start'] }} to {{ $data['profit_loss']['end'] }}
            @endif
        </div>
    </header>

    {{-- Optional KPI Summary --}}
    @if(!empty($includeSummary) || !empty($data['profit_loss']))
        <div class="summary section">
            <h2 style="font-size:14px;margin-bottom:6px;">Summary</h2>
            <div class="item"><strong>Total Income:</strong> ₵ {{ number_format($data['profit_loss']['total_income'] ?? 0, 2) }}</div>
            <div class="item"><strong>Total Expenses:</strong> ₵ {{ number_format(($data['profit_loss']['total_expenses'] ?? 0) + ($data['profit_loss']['total_payroll'] ?? 0), 2) }}</div>
            <div class="item"><strong>Profit / Loss:</strong> ₵ {{ number_format($data['profit_loss']['profit_loss'] ?? 0, 2) }}</div>
        </div>
    @endif

    {{-- Charts (images are base64 data URIs) --}}
    @if(!empty($includeChart) && !empty($data['chart_images']))
        <div class="section">
            <h2 style="font-size:14px;margin-bottom:6px;">Charts</h2>
            @foreach($data['chart_images'] as $ci)
                <div class="chart">
                    <div style="font-weight:600;margin-bottom:6px;">{{ $ci['title'] ?? 'Chart' }}</div>
                    <img src="{{ $ci['image'] }}" alt="{{ $ci['title'] ?? 'chart' }}" />
                </div>
            @endforeach
        </div>
    @endif

    {{-- Small tables depending on type --}}
    <div class="section">
        @if($type === 'weekly' && !empty($data['weekly']))
            <h2 style="font-size:14px;margin-bottom:6px;">Weekly Egg Production</h2>
            <table>
                <thead>
                    <tr><th>Year</th><th>Week</th><th>Total Crates</th></tr>
                </thead>
                <tbody>
                    @foreach($data['weekly'] as $r)
                        <tr>
                            <td>{{ $r->year }}</td>
                            <td>{{ $r->week }}</td>
                            <td>{{ number_format($r->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($type === 'monthly' && !empty($data['monthly']))
            <h2 style="font-size:14px;margin-top:12px;margin-bottom:6px;">Monthly Egg Production</h2>
            <table>
                <thead><tr><th>Year</th><th>Month</th><th>Total Crates</th></tr></thead>
                <tbody>
                @foreach($data['monthly'] as $r)
                    <tr>
                        <td>{{ $r->year }}</td>
                        <td>{{ \Carbon\Carbon::createFromDate($r->year, $r->month_num, 1)->format('F Y') }}</td>
                        <td>{{ number_format($r->total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if($type === 'custom')
            <h2 style="font-size:14px;margin-top:12px;margin-bottom:6px;">Custom Metrics</h2>

            @if(!empty($data['eggs']) && $data['eggs']->count())
                <h3 style="font-size:12px;margin-bottom:4px;">Eggs</h3>
                <table>
                    <thead><tr><th>Date Laid</th><th>Crates</th></tr></thead>
                    <tbody>
                        @foreach($data['eggs'] as $e)
                            <tr><td>{{ $e->date_laid }}</td><td>{{ number_format($e->crates, 2) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(!empty($data['sales']) && $data['sales']->count())
                <h3 style="font-size:12px;margin-top:8px;margin-bottom:4px;">Sales</h3>
                <table>
                    <thead><tr><th>Date</th><th>Customer</th><th>Product</th><th>Qty</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($data['sales'] as $s)
                            <tr>
                                <td>{{ $s->sale_date }}</td>
                                <td>{{ optional($s->customer)->name ?? 'N/A' }}</td>
                                <td>
                                    @if($s->saleable_type === \App\Models\Bird::class)
                                        {{ optional($s->saleable)->breed ?? 'Bird' }}
                                    @else
                                        Egg Batch #{{ $s->saleable_id }}
                                    @endif
                                </td>
                                <td>{{ $s->quantity }}</td>
                                <td>₵ {{ number_format($s->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif

        @if($type === 'profitability' && !empty($data['profitability']))
            <h2 style="font-size:14px;margin-top:12px;margin-bottom:6px;">Profitability by Bird</h2>
            <table>
                <thead><tr><th>ID</th><th>Breed</th><th>Type</th><th>Sales</th><th>Feed Cost</th><th>Op Cost</th><th>Profit</th></tr></thead>
                <tbody>
                    @foreach($data['profitability'] as $r)
                        <tr>
                            <td>{{ $r->bird_id }}</td>
                            <td>{{ $r->breed }}</td>
                            <td>{{ $r->type }}</td>
                            <td>₵ {{ number_format($r->sales, 2) }}</td>
                            <td>₵ {{ number_format($r->feed_cost, 2) }}</td>
                            <td>₵ {{ number_format($r->operational_cost ?? 0, 2) }}</td>
                            <td>₵ {{ number_format($r->profit, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <footer style="position:fixed; bottom:8px; left:16px; right:16px; text-align:center; color:#888; font-size:10px;">
        Generated by Farm App — {{ now()->format('Y') }}
    </footer>
</body>
</html>
