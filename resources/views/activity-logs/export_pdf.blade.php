<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Activity Logs Export</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#222; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align:left; vertical-align: top; }
        th { background:#f4f4f4; font-weight:700; }
        .small { font-size: 11px; color: #666; }
        .header { margin-bottom: 12px; }
        .filters { margin-bottom: 10px; font-size: 12px; }
        pre { white-space: pre-wrap; word-wrap: break-word; font-family: monospace; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Activity Logs</h2>
        @if(!empty($filters))
            <div class="filters small">
                <strong>Filters:</strong>
                @foreach($filters as $k => $v)
                    @if($v)
                        <span>{{ ucfirst(str_replace('_',' ',$k)) }}: {{ $v }}</span>&nbsp;&nbsp;
                    @endif
                @endforeach
            </div>
        @endif
        <div class="small">Generated: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 120px;">Date</th>
                <th style="width: 160px;">User</th>
                <th style="width: 120px;">Action</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $it)
                <tr>
                    <td>{{ $it->created_at ? $it->created_at->format('Y-m-d H:i:s') : '' }}</td>
                    <td>{{ $it->user ? $it->user->name . ' (' . $it->user->email . ')' : 'System' }}</td>
                    <td>{{ $it->action }}</td>
                    <td><pre>{{ $it->details }}</pre></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
