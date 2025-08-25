@extends('layouts.app')

{{-- @section('title', 'Pending Transactions') --}}

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pending Transactions</h2>
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.index') }}" 
               class="inline-flex items-center bg-gray-300 text-gray-800 px-4 py-2 rounded-lg shadow hover:bg-gray-400 
                      dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 text-sm transition">
                🔄 Reset Filters
            </a>
            {{-- <a href="{{ route('transactions.export', array_filter([
                    'type' => request('type'),
                    'status' => request('status'),
                    'start_date' => request('start_date'),
                    'end_date' => request('end_date'),
                    'q' => request('q'),
                ])) }}" 
               class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 
                      dark:bg-green-500 dark:hover:bg-green-600 text-sm transition"
               onclick="return confirm('Export visible transactions to CSV?');"
               aria-label="Export visible transactions to CSV">
                📥 Export CSV
            </a> --}}
        </div>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Pending Transactions</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($transactions->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Amount</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">GHS {{ number_format($transactions->sum('amount'), 2) }}</p>
                <span class="text-gray-600 dark:text-gray-300">GHS</span>
            </div>
        </div>
    </section>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if (session('error') || $errors->any())
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-2xl border border-red-200 dark:border-red-700">
            ❌ 
            @if (session('error'))
                {{ session('error') }}
            @else
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <!-- Filter Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Transactions</h3>
            <form method="GET" action="{{ route('transactions.index') }}" id="transactions-filters" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="q" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="ID, description, customer..." type="search"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                           aria-describedby="q-error">
                    @error('q')
                        <p id="q-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select id="type" name="type" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                        <option value="">All Types</option>
                        <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                        <option value="order" {{ request('type') === 'order' ? 'selected' : '' }}>Order</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input id="start_date" name="start_date" type="date" value="{{ request('start_date', $start ?? '') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                           aria-describedby="start_date-error">
                    @error('start_date')
                        <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input id="end_date" name="end_date" type="date" value="{{ request('end_date', $end ?? '') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                           aria-describedby="end_date-error">
                    @error('end_date')
                        <p id="end_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex items-end gap-4">
                    <button type="submit" 
                            class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                                   dark:bg-blue-500 dark:hover:bg-blue-600 text-sm transition">
                        🔍 Apply
                    </button>
                    <a href="{{ route('transactions.index') }}" 
                       class="inline-flex items-center bg-gray-300 text-gray-800 px-4 py-2 rounded-lg shadow hover:bg-gray-400 
                              dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 text-sm transition">
                        🔄 Clear
                    </a>
                    <p class="ml-auto text-sm text-gray-500 dark:text-gray-400">Showing <strong>{{ $transactions->count() }}</strong> of <strong>{{ $transactions->total() }}</strong></p>
                </div>
            </form>
        </div>
    </section>

    <!-- Transaction Chart -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Transaction Trends</h3>
            <canvas id="transactionChart" class="w-full h-64"></canvas>
        </div>
    </section>

    <!-- Transactions Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Transaction Records</h3>
            @if ($transactions->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No pending transactions found.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm" data-sort-order="asc" aria-describedby="transactions-table-desc">
                        <caption id="transactions-table-desc" class="sr-only">List of pending transactions with actions to approve or reject</caption>
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">ID</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Source</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($transactions as $transaction)
                                @php
                                    $type = $transaction->type ?? 'unknown';
                                    $status = $transaction->status ?? 'pending';
                                    $amount = is_numeric($transaction->amount) ? number_format($transaction->amount, 2) : ($transaction->amount ?? '0.00');
                                    $dateFormatted = $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') : 'N/A';
                                    $typeClass = match($type) {
                                        'sale' => 'bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-200',
                                        'expense' => 'bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-200',
                                        'income' => 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200',
                                        'order' => 'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200',
                                        default => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    };
                                    $statusClass = match($status) {
                                        'approved' => 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200',
                                        'rejected' => 'bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-200',
                                        default => 'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition" id="transaction-row-{{ $transaction->id }}">
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">{{ $transaction->id }}</td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeClass }}">
                                            {{ ucfirst($type) }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300 font-medium">GHS {{ $amount }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $dateFormatted }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">
                                        @if ($transaction->source && $transaction->source_type === \App\Models\Sale::class)
                                            <a href="{{ route('sales.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Sale #{{ $transaction->source_id }}</a>
                                        @elseif ($transaction->source && $transaction->source_type === \App\Models\Expense::class)
                                            <a href="{{ route('expenses.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Expense #{{ $transaction->source_id }}</a>
                                        @elseif ($transaction->source && $transaction->source_type === \App\Models\Income::class)
                                            <a href="{{ route('income.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Income #{{ $transaction->source_id }}</a>
                                        @elseif ($transaction->source && $transaction->source_type === \App\Models\Order::class)
                                            <a href="{{ route('orders.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Order #{{ $transaction->source_id }}</a>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="p-4 flex space-x-2">
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs transition">
                                            👀 View
                                        </a>
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 text-xs transition"
                                                onclick="openApproveModal({{ $transaction->id }}, {{ (float) ($transaction->amount ?? 0) }})"
                                                aria-label="Open approve modal for transaction {{ $transaction->id }}">
                                            ✅ Approve
                                        </button>
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 text-xs transition"
                                                onclick="openRejectModal({{ $transaction->id }})"
                                                aria-label="Open reject modal for transaction {{ $transaction->id }}">
                                            ❌ Reject
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-6 flex justify-end">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle">
        <div class="bg-white dark:bg-[#1a1a3a] rounded-2xl p-6 w-full max-w-md shadow-lg border border-gray-200 dark:border-gray-700">
            <h3 id="approveModalTitle" class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Approve Transaction</h3>
            <form id="approveModalForm" method="POST" action="">
                @csrf
                <input type="hidden" name="transaction_id" id="approve_transaction_id">
                <label for="approve_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Approval Amount (leave blank to approve full amount)</label>
                <input id="approve_amount" name="amount" type="number" step="0.01" min="0" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition mb-4"
                       aria-describedby="approve_amount-error">
                @error('amount')
                    <p id="approve_amount-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
                <div class="flex justify-end gap-4">
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-300 text-gray-800 shadow hover:bg-gray-400 
                                   dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 text-sm transition"
                            onclick="closeApproveModal()">Cancel</button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white shadow hover:bg-green-700 
                                   dark:bg-green-500 dark:hover:bg-green-600 text-sm transition">
                        ✅ Confirm Approve
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
        <div class="bg-white dark:bg-[#1a1a3a] rounded-2xl p-6 w-full max-w-md shadow-lg border border-gray-200 dark:border-gray-700">
            <h3 id="rejectModalTitle" class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Reject Transaction</h3>
            <form id="rejectModalForm" method="POST" action="">
                @csrf
                <input type="hidden" name="transaction_id" id="reject_transaction_id">
                <label for="reject_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Rejection</label>
                <textarea id="reject_reason" name="reason" rows="4" required 
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition mb-4"
                          aria-describedby="reject_reason-error"></textarea>
                @error('reason')
                    <p id="reject_reason-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
                <div class="flex justify-end gap-4">
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-300 text-gray-800 shadow hover:bg-gray-400 
                                   dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 text-sm transition"
                            onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 text-white shadow hover:bg-red-700 
                                   dark:bg-red-500 dark:hover:bg-red-600 text-sm transition">
                        ❌ Confirm Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('scripts')
<script>
    // Debounce helper for search
    function debounce(fn, delay = 400) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    }

    // Wire search input to submit after debounce
    (function () {
        const q = document.getElementById('q');
        if (!q) return;
        const form = document.getElementById('transactions-filters');

        q.addEventListener('input', debounce(() => {
            form.submit();
        }, 700));
    })();

    // Modal helpers
    function openApproveModal(id, maxAmount = 0) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveModalForm');
        const hidden = document.getElementById('approve_transaction_id');
        const amount = document.getElementById('approve_amount');

        hidden.value = id;
        amount.value = maxAmount ? parseFloat(maxAmount).toFixed(2) : '';
        form.action = `/transactions/${id}/approve`;
        modal.classList.remove('hidden');
        amount.focus();
        trapFocus(modal);
    }

    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.classList.add('hidden');
        releaseFocus();
    }

    function openRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectModalForm');
        const hidden = document.getElementById('reject_transaction_id');
        const reason = document.getElementById('reject_reason');

        hidden.value = id;
        reason.value = '';
        form.action = `/transactions/${id}/reject`;
        modal.classList.remove('hidden');
        reason.focus();
        trapFocus(modal);
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        releaseFocus();
    }

    // Focus trap for modals
    let _previousActive = null;
    let trapHandler = null;
    function trapFocus(modal) {
        _previousActive = document.activeElement;
        const focusable = modal.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (!first) return;
        first.focus();

        trapHandler = (e) => {
            if (e.key === 'Escape') {
                if (!document.getElementById('approveModal').classList.contains('hidden')) closeApproveModal();
                if (!document.getElementById('rejectModal').classList.contains('hidden')) closeRejectModal();
            }
            if (e.key === 'Tab') {
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        };
        modal.addEventListener('keydown', trapHandler);
    }

    function releaseFocus() {
        const modals = [document.getElementById('approveModal'), document.getElementById('rejectModal')];
        modals.forEach(m => m.removeEventListener('keydown', trapHandler));
        if (_previousActive) _previousActive.focus();
        _previousActive = null;
    }

    // Client-side validations for modal forms
    document.getElementById('approveModalForm').addEventListener('submit', (e) => {
        const amountInput = document.getElementById('approve_amount');
        if (amountInput.value && parseFloat(amountInput.value) < 0) {
            e.preventDefault();
            alert('Amount must be a positive number.');
        }
    });

    document.getElementById('rejectModalForm').addEventListener('submit', (e) => {
        const reason = document.getElementById('reject_reason');
        if (!reason.value.trim()) {
            e.preventDefault();
            alert('Please enter a reason for rejection.');
        }
    });

    // Table sort
    function sortTable(columnIndex, type) {
        const table = document.querySelector('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isAscending = table.dataset.sortOrder !== 'asc';
        table.dataset.sortOrder = isAscending ? 'asc' : 'desc';

        rows.sort((a, b) => {
            const aValue = a.cells[columnIndex].textContent.trim();
            const bValue = b.cells[columnIndex].textContent.trim();
            if (type === 'number') {
                const aNum = parseFloat(aValue.replace(/[^0-9.-]+/g, '')) || 0;
                const bNum = parseFloat(bValue.replace(/[^0-9.-]+/g, '')) || 0;
                return isAscending ? aNum - bNum : bNum - aNum;
            } else if (type === 'date') {
                return isAscending ? new Date(aValue) - new Date(bValue) : new Date(bValue) - new Date(aValue);
            }
            return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
        });

        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endpush
@endsection