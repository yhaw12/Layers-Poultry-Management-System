{{-- invoice_fragment.blade.php --}}
@php
    // Determine cashier: prefer relation, fallback to created_by user
    $cashierUser = $sale->relationLoaded('cashier') ? $sale->cashier : ($sale->cashier ?? null);

    if (empty($cashierUser) && !empty($sale->created_by)) {
        // avoid eager-loading issues; this is a simple fallback lookup
        $cashierUser = \App\Models\User::find($sale->created_by);
    }

    // Company placeholder - ensure controller passes $company or adjust accordingly
    $company = $company ?? [
        'name' => config('app.name', 'Company'),
        'address' => config('app.address', ''),
        'phone' => config('app.phone', ''),
        'email' => config('app.email', ''),
    ];
@endphp

<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <div class="company-logo">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ asset('logo.png') }}" alt="Company Logo" style="max-height:60px;">
            @else
                <h2 style="margin:0;color:#007bff;">{{ $company['name'] }}</h2>
            @endif
        </div>
        <div class="invoice-info">
            <h1>INVOICE</h1>
            <p><strong>Invoice #:</strong> {{ $sale->id }}</p>
            <p><strong>Issue Date:</strong> {{ optional($sale->sale_date)->format('F j, Y') ?? 'N/A' }}</p>
            <p><strong>Due Date:</strong> {{ optional($sale->due_date)->format('F j, Y') ?? 'N/A' }}</p>
            <p><strong>Status:</strong>
                <span class="status {{ $sale->status ?? '' }}" style="padding:3px 8px;border-radius:4px;color:#fff;font-size:12px;
                    background:
                        {{ $sale->status === 'paid' ? '#28a745' : ($sale->status === 'overdue' ? '#dc3545' : '#ffc107') }};
                    ">
                    {{ ucfirst(str_replace('_',' ',$sale->status ?? '')) }}
                </span>
            </p>
        </div>
    </div>

    <!-- Customer & Cashier Info -->
    <div class="customer-info">
        <div>
            <h3>Bill To:</h3>
            <p style="margin:0;font-weight:600;">{{ $sale->customer->name ?? 'Unknown Customer' }}</p>
            <p style="margin:0;color:#555;">✉ {{ $sale->customer->email ?? 'N/A' }}</p>
            @if(!empty($sale->customer->phone))
                <p style="margin:0;color:#555;">📞 {{ $sale->customer->phone }}</p>
            @endif
        </div>

        <div style="text-align:right;">
            <h3>Cashier</h3>
            <p style="margin:0;font-weight:600;">
                {{ $cashierUser->name ?? (optional($sale->creator)->name ?? '—') }}
            </p>
            @if(!empty($cashierUser->email))
                <p style="margin:0;color:#555;">✉ {{ $cashierUser->email }}</p>
            @endif
            @if(!empty($cashierUser->phone))
                <p style="margin:0;color:#555;">📞 {{ $cashierUser->phone }}</p>
            @endif
        </div>
    </div>

    <!-- Products Table -->
    <table class="items-table" aria-describedby="invoice-items">
        <thead>
            <tr>
                <th>Product</th>
                <th>Variant</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($sale->saleable_type && $sale->saleable)
                        @if($sale->saleable_type === \App\Models\Bird::class || str_contains($sale->saleable_type, 'Bird'))
                            {{ $sale->saleable->breed ?? 'Bird' }} {{ $sale->saleable->type ? '('.$sale->saleable->type.')' : '' }}
                        @else
                            {{ $sale->saleable->name ?? 'Product' }}
                        @endif
                    @else
                        {{ $sale->product_name ?? 'Item' }}
                    @endif
                </td>
                <td>{{ ucfirst($sale->product_variant ?? 'N/A') }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>₵ {{ number_format($sale->unit_price, 2) }}</td>
                <td>₵ {{ number_format($sale->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Payment history removed from this invoice view.
         Payment and transaction auditing should live in the payments/transactions index pages. --}}

    <!-- Totals -->
    <div class="totals" role="status" aria-live="polite">
        <div><strong>Total Amount:</strong> ₵ {{ number_format($sale->total_amount,2) }}</div>
        <div><strong>Paid Amount:</strong> ₵ {{ number_format($sale->paid_amount ?? 0,2) }}</div>
        <div><strong>Balance Due:</strong>
            <span class="balance" style="color:{{ ($sale->total_amount - ($sale->paid_amount ?? 0)) > 0 ? '#dc3545' : '#28a745' }}; font-weight:700;">
                ₵ {{ number_format(max(0, ($sale->total_amount ?? 0) - ($sale->paid_amount ?? 0)), 2) }}
            </span>
        </div>
    </div>

    <!-- Footer -->
    <div class="company-info">
        <strong>{{ $company['name'] }}</strong><br>
        {{ $company['address'] }}<br>
        {{ $company['phone'] }} — {{ $company['email'] }}<br>
        <em>Payment Terms: Net 7 days</em>
    </div>
</div>

<style>
    .invoice-container {
        max-width: 850px;
        margin: 0 auto;
        padding: 25px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        font-family: 'Segoe UI', Tahoma, sans-serif;
        color: #333;
    }

    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        border-bottom: 2px solid #4A90E2;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }
    .header img { max-height: 60px; }
    .invoice-info h1 {
        margin: 0;
        font-size: 24px;
        color: #4A90E2;
    }
    .invoice-info p { margin: 3px 0; font-size: 14px; }

    /* Status Badge */
    .status {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        color: #fff;
    }

    /* Customer */
    .customer-info { margin-bottom: 20px; }
    .customer-info h3 { margin: 0 0 8px; color: #4A90E2; }

    /* Tables */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 18px;
    }
    .items-table th {
        background: #f4f6fa;
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }
    .items-table td {
        border-top: 1px solid #eee;
        padding: 10px;
        font-size: 13px;
    }
    .items-table tr:nth-child(even) { background: #fafafa; }

    /* Totals */
    .totals {
        text-align: right;
        margin-top: 15px;
        font-size: 15px;
    }

    /* Footer */
    .company-info {
        margin-top: 25px;
        font-size: 12px;
        color: #777;
        text-align: center;
        border-top: 1px solid #eee;
        padding-top: 12px;
    }
</style>
