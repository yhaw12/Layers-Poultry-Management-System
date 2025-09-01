{{-- resources/views/sales/invoice_fragment.blade.php --}}
<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <div class="company-logo">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ asset('logo.png') }}" alt="Company Logo">
            @else
                <h2>{{ $company['name'] }}</h2>
            @endif
        </div>
        <div class="invoice-info">
            <h1>INVOICE</h1>
            <p><strong>Invoice #:</strong> {{ $sale->id }}</p>
            <p><strong>Issue Date:</strong> {{ optional($sale->sale_date)->format('F j, Y') ?? 'N/A' }}</p>
            <p><strong>Due Date:</strong> {{ optional($sale->due_date)->format('F j, Y') ?? 'N/A' }}</p>
            <p><strong>Status:</strong> 
                <span class="status {{ $sale->status }}">
                    {{ ucfirst(str_replace('_',' ',$sale->status)) }}
                </span>
            </p>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="customer-info">
        <h3>Bill To:</h3>
        <p>{{ $sale->customer->name ?? 'Unknown Customer' }}</p>
        {{-- <p>📞 {{ $sale->customer->phone ?? 'N/A' }}</p> --}}
        <p>✉ {{ $sale->customer->email ?? 'N/A' }}</p>
    </div>

    <!-- Products Table -->
    <table class="items-table">
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
                    {{ $sale->saleable_type == 'App\Models\Bird' && $sale->saleable ? 
                        ($sale->saleable->breed . ' (' . ($sale->saleable->type ?? '') . ')') : 
                        ($sale->saleable ? 'Eggs' : 'Unknown Product') }}
                </td>
                <td>{{ ucfirst($sale->product_variant ?? 'N/A') }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>₵ {{ number_format($sale->unit_price, 2) }}</td>
                <td>₵ {{ number_format($sale->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment History -->
    @if($sale->payments->count() > 0)
        <h4>Payment History</h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->payments as $payment)
                    <tr>
                        <td>{{ optional($payment->payment_date)->format('F j, Y') ?? 'N/A' }}</td>
                        <td>₵ {{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Totals -->
    <div class="totals">
        <p><strong>Total Amount:</strong> ₵ {{ number_format($sale->total_amount,2) }}</p>
        <p><strong>Paid Amount:</strong> ₵ {{ number_format($sale->paid_amount,2) }}</p>
        <p><strong>Balance Due:</strong> 
            <span class="balance">₵ {{ number_format(max(0,$sale->total_amount - $sale->paid_amount), 2) }}</span>
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
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
    .status.paid { background: #28a745; }
    .status.pending { background: #ffc107; color:#333; }
    .status.overdue { background: #dc3545; }

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
    .totals strong { color: #000; }
    .balance { font-weight: bold; color: #dc3545; }

    /* Footer */
    .footer {
        margin-top: 25px;
        font-size: 12px;
        color: #777;
        text-align: center;
        border-top: 1px solid #eee;
        padding-top: 12px;
    }
</style>
