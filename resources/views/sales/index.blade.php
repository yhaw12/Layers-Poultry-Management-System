@extends('layouts.app')

@section('title', 'Sales')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-50 dark:bg-gray-900 dark:text-white">
    <div id="toast" class="fixed right-6 top-6 z-50 invisible pointer-events-none transition-all duration-300 ease-out opacity-0 transform translate-y-4" role="status" aria-live="polite" aria-atomic="true">
        <div id="toastInner" class="max-w-sm rounded-xl p-4 shadow-xl bg-gray-800 text-white flex items-center space-x-3" tabindex="-1">
            <svg id="toastIcon" class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="toastMessage" class="sr-only">Notification</span>
        </div>
    </div>

    <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Sales & Invoices</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage invoices, quick payments and exports — improved for speed and accessibility.</p>
        </div>

        <div class="flex space-x-4">
            <button id="openPendingPaymentsBtn" 
                    class="inline-flex items-center bg-yellow-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-yellow-600 dark:bg-yellow-400 dark:hover:bg-yellow-500 transition-colors duration-200 font-medium"
                    aria-controls="pendingPaymentsModal" aria-expanded="false">
                💰 Pending Payments <span id="unpaidCount" class="ml-2 bg-white dark:bg-gray-800 text-yellow-600 dark:text-yellow-300 rounded-full px-2.5 py-1 text-xs font-semibold">{{ $sales->where('status', '!=', 'paid')->count() }}</span>
            </button>

            <a href="{{ route('sales.create') }}" id="addSaleBtn"
               class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-blue-600 dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium" aria-label="Add new sale">
                ➕ Add Sale
            </a>
        </div>
    </section>

    <section>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center transform transition-all hover:scale-105 duration-200">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ number_format($sales->total(), 0) }}</p>
                <span class="text-sm text-gray-600 dark:text-gray-300 mt-1">Records</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center transform transition-all hover:scale-105 duration-200">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">₵ {{ number_format($sales->sum('total_amount'), 2) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center transform transition-all hover:scale-105 duration-200">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Paid</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">₵ {{ number_format($sales->sum('paid_amount'), 2) }}</p>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl border border-green-200">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-xl border border-red-200">{{ session('error') }}</div>
    @endif

    <section>
        <div class="container-box bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Filter Sales</h3>
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-end gap-4" id="filterForm">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $start) }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $end) }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[220px]">
                    <label for="quickSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input name="search" type="search" value="{{ request('search') }}" placeholder="Customer name, ID..." class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-xl font-medium">Filter</button>
                    <a href="{{ route('sales.index') }}" class="px-3 py-2 rounded-lg border text-sm text-gray-700 dark:text-gray-200">Reset</a>
                </div>
            </form>
        </div>
    </section>

    <section>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Sales Records</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Showing <strong>{{ $sales->count() }}</strong> of <strong>{{ $sales->total() }}</strong></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700/80">
                            <th class="p-4 text-left font-medium uppercase tracking-wider">Invoice #</th>
                            <th class="p-4 text-left font-medium uppercase tracking-wider">Customer</th>
                            <th class="p-4 text-left font-medium uppercase tracking-wider">Total (₵)</th>
                            <th class="p-4 text-left font-medium uppercase tracking-wider">Paid (₵)</th>
                            <th class="p-4 text-left font-medium uppercase tracking-wider">Status</th>
                            <th class="p-4 text-left font-medium uppercase tracking-wider">Date</th>
                            <th class="p-4 text-left font-medium uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody" class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($sales as $sale)
                            @php
                                $paidAmount = round((float)$sale->paid_amount, 2);
                                $totalAmount = round((float)$sale->total_amount, 2);
                                $balance = $totalAmount - $paidAmount;
                                
                                // UX Status Logic
                                if ($balance <= 0.01) {
                                    $statusColor = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
                                    $statusText = 'Paid';
                                } elseif ($paidAmount > 0) {
                                    $statusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
                                    $statusText = 'Partial';
                                } else {
                                    $statusColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
                                    $statusText = 'Pending';
                                }
                            @endphp
                            <tr data-sale-id="{{ $sale->id }}" class="sale-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="p-4 font-mono text-sm text-blue-600 dark:text-blue-400">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="p-4 font-medium text-gray-900 dark:text-white">{{ $sale->customer->name ?? 'Guest' }}</td>
                                <td class="p-4 font-semibold text-gray-700 dark:text-gray-200">₵ <span class="row-total">{{ number_format($totalAmount, 2) }}</span></td>
                                <td class="p-4 text-gray-500 dark:text-gray-400">
                                    ₵ <span class="row-paid">{{ number_format($paidAmount, 2) }}</span>
                                    @if($balance > 0)
                                        <div class="text-xs text-red-500 font-bold mt-1">Due: {{ number_format($balance, 2) }}</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $sale->sale_date->format('M d, Y') }}
                                </td>
                                <td class="p-4 flex items-center gap-3">
                                    {{-- UX FIX: Icon Buttons to save space --}}
                                    @if ($balance > 0.01)
                                        <button data-action="open-pay-modal" data-sale-id="{{ $sale->id }}" data-paid="{{ $paidAmount }}" data-total="{{ $totalAmount }}" 
                                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition p-1" title="Record Payment">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('sales.invoice', $sale->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition p-1" title="View Invoice">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>

                                    <a href="{{ route('sales.edit', $sale) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition p-1" title="Edit Sale">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $sales->links() }}</div>
        </div>
    </section>

    <div id="modalOverlay" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity duration-300"></div>

    <div id="pendingPaymentsModal" class="fixed inset-0 hidden flex items-center justify-center z-50 px-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Pending Payments</h3>
                <button onclick="closeModal('pendingPaymentsModal')" class="text-2xl text-gray-400 hover:text-white">&times;</button>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-100 dark:bg-gray-700"> <th class="p-2 text-left">Inv #</th> <th class="p-2 text-left">Customer</th> <th class="p-2 text-left">Balance</th> <th class="p-2 text-left">Action</th> </tr></thead>
                <tbody id="pendingTableBody" class="divide-y"></tbody>
            </table>
        </div>
    </div>

        <div id="paymentModal" class="fixed inset-0 hidden flex items-center justify-center z-50 px-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl max-w-md w-full relative shadow-2xl transform transition-all duration-300 scale-95 opacity-0">
            
            <button type="button" 
                    onclick="closeModal('paymentModal')" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors"
                    aria-label="Close modal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Record Payment #<span id="payIdDisplay" class="ml-1 text-blue-600"></span>
            </h3>

            <form id="paymentForm">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Amount (₵)</label>
                        <input type="number" id="pay_amount" step="0.01" 
                            class="w-full rounded-xl border border-gray-300 dark:bg-gray-700 dark:border-gray-600 p-3 text-lg font-bold text-green-600 focus:ring-2 focus:ring-green-500 outline-none transition" 
                            required>
                        <span id="payMaxAmount" class="text-xs text-gray-500 mt-2 block italic"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" id="pay_date" value="{{ date('Y-m-d') }}" 
                                class="w-full border border-gray-300 rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method</label>
                            <select id="pay_method" class="w-full border border-gray-300 rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 text-sm">
                                <option value="cash">Cash</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div id="payError" class="hidden p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-start">
                        <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span></span>
                    </div>

                    <div class="flex flex-col gap-2 pt-4">
                        <button type="submit" id="paySubmitBtn" 
                                class="w-full py-3 bg-green-600 text-white rounded-xl font-bold shadow-lg hover:bg-green-700 active:transform active:scale-95 transition duration-200">
                            <span id="payBtnText">Confirm Payment</span>
                        </button>
                        <button type="button" onclick="closeModal('paymentModal')" 
                                class="w-full py-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition">
                            Maybe Later
                        </button>
                    </div>
                </div>
            </form>
            </div>
        </div>
