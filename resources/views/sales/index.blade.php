@extends('layouts.app')

@section('title', 'Sales')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sales & Invoices</h2>
        <div class="flex space-x-4">
            <button id="openPendingPaymentsBtn" 
                    class="inline-flex items-center bg-yellow-600 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-700 
                           dark:bg-yellow-500 dark:hover:bg-yellow-600 transition">
                💰 Pending Payments <span id="unpaidCount" class="ml-2 bg-white dark:bg-gray-700 text-yellow-600 dark:text-yellow-300 rounded-full px-2 py-1 text-xs">{{ $sales->where('status', '!=', 'paid')->count() }}</span>
            </button>
            <a href="{{ route('sales.create') }}" 
               class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                      dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                ➕ Add Sale
            </a>
        </div>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Sales</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($sales->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Amount</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">GHS {{ number_format($sales->sum('total_amount'), 2) }}</p>
                <span class="text-gray-600 dark:text-gray-300">GHS</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Paid</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">GHS {{ number_format($sales->sum('paid_amount'), 2) }}</p>
                <span class="text-gray-600 dark:text-gray-300">GHS</span>
            </div>
        </div>
    </section>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-2xl border border-red-200 dark:border-red-700">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Filter Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Sales</h3>
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <button type="submit" 
                        class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                               dark:bg-blue-500 dark:hover:bg-blue-600 text-sm transition">
                    🔍 Filter
                </button>
            </form>
        </div>
    </section>

    <!-- Sales Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Sales Records</h3>
            @if ($sales->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No sales records found yet.</p>
                    <a href="{{ route('sales.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        ➕ Add Your First Sale
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Invoice #</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Product</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Paid (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody" class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($sales as $sale)
                                <tr data-sale-id="{{ $sale->id }}" class="sale-row hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">{{ $sale->id }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $sale->customer ? $sale->customer->name : 'Unknown' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">
                                        @if ($sale->saleable_type == 'App\Models\Bird' || $sale->saleable_type == \App\Models\Bird::class)
                                            {{ optional($sale->saleable)->breed ?? 'Bird' }} {{ optional($sale->saleable)->type ? '(' . $sale->saleable->type . ')' : '' }}
                                        @else
                                            {{ $sale->saleable ? 'Eggs' : 'Unknown Product' }}
                                        @endif
                                    </td>
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">{{ $sale->quantity }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">GHS <span class="sale-total-amount">{{ number_format($sale->total_amount, 2) }}</span></td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">GHS <span class="sale-paid-amount">{{ number_format($sale->paid_amount, 2) }}</span></td>
                                    <td class="p-4">
                                        @php
                                            $status = $sale->status;
                                            $statusText = ucfirst(str_replace('_', ' ', $status));
                                        @endphp
                                        <span class="sale-status px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $status == 'paid' ? 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200' : ($status == 'partially_paid' ? 'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200' : ($status == 'overdue' ? 'bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-200' : 'bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-200')) }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">
                                        {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') : 'N/A' }}
                                    </td>
                                    <td class="p-4 flex space-x-2">
                                        @php
                                            // hide pay button if fully paid (safe compare)
                                            $isPaid = round((float)$sale->paid_amount, 2) >= round((float)$sale->total_amount, 2);
                                        @endphp

                                        @if (! $isPaid)
                                            <button data-action="open-pay-modal"
                                                    data-sale-id="{{ $sale->id }}"
                                                    data-paid="{{ number_format($sale->paid_amount, 2, '.', '') }}"
                                                    data-total="{{ number_format($sale->total_amount, 2, '.', '') }}"
                                                    class="pay-btn inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 text-xs transition">
                                                💳 Pay
                                            </button>
                                        @endif

                                        <button data-action="preview-invoice" data-sale-id="{{ $sale->id }}"
                                                class="invoice-preview-btn inline-flex items-center px-3 py-1 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 text-xs transition">
                                            📜 Preview
                                        </button>
                                        <a href="{{ route('sales.invoice', $sale->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs transition">
                                           📥 Download
                                        </a> 
                                        @can('email-invoices')
                                            <a href="{{ route('sales.emailInvoice', $sale->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-purple-500 text-white rounded-lg shadow hover:bg-purple-600 text-xs transition">
                                               📧 Email
                                            </a>
                                        @endcan
                                        <a href="{{ route('sales.edit', $sale) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs transition">
                                           ✏️ Edit
                                        </a>
                                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this sale record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs transition">
                                                🗑 Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($sales instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-6 flex justify-end">
                        {{ $sales->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>

    <!-- Invoice Preview Modal -->
    <div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-[#2d2d5a] p-6 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Invoice Preview</h3>
                <button id="closeInvoiceBtn" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">✕</button>
            </div>
            <div id="invoiceContent" class="prose dark:prose-invert"></div>
        </div>
    </div>

    <!-- Pending Payments Modal -->
    <div id="pendingPaymentsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-[#2d2d5a] p-6 rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Pending Payments</h3>
                <button id="closePendingPaymentsBtn" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">✕</button>
            </div>
            <div class="mb-4">
                <label for="paymentStatusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Status</label>
                <select id="paymentStatusFilter" onchange="loadPendingPayments()" 
                        class="w-full max-w-[200px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                    <option value="">All Unpaid</option>
                    <option value="pending">Pending</option>
                    <option value="partially_paid">Partially Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div id="pendingPaymentsContent" class="overflow-x-auto">
                <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Invoice #</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total (GHS)</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Paid (GHS)</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Balance (GHS)</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Due Date</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="pendingPaymentsTable" class="divide-y divide-gray-200 dark:divide-gray-600">
                        <!-- Will be filled by loadPendingPayments() if needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-[#2d2d5a] p-6 rounded-2xl max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Record Payment for Invoice #<span id="paymentSaleId"></span></h3>
                <button id="closePaymentBtn" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">✕</button>
            </div>
            <form id="paymentForm" method="POST" onsubmit="return handlePaymentSubmit(event)">
                @csrf
                <div class="mb-4">
                    <label for="payment_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (GHS)</label>
                    <input type="number" id="payment_amount" name="amount" step="0.01" min="0.01" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition" required>
                    <span id="paymentMaxAmount" class="text-xs text-gray-500 dark:text-gray-400"></span>
                </div>
                <div class="mb-4">
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ now()->format('Y-m-d') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition" required>
                </div>
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                    <select id="payment_method" name="payment_method" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div id="paymentError" class="hidden mb-4 p-2 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg"></div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelPaymentBtn" onclick="closePaymentModal()" 
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                        Cancel
                    </button>
                    <button type="submit" id="submitPaymentBtn"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition">
                        💳 Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Helpers and initializations
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Open payment modal with sale data
    function openPaymentModal(saleId, paid, total) {
        const modal = document.getElementById('paymentModal');
        document.getElementById('paymentSaleId').innerText = saleId;
        modal.dataset.saleId = saleId;

        const paidNum = parseFloat(paid || 0);
        const totalNum = parseFloat(total || 0);
        const balance = Math.max(0, (totalNum - paidNum));

        const amountInput = document.getElementById('payment_amount');
        amountInput.value = balance.toFixed(2);
        amountInput.max = balance.toFixed(2);
        document.getElementById('paymentMaxAmount').innerText = 'Outstanding balance: GHS ' + balance.toFixed(2);

        document.getElementById('paymentError').classList.add('hidden');
        modal.classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    // Attach handlers to pay buttons and invoice preview
    document.addEventListener('click', function(e) {
        const payBtn = e.target.closest('[data-action="open-pay-modal"]');
        if (payBtn) {
            const saleId = payBtn.dataset.saleId;
            const paid = payBtn.dataset.paid;
            const total = payBtn.dataset.total;
            openPaymentModal(saleId, paid, total);
            return;
        }

        const invoicePreviewBtn = e.target.closest('[data-action="preview-invoice"]');
        if (invoicePreviewBtn) {
            const saleId = invoicePreviewBtn.dataset.saleId;
            previewInvoice(saleId);
            return;
        }
    });

    // Invoice preview (simple fetch of view with preview query)
    async function previewInvoice(saleId) {
        const url = `/sales/${saleId}/invoice?preview=1`;
        const modal = document.getElementById('invoiceModal');
        const contentEl = document.getElementById('invoiceContent');
        contentEl.innerHTML = '<p>Loading...</p>';
        modal.classList.remove('hidden');
        try {
            const res = await fetch(url, { headers: { 'Accept': 'text/html' }});
            if (!res.ok) throw new Error('Failed to load invoice preview');
            const html = await res.text();
            contentEl.innerHTML = html;
        } catch (err) {
            contentEl.innerHTML = `<div class="text-red-600">Error loading preview.</div>`;
        }
    }

    document.getElementById('closeInvoiceBtn')?.addEventListener('click', function() {
        document.getElementById('invoiceModal').classList.add('hidden');
    });

    // Payment form submit handler -> AJAX post
    async function handlePaymentSubmit(e) {
        e.preventDefault();
        const modal = document.getElementById('paymentModal');
        const saleId = modal.dataset.saleId;
        if (!saleId) return;

        const amount = parseFloat(document.getElementById('payment_amount').value || 0);
        const date = document.getElementById('payment_date').value;
        const method = document.getElementById('payment_method').value;
        const errorEl = document.getElementById('paymentError');
        errorEl.classList.add('hidden');
        errorEl.innerText = '';

        const url = `/sales/${saleId}/record-payment`;

        // Prepare JSON body (you may also send FormData — JSON is simpler here)
        const payload = {
            amount: amount,
            payment_date: date,
            payment_method: method || null,
        };

        try {
            const res = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin', // <-- Important: send cookies so Laravel stays authenticated
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            // Try to parse JSON safely
            let data;
            try {
                data = await res.json();
            } catch (parseErr) {
                // Not JSON — handle as error
                throw new Error('Server returned non-JSON response (possible redirect).');
            }

            if (!res.ok) {
                // If validation errors
                if (data && data.errors) {
                    const messages = Object.values(data.errors).flat().join(' ');
                    errorEl.innerText = messages;
                } else if (data && data.error) {
                    errorEl.innerText = data.error;
                } else {
                    errorEl.innerText = 'Failed to record payment.';
                }
                errorEl.classList.remove('hidden');
                return;
            }

            // success - update DOM
            const saleRow = document.querySelector(`tr[data-sale-id="${saleId}"]`);
            if (saleRow) {
                const paidEl = saleRow.querySelector('.sale-paid-amount');
                if (paidEl) {
                    paidEl.innerText = parseFloat(data.paid_amount).toFixed(2);
                }

                const statusEl = saleRow.querySelector('.sale-status');
                if (statusEl) {
                    statusEl.innerText = data.status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
                    statusEl.className = 'sale-status px-2 py-1 text-xs font-semibold rounded-full';
                    if (data.status === 'paid') {
                        statusEl.classList.add('bg-green-200', 'text-green-800', 'dark:bg-green-700', 'dark:text-green-200');
                    } else if (data.status === 'partially_paid') {
                        statusEl.classList.add('bg-yellow-200', 'text-yellow-800', 'dark:bg-yellow-700', 'dark:text-yellow-200');
                    } else if (data.status === 'overdue') {
                        statusEl.classList.add('bg-red-200', 'text-red-800', 'dark:bg-red-700', 'dark:text-red-200');
                    } else {
                        statusEl.classList.add('bg-blue-200', 'text-blue-800', 'dark:bg-blue-700', 'dark:text-blue-200');
                    }
                }

                const payBtn = saleRow.querySelector('[data-action="open-pay-modal"]');
                if (data.status === 'paid' && payBtn) {
                    payBtn.remove();
                }
            }

            // update unpaidCount if changed to paid
            const unpaidCountEl = document.getElementById('unpaidCount');
            if (unpaidCountEl && data.status === 'paid') {
                const prev = parseInt(unpaidCountEl.innerText) || 0;
                unpaidCountEl.innerText = Math.max(0, prev - 1);
            }

            closePaymentModal();
            // optional user feedback
            // show a nice toast; simple alert for now
            alert(data.message || 'Payment recorded.');

        } catch (err) {
            // fallback catch
            console.error('Payment request error', err);
            errorEl.innerText = err.message || 'An unexpected error occurred.';
            errorEl.classList.remove('hidden');
        }

        return false;
    }


    // Close modal handlers
    document.getElementById('closePaymentBtn')?.addEventListener('click', closePaymentModal);
    document.getElementById('cancelPaymentBtn')?.addEventListener('click', closePaymentModal);

    // Pending payments modal opener
    document.getElementById('openPendingPaymentsBtn')?.addEventListener('click', function() {
        document.getElementById('pendingPaymentsModal').classList.remove('hidden');
        loadPendingPayments();
    });

    document.getElementById('closePendingPaymentsBtn')?.addEventListener('click', function() {
        document.getElementById('pendingPaymentsModal').classList.add('hidden');
    });

    // Load pending payments (simple fetch of filtered list)
    async function loadPendingPayments() {
        const filter = document.getElementById('paymentStatusFilter').value;
        const tableBody = document.getElementById('pendingPaymentsTable');
        tableBody.innerHTML = '<tr><td colspan="8" class="p-4 text-center">Loading...</td></tr>';

        // We'll use the index endpoint to fetch JSON of unpaid sales if you implement an API; for now, I'll call a simple JSON endpoint that you can create later.
        // Fallback: build table from current DOM by scanning rows.
        const rows = document.querySelectorAll('tr[data-sale-id]');
        const pending = [];
        rows.forEach(r => {
            const saleId = r.dataset.saleId;
            const cust = r.querySelector('td:nth-child(2)')?.innerText || '';
            const totalText = r.querySelector('.sale-total-amount')?.innerText || '0';
            const paidText = r.querySelector('.sale-paid-amount')?.innerText || '0';
            const statusEl = r.querySelector('.sale-status');
            const status = statusEl ? statusEl.innerText.toLowerCase() : '';
            const due = r.querySelector('td:nth-child(8)')?.innerText || '';
            const total = parseFloat(totalText.replace(/,/g,'')) || 0;
            const paid = parseFloat(paidText.replace(/,/g,'')) || 0;
            const balance = (total - paid).toFixed(2);
            if (status !== 'paid') {
                if (!filter || status.includes(filter.replace('_', ' '))) {
                    pending.push({ saleId, cust, total, paid, balance, status, due });
                }
            }
        });

        if (pending.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" class="p-4 text-center">No pending payments found.</td></tr>';
            return;
        }

        tableBody.innerHTML = pending.map(p => `
            <tr>
                <td class="p-4 font-semibold text-blue-600">${p.saleId}</td>
                <td class="p-4">${p.cust}</td>
                <td class="p-4">GHS ${parseFloat(p.total).toFixed(2)}</td>
                <td class="p-4">GHS ${parseFloat(p.paid).toFixed(2)}</td>
                <td class="p-4">GHS ${parseFloat(p.balance).toFixed(2)}</td>
                <td class="p-4">${p.status}</td>
                <td class="p-4">${p.due}</td>
                <td class="p-4">
                    <button data-action="open-pay-modal" data-sale-id="${p.saleId}" data-paid="${p.paid}" data-total="${p.total}" class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 text-xs transition">
                        💳 Pay
                    </button>
                </td>
            </tr>
        `).join('');
    }
</script>
@endsection
