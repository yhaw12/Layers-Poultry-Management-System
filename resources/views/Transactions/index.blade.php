@extends('layouts.app')

@section('title', 'Pending Transactions')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Toast Container -->
    <div id="toast-container" aria-live="polite" class="mb-4"></div>

    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pending Transactions</h2>
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.index') }}" 
               class="inline-flex items-center bg-gray-300 text-gray-800 px-4 py-2 rounded-lg shadow hover:bg-gray-400 
                      dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 text-sm transition">
                🔄 Reset Filters
            </a>
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
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">₵ {{ number_format($transactions->sum('amount'), 2) }}</p>
            </div>
        </div>
    </section>

    <!-- Transactions Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Transaction Records</h3>
            <div id="transactions-table-wrapper" aria-live="polite">
                @if ($transactions->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">No pending transactions found.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg">
                        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm" data-sort-order="asc" aria-describedby="transactions-table-desc">
                            <caption id="transactions-table-desc" class="sr-only">List of pending transactions with actions to approve, reject, edit, or delete</caption>
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">ID</th>
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount (₵)</th>
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
                                        <td class="p-4 text-gray-700 dark:text-gray-300 font-medium">₵ {{ $amount }}</td>
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
                                               class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs focus:ring-2 focus:ring-blue-500 transition"
                                               aria-label="View transaction {{ $transaction->id }}">
                                                <span class="mr-2" aria-hidden="true">👀</span> View
                                            </a>
                                            {{-- <button type="button" data-id="{{ $transaction->id }}" data-url="{{ route('transactions.edit', $transaction->id) }}" 
                                                    class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs focus:ring-2 focus:ring-yellow-500 transition" 
                                                    aria-label="Edit transaction {{ $transaction->id }}">
                                                <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                            </button> --}}
                                            <button type="button" data-id="{{ $transaction->id }}" data-url="{{ route('transactions.destroy', $transaction->id) }}" 
                                                    class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs focus:ring-2 focus:ring-red-500 transition" 
                                                    aria-label="Delete transaction {{ $transaction->id }}">
                                                <span class="mr-2" aria-hidden="true">🗑</span> Delete
                                            </button>
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 text-xs focus:ring-2 focus:ring-green-500 transition"
                                                    onclick="openApproveModal({{ $transaction->id }}, {{ (float) ($transaction->amount ?? 0) }})"
                                                    aria-label="Open approve modal for transaction {{ $transaction->id }}">
                                                <span class="mr-2" aria-hidden="true">✅</span> Approve
                                            </button>
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 text-xs focus:ring-2 focus:ring-red-500 transition"
                                                    onclick="openRejectModal({{ $transaction->id }})"
                                                    aria-label="Open reject modal for transaction {{ $transaction->id }}">
                                                <span class="mr-2" aria-hidden="true">❌</span> Reject
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->hasPages())
                        <div class="mt-6 flex justify-between items-center">
                            <div class="flex space-x-2">
                                <a href="{{ $transactions->previousPageUrl() }}" 
                                   class="pagination-link inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ $transactions->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                   aria-label="Previous page" {{ $transactions->onFirstPage() ? 'disabled' : '' }} data-ajax="true">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                </a>
                                <a href="{{ $transactions->nextPageUrl() }}" 
                                   class="pagination-link inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ !$transactions->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                   aria-label="Next page" {{ !$transactions->hasMorePages() ? 'disabled' : '' }} data-ajax="true">
                                    Next
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
                            </span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this transaction? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="delete-cancel" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition" aria-label="Cancel delete">Cancel</button>
                <button id="delete-confirm" class="px-4 py-2 rounded bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition" aria-label="Confirm delete" disabled>
                    <span class="flex items-center">
                        <svg id="delete-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                        Delete
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Confirmation Modal -->
    <div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Edit</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to edit this transaction?</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="edit-cancel" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition" aria-label="Cancel edit">Cancel</button>
                <button id="edit-confirm" class="px-4 py-2 rounded bg-yellow-500 text-white text-sm font-medium hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition" aria-label="Confirm edit">
                    <span class="flex items-center">
                        <svg id="edit-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                        Edit
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle">
        <div class="bg-white dark:bg-[#1a1a3a] rounded-2xl p-6 w-full max-w-md shadow-lg border border-gray-200 dark:border-gray-700">
            <h3 id="approveModalTitle" class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Approve Transaction</h3>
            <form id="approveModalForm" method="POST" action="">
                @csrf
                <input type="hidden" name="transaction_id" id="approve_transaction_id">
                <label for="approve_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Approval Amount (leave blank to approve full amount)</label>
                <input id="approve_amount" name="amount" type="number" step="0.01" min="0" 
                        class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200"
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
                        <span class="flex items-center">
                            <svg id="approve-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                            Confirm Approve
                        </span>
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
                        <span class="flex items-center">
                            <svg id="reject-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                            Confirm Reject
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // --- Config / Selectors ---
    const wrapperSelector = '#transactions-table-wrapper';
    const toastContainer = document.getElementById('toast-container');

    // Modal elements
    const deleteModal = document.getElementById('delete-modal');
    const deleteCancel = document.getElementById('delete-cancel');
    const deleteConfirm = document.getElementById('delete-confirm');
    const deleteSpinner = document.getElementById('delete-spinner');

    const editModal = document.getElementById('edit-modal');
    const editCancel = document.getElementById('edit-cancel');
    const editConfirm = document.getElementById('edit-confirm');
    const editSpinner = document.getElementById('edit-spinner');

    const approveModal = document.getElementById('approveModal');
    const approveForm = document.getElementById('approveModalForm');
    const approveAmountInput = document.getElementById('approve_amount');
    const approveSpinner = document.getElementById('approve-spinner');

    const rejectModal = document.getElementById('rejectModal');
    const rejectForm = document.getElementById('rejectModalForm');
    const rejectReasonInput = document.getElementById('reject_reason');
    const rejectSpinner = document.getElementById('reject-spinner');

    // State holders
    let currentDeleteUrl = null;
    let currentEditUrl = null;
    let activeModal = null;
    let lastActiveElement = null;

    // --- Helpers ---
    function createToast(message, type = 'info', timeout = 3500) {
        if (!toastContainer) return;
        const id = 'toast-' + Date.now();
        const colors = {
            info: 'bg-indigo-600 text-white',
            success: 'bg-green-600 text-white',
            error: 'bg-red-600 text-white'
        };
        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = `mb-3 px-4 py-2 rounded shadow ${colors[type] || colors.info} max-w-sm flex justify-between items-center`;
        wrapper.setAttribute('role', 'status');
        wrapper.setAttribute('aria-live', 'polite');
        wrapper.innerHTML = `
            <span class="truncate">${escapeHtml(message)}</span>
            <button class="ml-4 text-white hover:text-gray-200 focus:outline-none" aria-label="Dismiss toast">✕</button>
        `;
        const btn = wrapper.querySelector('button');
        btn.addEventListener('click', () => wrapper.remove());
        toastContainer.appendChild(wrapper);

        // fadeout
        setTimeout(() => {
            wrapper.classList.add('opacity-0', 'transition', 'duration-300');
            setTimeout(() => wrapper.remove(), 350);
        }, timeout);
    }

    // simple escape to avoid accidental HTML injection in toasts
    function escapeHtml(s) {
        if (!s && s !== 0) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    async function extractFragmentFromHtml(htmlText, selector = wrapperSelector) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');
        const fragment = doc.querySelector(selector);
        return fragment ? fragment.innerHTML : null;
    }

    async function reloadTable(url = null, selector = wrapperSelector) {
        const target = url || window.location.href;
        try {
            const res = await fetch(target, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            // if server redirected (e.g. to login) follow the redirect normally
            if (res.redirected) {
                window.location.href = res.url;
                return;
            }

            if (!res.ok) {
                createToast('Failed to refresh table. Reloading page...', 'error', 3000);
                setTimeout(() => window.location.reload(), 900);
                return;
            }

            const text = await res.text();
            const newInner = await extractFragmentFromHtml(text, selector);
            if (!newInner) {
                createToast('Failed to refresh table fragment. Reloading page...', 'error', 3000);
                setTimeout(() => window.location.reload(), 900);
                return;
            }

            const wrapper = document.querySelector(selector);
            if (wrapper) {
                wrapper.innerHTML = newInner;
            }

            // Update any summary cards if present (optional, best-effort)
            try {
                const doc = new DOMParser().parseFromString(text, 'text/html');
                const newTotal = doc.querySelector('.grid .text-3xl');
                const oldTotal = document.querySelector('.grid .text-3xl');
                if (newTotal && oldTotal) oldTotal.innerText = newTotal.innerText;
            } catch (err) {
                // silent
            }

            initTableControls();

        } catch (err) {
            createToast('Network error when refreshing table. Reloading...', 'error', 3000);
            setTimeout(() => window.location.reload(), 900);
        }
    }

    // POST form via AJAX (with CSRF)
    async function postFormAjax(url, formData) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        };
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers,
            body: formData
        });
        const isJson = (res.headers.get('content-type') || '').includes('application/json');
        const data = isJson ? await res.json().catch(()=>null) : null;
        return { ok: res.ok, status: res.status, data };
    }

    // DELETE via AJAX (CSRF)
    async function deleteAjax(url) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        };
        const res = await fetch(url, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers
        });
        const isJson = (res.headers.get('content-type') || '').includes('application/json');
        const data = isJson ? await res.json().catch(()=>null) : null;
        return { ok: res.ok, status: res.status, data };
    }

    // --- Modal focus trap & body overflow management ---
    function openModal(modal) {
        if (!modal) return;
        lastActiveElement = document.activeElement;
        activeModal = modal;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // prevent page vertical overflow
        // focus first focusable element
        const focusable = modal.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
        if (focusable.length) focusable[0].focus();
        // attach key listener for escape and tab trapping
        modal.addEventListener('keydown', handleModalKeydown);
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.style.display = 'none';
        modal.removeEventListener('keydown', handleModalKeydown);
        document.body.style.overflow = '';
        activeModal = null;
        if (lastActiveElement && typeof lastActiveElement.focus === 'function') {
            lastActiveElement.focus();
        }
    }

    function handleModalKeydown(e) {
        if (!activeModal) return;
        if (e.key === 'Escape') {
            // close whichever modal is open
            if (!approveModal.classList.contains('hidden')) closeApproveModal();
            if (!rejectModal.classList.contains('hidden')) closeRejectModal();
            if (!editModal.classList.contains('hidden')) closeEditModal();
            if (!deleteModal.classList.contains('hidden')) closeDeleteModal();
            return;
        }
        if (e.key !== 'Tab') return;
        // trap focus
        const focusable = activeModal.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    }

    // --- Approve modal helpers ---
    function openApproveModal(id, amount = null) {
        if (!approveModal || !approveForm) return;
        approveForm.action = `/transactions/${id}/approve`;
        const hidden = approveForm.querySelector('#approve_transaction_id');
        if (hidden) hidden.value = id;
        if (approveAmountInput) {
            approveAmountInput.value = (amount !== null && amount !== undefined) ? parseFloat(amount).toFixed(2) : '';
        }
        openModal(approveModal);
    }

    function closeApproveModal() {
        if (!approveModal || !approveForm) return;
        approveForm.reset();
        closeModal(approveModal);
    }

    // --- Reject modal helpers ---
    function openRejectModal(id) {
        if (!rejectModal || !rejectForm) return;
        rejectForm.action = `/transactions/${id}/reject`;
        const hidden = rejectForm.querySelector('#reject_transaction_id');
        if (hidden) hidden.value = id;
        if (rejectReasonInput) rejectReasonInput.value = '';
        openModal(rejectModal);
    }

    function closeRejectModal() {
        if (!rejectModal || !rejectForm) return;
        rejectForm.reset();
        closeModal(rejectModal);
    }

    // --- Edit modal helpers ---
    function openEditModal(url) {
        currentEditUrl = url;
        if (!editModal) return;
        openModal(editModal);
    }

    function closeEditModal() {
        currentEditUrl = null;
        if (!editModal) return;
        closeModal(editModal);
    }

    // --- Delete modal helpers ---
    function openDeleteModal(url) {
        currentDeleteUrl = url;
        if (!deleteModal) return;
        openModal(deleteModal);
    }

    function closeDeleteModal() {
        currentDeleteUrl = null;
        if (!deleteModal) return;
        closeModal(deleteModal);
    }

    // --- Event handlers for UI controls (bound per fragment load) ---
    function initTableControls() {
        // Approve buttons (may be many)
        document.querySelectorAll(`${wrapperSelector} .approve-btn`).forEach(btn => {
            btn.removeEventListener('click', approveBtnHandler);
            btn.addEventListener('click', approveBtnHandler);
        });

        // Reject buttons
        document.querySelectorAll(`${wrapperSelector} .reject-btn`).forEach(btn => {
            btn.removeEventListener('click', rejectBtnHandler);
            btn.addEventListener('click', rejectBtnHandler);
        });

        // Delete buttons
        document.querySelectorAll(`${wrapperSelector} .delete-btn`).forEach(btn => {
            btn.removeEventListener('click', deleteBtnHandler);
            btn.addEventListener('click', deleteBtnHandler);
        });

        // Edit buttons (open confirm modal before navigation)
        document.querySelectorAll(`${wrapperSelector} .edit-btn`).forEach(btn => {
            btn.removeEventListener('click', editBtnHandler);
            btn.addEventListener('click', editBtnHandler);
        });

        // Pagination links (use attribute data-ajax="true" or class pagination-link)
        const pagerLinks = document.querySelectorAll(`${wrapperSelector} [data-ajax="true"], ${wrapperSelector} .pagination-link`);
        pagerLinks.forEach(a => {
            a.removeEventListener('click', paginationClickHandler);
            a.addEventListener('click', paginationClickHandler);
        });
    }

    // Button handlers
    function approveBtnHandler(e) {
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        const amt = btn.dataset.amount ?? '';
        openApproveModal(id, amt);
    }

    function rejectBtnHandler(e) {
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        openRejectModal(id);
    }

    function deleteBtnHandler(e) {
        const btn = e.currentTarget;
        const url = btn.dataset.url;
        openDeleteModal(url);
    }

    function editBtnHandler(e) {
        const btn = e.currentTarget;
        const url = btn.dataset.url;
        openEditModal(url);
    }

    // Pagination click
    async function paginationClickHandler(e) {
        e.preventDefault();
        const link = e.currentTarget;
        if (link.classList.contains('opacity-50') || link.hasAttribute('disabled')) return;
        const href = link.getAttribute('href');
        if (!href) return;
        createToast('Loading page...', 'info', 800);
        await reloadTable(href);
        try { history.pushState({}, '', href); } catch (err) { /* ignore */ }
        window.scrollTo({ top: 150, behavior: 'smooth' });
    }

    // --- Wire modal form submits (AJAX) ---
    if (approveForm) {
        approveForm.addEventListener('submit', async function (ev) {
            ev.preventDefault();
            const url = approveForm.action;
            if (!url) return;
            const amount = approveAmountInput && approveAmountInput.value ? parseFloat(approveAmountInput.value) : null;
            if (amount !== null && isNaN(amount)) {
                createToast('Please enter a valid amount', 'error');
                return;
            }
            // UI lock
            const submitBtn = approveForm.querySelector('[type="submit"]');
            submitBtn?.setAttribute('disabled', 'disabled');
            approveSpinner?.classList.remove('hidden');

            const fd = new FormData(approveForm);
            try {
                const { ok, status, data } = await postFormAjax(url, fd);
                if (ok) {
                    createToast((data && data.message) ? data.message : 'Transaction approved', 'success', 2000);
                    closeApproveModal();
                    await reloadTable();
                } else {
                    if (status === 422 && data && data.errors) {
                        const messages = Object.values(data.errors).flat().join(' ');
                        createToast(messages, 'error', 4000);
                    } else {
                        createToast((data && data.message) ? data.message : 'Failed to approve', 'error', 3500);
                    }
                }
            } catch (err) {
                createToast('Network error. Try again.', 'error', 3000);
            } finally {
                submitBtn?.removeAttribute('disabled');
                approveSpinner?.classList.add('hidden');
            }
        });
    }

    if (rejectForm) {
        rejectForm.addEventListener('submit', async function (ev) {
            ev.preventDefault();
            const url = rejectForm.action;
            if (!url) return;
            const reason = rejectReasonInput ? rejectReasonInput.value.trim() : '';
            if (!reason) {
                createToast('Please enter a reason for rejection.', 'error');
                return;
            }
            const submitBtn = rejectForm.querySelector('[type="submit"]');
            submitBtn?.setAttribute('disabled', 'disabled');
            rejectSpinner?.classList.remove('hidden');

            const fd = new FormData(rejectForm);
            try {
                const { ok, status, data } = await postFormAjax(url, fd);
                if (ok) {
                    createToast((data && data.message) ? data.message : 'Transaction rejected', 'success', 2000);
                    closeRejectModal();
                    await reloadTable();
                } else {
                    if (status === 422 && data && data.errors) {
                        const messages = Object.values(data.errors).flat().join(' ');
                        createToast(messages, 'error', 4000);
                    } else {
                        createToast((data && data.message) ? data.message : 'Failed to reject', 'error', 3500);
                    }
                }
            } catch (err) {
                createToast('Network error. Try again.', 'error', 3000);
            } finally {
                submitBtn?.removeAttribute('disabled');
                rejectSpinner?.classList.add('hidden');
            }
        });
    }

    // Delete confirm click
    if (deleteConfirm) {
        deleteConfirm.addEventListener('click', async function () {
            if (!currentDeleteUrl) return;
            deleteConfirm.setAttribute('disabled', 'disabled');
            deleteSpinner?.classList.remove('hidden');

            try {
                const { ok, status, data } = await deleteAjax(currentDeleteUrl);
                if (ok) {
                    createToast((data && data.message) ? data.message : 'Deleted', 'success', 2000);
                    closeDeleteModal();
                    await reloadTable();
                } else {
                    const msg = (data && data.message) ? data.message : `Delete failed (status ${status})`;
                    createToast(msg, 'error', 4000);
                }
            } catch (err) {
                createToast('Network error. Try again.', 'error', 3000);
            } finally {
                deleteConfirm.removeAttribute('disabled');
                deleteSpinner?.classList.add('hidden');
                currentDeleteUrl = null;
            }
        });
    }

    // Delete cancel
    if (deleteCancel) {
        deleteCancel.addEventListener('click', () => closeDeleteModal());
    }

    // Edit confirm (navigate)
    if (editConfirm) {
        editConfirm.addEventListener('click', () => {
            if (!currentEditUrl) return;
            editConfirm.setAttribute('disabled', 'disabled');
            editSpinner?.classList.remove('hidden');
            window.location.href = currentEditUrl;
        });
    }
    if (editCancel) {
        editCancel.addEventListener('click', () => closeEditModal());
    }

    // Provide global helper functions used by inline onclick attributes (if any)
    window.openApproveModal = openApproveModal;
    window.closeApproveModal = closeApproveModal;
    window.openRejectModal = openRejectModal;
    window.closeRejectModal = closeRejectModal;
    window.openEditConfirm = openEditModal; // legacy name
    window.openDeleteConfirm = openDeleteModal; // legacy name

    // Bind events after DOM ready and after AJAX fragment updates
    document.addEventListener('DOMContentLoaded', () => {
        initTableControls();
        // handle browser navigation (back/forward)
        window.addEventListener('popstate', () => {
            reloadTable(window.location.href);
        });
    });

})();
</script>
@endpush

@endsection