</div>

<script>
(function() {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        const inner = document.getElementById('toastInner');
        document.getElementById('toastMessage').innerText = msg;
        inner.className = `max-w-sm rounded-xl p-4 shadow-xl flex items-center space-x-3 text-white ${type === 'success' ? 'bg-green-800' : 'bg-red-800'}`;
        t.classList.remove('invisible', 'opacity-0');
        t.classList.add('opacity-100');
        setTimeout(() => { 
            t.classList.replace('opacity-100', 'opacity-0');
            setTimeout(() => t.classList.add('invisible'), 500); 
        }, 3500);
    }

    window.openModal = function(id) {
        const m = document.getElementById(id);
        const overlay = document.getElementById('modalOverlay');
        overlay.classList.remove('hidden');
        m.classList.remove('hidden');
        setTimeout(() => {
            m.querySelector('div').classList.replace('scale-95', 'scale-100');
            m.querySelector('div').classList.replace('opacity-0', 'opacity-100');
        }, 10);
    };

    window.closeModal = function(id) {
        const m = document.getElementById(id);
        const overlay = document.getElementById('modalOverlay');
        m.querySelector('div').classList.replace('scale-100', 'scale-95');
        m.querySelector('div').classList.replace('opacity-100', 'opacity-0');
        setTimeout(() => {
            m.classList.add('hidden');
            if(!document.querySelector('[id$="Modal"]:not(.hidden)')) overlay.classList.add('hidden');
        }, 300);
    };

    // Trigger Payment Modal
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="open-pay-modal"]');
        if (btn) {
            const sid = btn.dataset.saleId;
            const bal = (parseFloat(btn.dataset.total) - parseFloat(btn.dataset.paid)).toFixed(2);
            document.getElementById('payIdDisplay').innerText = sid;
            document.getElementById('pay_amount').value = bal;
            document.getElementById('pay_amount').max = bal;
            document.getElementById('payMaxAmount').innerText = `Outstanding: ₵${bal}`;
            document.getElementById('paymentForm').dataset.saleId = sid;
            openModal('paymentModal');
        }
    });

    // Handle AJAX Payment Submission
    document.getElementById('paymentForm').onsubmit = async function(e) {
        e.preventDefault();
        
        // FIX: Retrieve sid directly from the form dataset where we stored it
        const sid = this.dataset.saleId; 
        
        if (!sid) {
            console.error("Sale ID (sid) is missing from form dataset.");
            return;
        }

        const btn = document.getElementById('paySubmitBtn');
        const err = document.getElementById('payError');
        const btnText = document.getElementById('payBtnText');

        btn.disabled = true;
        btnText.innerText = 'Processing...';
        err.classList.add('hidden');

        try {
            const res = await fetch(`/sales/${sid}/record-payment?t=${Date.now()}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf, // Ensure this variable exists at the top of your script
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    amount: document.getElementById('pay_amount').value,
                    payment_date: document.getElementById('pay_date').value,
                    payment_method: document.getElementById('pay_method').value
                })
            });

            const contentType = res.headers.get("content-type");

            if (contentType && contentType.includes("application/json")) {
                const data = await res.json();
                if (data.success) {
                    showToast(data.message);
                    // UX FIX: Reload to update the table status badges immediately
                    window.location.reload(); 
                } else {
                    throw new Error(data.error || "Payment failed");
                }
            } else {
                // This handles the ResponseCache HTML interference
                throw new Error("System Error: Received HTML instead of JSON. Please run 'php artisan responsecache:clear' in your terminal.");
            }

        } catch (e) {
            err.querySelector('span').innerText = e.message;
            err.classList.remove('hidden');
            btn.disabled = false;
            btnText.innerText = 'Confirm Payment';
        }
    };

    // Pending Payments Scraper
    document.getElementById('openPendingPaymentsBtn').onclick = function() {
        const table = document.getElementById('pendingTableBody');
        table.innerHTML = '';
        document.querySelectorAll('.sale-row').forEach(row => {
            const paid = parseFloat(row.querySelector('.row-paid').innerText);
            const total = parseFloat(row.querySelector('.row-total').innerText);
            if (paid < total) {
                const id = row.dataset.saleId;
                const cust = row.cells[1].innerText;
                const bal = (total - paid).toFixed(2);
                table.innerHTML += `
                    <tr class="border-b dark:border-gray-700">
                        <td class="p-3">${id}</td>
                        <td class="p-3">${cust}</td>
                        <td class="p-3 font-bold text-red-600">₵${bal}</td>
                        <td class="p-3">
                            <button data-action="open-pay-modal" data-sale-id="${id}" data-total="${total}" data-paid="${paid}" 
                                class="text-blue-500 underline font-bold hover:text-blue-700 transition">Pay Now</button>
                        </td>
                    </tr>`;
            }
        });
        openModal('pendingPaymentsModal');
    };
})();
</script>

<style>
    /* Prevent UI flashing for hidden modals */
    [id$="Modal"].hidden { display: none; }
</style>
@endsection