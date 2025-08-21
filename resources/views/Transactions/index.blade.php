@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold dark:text-gray-100">Pending Transactions</h1>

        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">Reset filters</a>

            {{-- Export (GET) --}}
            {{-- <a href="{{ route('transactions.export', array_filter([
                    'type' => request('type'),
                    'start_date' => request('start_date'),
                    'end_date' => request('end_date'),
                    'q' => request('q'),
                ])) }}"
               class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
               aria-label="Export visible transactions to CSV">
               Export CSV
            </a> --}}
        </div>
    </div>

    {{-- Success / Error --}}
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-3 text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-3 text-red-800">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('transactions.index') }}" id="transactions-filters" class="mb-6 bg-white dark:bg-gray-800 p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="q" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                <input id="q" name="q" value="{{ request('q') }}" placeholder="ID, description, customer..." type="search"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="">All types</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                    <option value="order" {{ request('type') === 'order' ? 'selected' : '' }}>Order</option>
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                <input id="start_date" name="start_date" type="date" value="{{ request('start_date', $start ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200" />
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                <input id="end_date" name="end_date" type="date" value="{{ request('end_date', $end ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200" />
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Apply</button>
            <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:underline">Clear</a>
            <p class="ml-auto text-sm text-gray-500 dark:text-gray-400">Showing <strong>{{ $transactions->count() }}</strong> of <strong>{{ $transactions->total() }}</strong></p>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" data-sort-order="asc" aria-describedby="transactions-table-desc">
            <caption id="transactions-table-desc" class="sr-only">List of pending transactions with actions to approve or reject</caption>
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Source</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($transactions as $transaction)
                    @php
                        // defensive / safe values
                        $type = $transaction->type ?? 'unknown';
                        $amount = is_numeric($transaction->amount) ? number_format($transaction->amount, 2) : ($transaction->amount ?? '0.00');
                        try {
                            $dateFormatted = \Carbon\Carbon::parse($transaction->date)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            $dateFormatted = $transaction->date ?? '';
                        }

                        // type badge classes
                        $typeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                        if ($type === 'sale') $typeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                        if ($type === 'expense') $typeClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                        if ($type === 'income') $typeClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                    @endphp

                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $transaction->id }}</td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeClass }}">
                                {{ ucfirst($type) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">${{ $amount }}</td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $dateFormatted }}</td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            @if ($transaction->source && $transaction->source_type === \App\Models\Sale::class)
                                <a href="{{ route('sales.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Sale #{{ $transaction->source_id }}</a>
                            @elseif ($transaction->source && $transaction->source_type === \App\Models\Expense::class)
                                <a href="{{ route('expenses.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Expense #{{ $transaction->source_id }}</a>
                            @elseif ($transaction->source && $transaction->source_type === \App\Models\Income::class)
                                <a href="{{ route('incomes.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Income #{{ $transaction->source_id }}</a>
                            @elseif ($transaction->source && $transaction->source_type === \App\Models\Order::class)
                                <a href="{{ route('orders.show', $transaction->source_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Order #{{ $transaction->source_id }}</a>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{-- View/detail --}}
                            <a href="{{ route('transactions.show', $transaction->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline mr-3">View</a>

                            {{-- Approve button opens modal --}}
                            <button
                                type="button"
                                class="text-green-600 dark:text-green-400 hover:underline mr-3"
                                onclick="openApproveModal({{ $transaction->id }}, {{ (float) ($transaction->amount ?? 0) }})"
                                aria-label="Open approve modal for transaction {{ $transaction->id }}">
                                Approve
                            </button>

                            {{-- Reject button opens modal --}}
                            <button
                                type="button"
                                class="text-red-600 dark:text-red-400 hover:underline"
                                onclick="openRejectModal({{ $transaction->id }})"
                                aria-label="Open reject modal for transaction {{ $transaction->id }}">
                                Reject
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No pending transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
</div>

{{-- Approve Modal (accessible, form posts to approve route) --}}
<div id="approveModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black bg-opacity-40" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-lg">
        <h3 id="approveModalTitle" class="text-lg font-semibold mb-3 dark:text-gray-100">Approve Transaction</h3>

        <form id="approveModalForm" method="POST" action="">
            @csrf
            <input type="hidden" name="transaction_id" id="approve_transaction_id">

            <label for="approve_amount" class="block text-sm text-gray-700 dark:text-gray-300">Approval Amount (leave blank to approve full amount)</label>
            <input id="approve_amount" name="amount" type="number" step="0.01" min="0" class="mt-2 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 p-2" />

            <div class="mt-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 rounded bg-gray-600 text-white hover:bg-gray-700" onclick="closeApproveModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Confirm Approve</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black bg-opacity-40" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-lg">
        <h3 id="rejectModalTitle" class="text-lg font-semibold mb-3 dark:text-gray-100">Reject Transaction</h3>

        <form id="rejectModalForm" method="POST" action="">
            @csrf
            <input type="hidden" name="transaction_id" id="reject_transaction_id">

            <label for="reject_reason" class="block text-sm text-gray-700 dark:text-gray-300">Reason for rejection</label>
            <textarea id="reject_reason" name="reason" rows="4" required class="mt-2 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 p-2"></textarea>

            <div class="mt-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 rounded bg-gray-600 text-white hover:bg-gray-700" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    /* Debounce helper for search */
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
            // optional: keep the page at top after submit
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
        form.action = `/transactions/${id}/approve`; // hits your controller route (POST)
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
        form.action = `/transactions/${id}/reject`; // hits your controller route (POST)
        modal.classList.remove('hidden');
        reason.focus();
        trapFocus(modal);
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        releaseFocus();
    }

    // Simple focus trap for modal (keeps keyboard focus inside)
    let _previousActive = null;
    function trapFocus(modal) {
        _previousActive = document.activeElement;
        const focusable = modal.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (!first) return;
        first.focus();

        modal.addEventListener('keydown', trapHandler = (e) => {
            if (e.key === 'Escape') {
                // close whichever modal is visible
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
        });
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

    // Optional: basic table sort (maintained from your earlier script)
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

<script>
function approveTransaction(id) {
    fetch(`/transactions/${id}/approve`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Transaction approved ✅");
            document.getElementById(`transaction-row-${id}`).remove();
        }
    });
}

function declineTransaction(id) {
    fetch(`/transactions/${id}/decline`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Transaction declined ❌");
            document.getElementById(`transaction-row-${id}`).remove();
        }
    });
}
</script>

@endpush
@endsection
