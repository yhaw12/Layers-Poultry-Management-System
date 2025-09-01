@extends('layouts.app')

@section('title', 'Feed Inventory')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Feed Inventory</h2>
        <div class="flex space-x-4">
            <a href="{{ route('feed.create') }}" 
               class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" 
               aria-label="Add new feed">
                <span class="mr-2" aria-hidden="true">➕</span> Add Feed
            </a>
            <a href="{{ route('feed.consumption') }}" 
               class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 focus:ring-2 focus:ring-green-500 transition" 
               aria-label="Record feed consumption">
                <span class="mr-2" aria-hidden="true">📊</span> Record Consumption
            </a>
        </div>
    </section>

    <!-- Feeds Table -->
    <section>
        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Feed Records</h3>
            <div id="toast-container" aria-live="polite" class="mb-4"></div>
            <div id="feeds-table-wrapper" aria-live="polite">
                @section('table-content')
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Purchased</span>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalQuantity, 0) }}</p>
                            <span class="text-gray-600 dark:text-gray-300">Bags</span>
                        </div>
                        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Cost</span>
                            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400" aria-describedby="currency-description">₵ {{ number_format($totalCost, 2) }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Consumed</span>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalConsumed, 2) }}</p>
                            <span class="text-gray-600 dark:text-gray-300">Kilograms</span>
                        </div>
                    </div>
                    @if ($feeds->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-600 dark:text-gray-400 mb-4">No feed records found yet.</p>
                            <a href="{{ route('feed.create') }}" 
                               class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" 
                               aria-label="Add your first feed">
                                <span class="mr-2" aria-hidden="true">➕</span> Add Your First Feed
                            </a>
                        </div>
                    @else
                        <!-- Desktop Table -->
                        <div class="hidden sm:block overflow-x-auto rounded-lg">
                            <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                                <thead>
                                    <tr class="bg-gray-200 dark:bg-gray-700">
                                        <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                                        <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Weight (kg)</th>
                                        <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Purchase Date</th>
                                        <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Cost (₵)</th>
                                        <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($feeds as $feed)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                            <td class="p-4 text-gray-700 dark:text-gray-300">{{ $feed->type }}</td>
                                            <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">{{ $feed->quantity }}</td>
                                            <td class="p-4 text-gray-700 dark:text-gray-300">{{ number_format($feed->weight, 2) }}</td>
                                            <td class="p-4 text-gray-700 dark:text-gray-300">{{ $feed->purchase_date->format('Y-m-d') }}</td>
                                            <td class="p-4 text-gray-700 dark:text-gray-300" aria-describedby="currency-description">{{ number_format($feed->cost, 2) }}</td>
                                            <td class="p-4 flex space-x-2">
                                                <button type="button" data-id="{{ $feed->id }}" data-url="{{ route('feed.edit', $feed) }}" 
                                                        class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs focus:ring-2 focus:ring-yellow-500 transition" 
                                                        aria-label="Edit feed {{ $feed->type }} purchased on {{ $feed->purchase_date->format('Y-m-d') }}">
                                                    <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                                </button>
                                                <button type="button" data-id="{{ $feed->id }}" data-url="{{ route('feed.destroy', $feed) }}" 
                                                        class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs focus:ring-2 focus:ring-red-500 transition" 
                                                        aria-label="Delete feed {{ $feed->type }} purchased on {{ $feed->purchase_date->format('Y-m-d') }}">
                                                    <span class="mr-2" aria-hidden="true">🗑</span> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Mobile Card Layout -->
                        <div class="sm:hidden space-y-4">
                            @foreach ($feeds as $feed)
                                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $feed->type }}</h4>
                                        <div class="flex space-x-2">
                                            <button type="button" data-id="{{ $feed->id }}" data-url="{{ route('feed.edit', $feed) }}" 
                                                    class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs focus:ring-2 focus:ring-yellow-500 transition" 
                                                    aria-label="Edit feed {{ $feed->type }} purchased on {{ $feed->purchase_date->format('Y-m-d') }}">
                                                <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                            </button>
                                            <button type="button" data-id="{{ $feed->id }}" data-url="{{ route('feed.destroy', $feed) }}" 
                                                    class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs focus:ring-2 focus:ring-red-500 transition" 
                                                    aria-label="Delete feed {{ $feed->type }} purchased on {{ $feed->purchase_date->format('Y-m-d') }}">
                                                <span class="mr-2" aria-hidden="true">🗑</span> Delete
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <div><strong>Quantity:</strong> {{ $feed->quantity }} bags</div>
                                        <div><strong>Weight:</strong> {{ number_format($feed->weight, 2) }} kg</div>
                                        <div><strong>Purchase Date:</strong> {{ $feed->purchase_date->format('Y-m-d') }}</div>
                                        <div><strong>Cost:</strong> ₵ {{ number_format($feed->cost, 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center">
                            @if ($feeds->hasPages())
                                <div class="flex space-x-2">
                                    <a href="{{ $feeds->previousPageUrl() }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ $feeds->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                       aria-label="Previous page" {{ $feeds->onFirstPage() ? 'disabled' : '' }} data-ajax="true">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </a>
                                    <a href="{{ $feeds->nextPageUrl() }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ !$feeds->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                       aria-label="Next page" {{ !$feeds->hasMorePages() ? 'disabled' : '' }} data-ajax="true">
                                        Next
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Page {{ $feeds->currentPage() }} of {{ $feeds->lastPage() }}
                                </span>
                            @endif
                        </div>
                    @endif
                @show
            </div>
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this feed record? This action cannot be undone.</p>
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
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to edit this feed record?</p>
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

    <!-- Hidden currency description for accessibility -->
    <span id="currency-description" class="sr-only">Amount in Ghanaian Cedi</span>
</div>

@push('scripts')
<script>
(function () {
    // Elements
    const toastContainer = document.getElementById('toast-container');
    const deleteModal = document.getElementById('delete-modal');
    const deleteCancel = document.getElementById('delete-cancel');
    const deleteConfirm = document.getElementById('delete-confirm');
    const deleteSpinner = document.getElementById('delete-spinner');
    const editModal = document.getElementById('edit-modal');
    const editCancel = document.getElementById('edit-cancel');
    const editConfirm = document.getElementById('edit-confirm');
    const editSpinner = document.getElementById('edit-spinner');

    // Toast helper
    function toast(message, type = 'info', timeout = 3000) {
        const id = 't-' + Date.now();
        const colors = {
            info: 'bg-indigo-600 text-white',
            success: 'bg-green-600 text-white',
            error: 'bg-red-600 text-white'
        };
        const el = document.createElement('div');
        el.id = id;
        el.className = `mb-3 px-4 py-2 rounded shadow ${colors[type] || colors.info} max-w-sm flex justify-between items-center`;
        el.innerHTML = `
            <span>✅ ${message}</span>
            <button class="ml-4 text-white hover:text-gray-200" aria-label="Dismiss toast">✕</button>
        `;
        toastContainer.appendChild(el);
        const closeBtn = el.querySelector('button');
        closeBtn.addEventListener('click', () => el.remove());
        setTimeout(() => {
            el.classList.add('opacity-0', 'transition', 'duration-300');
            setTimeout(() => el.remove(), 350);
        }, timeout);
    }

    // Show server-set messages
    @if (session('success'))
        toast('{{ session('success') }}', 'success', 4000);
    @endif
    @if (session('error'))
        toast('{{ session('error') }}', 'error', 4000);
    @endif

    // Helper: extract fragment from HTML
    async function extractFragmentFromHtml(htmlText, selector = '#feeds-table-wrapper') {
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');
        const fragment = doc.querySelector(selector);
        return fragment ? fragment.innerHTML : null;
    }

    // reloadTable: fetches page and replaces table wrapper
    async function reloadTable(url = null) {
        const targetUrl = url || window.location.href;
        try {
            const res = await fetch(targetUrl, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });
            if (!res.ok) {
                throw new Error('Failed to load page: ' + res.status);
            }
            const text = await res.text();
            const newInner = await extractFragmentFromHtml(text, '#feeds-table-wrapper');
            if (!newInner) {
                toast('Failed to refresh table. Reloading page...', 'error', 3000);
                setTimeout(() => window.location.reload(), 1200);
                return;
            }
            const wrapper = document.getElementById('feeds-table-wrapper');
            wrapper.innerHTML = newInner;
            initTableControls();
        } catch (err) {
            console.error('reloadTable error', err);
            toast('Failed to refresh table. Reloading page...', 'error', 3000);
            setTimeout(() => window.location.reload(), 1200);
        }
    }

    // Initialize event listeners for table controls
    function initTableControls() {
        // Delete buttons
        document.querySelectorAll('#feeds-table-wrapper .delete-btn').forEach(btn => {
            btn.removeEventListener('click', deleteBtnClickHandler);
            btn.addEventListener('click', deleteBtnClickHandler);
        });

        // Edit buttons
        document.querySelectorAll('#feeds-table-wrapper .edit-btn').forEach(btn => {
            btn.removeEventListener('click', editBtnClickHandler);
            btn.addEventListener('click', editBtnClickHandler);
        });

        // Pagination links
        document.querySelectorAll('#feeds-table-wrapper [data-ajax="true"]').forEach(link => {
            link.removeEventListener('click', ajaxPageClickHandler);
            link.addEventListener('click', ajaxPageClickHandler);
        });
    }

    // Delete handler
    let currentDeleteUrl = null;
    function deleteBtnClickHandler(e) {
        const btn = e.currentTarget;
        currentDeleteUrl = btn.dataset.url;
        deleteModal.classList.remove('hidden');
        deleteModal.style.display = 'flex';
        deleteConfirm.disabled = false;
        deleteSpinner.classList.add('hidden');
        deleteConfirm.focus();
    }

    deleteCancel.addEventListener('click', () => {
        deleteModal.classList.add('hidden');
        deleteModal.style.display = 'none';
        currentDeleteUrl = null;
    });

    deleteConfirm.addEventListener('click', async () => {
        if (!currentDeleteUrl) return;
        deleteConfirm.disabled = true;
        deleteSpinner.classList.remove('hidden');

        try {
            const response = await fetch(currentDeleteUrl, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            let data = {};
            try {
                data = await response.json();
            } catch (_) {
                data = null;
            }

            if (response.ok) {
                const ok = data ? (data.success ?? true) : true;
                const msg = data ? (data.message ?? 'Feed record deleted') : 'Feed record deleted';
                if (ok) {
                    toast(msg, 'success', 2000);
                    await reloadTable();
                } else {
                    toast(data.message || 'Failed to delete feed record', 'error', 3000);
                }
            } else {
                const errMsg = (data && data.message) ? data.message : `Failed to delete (status ${response.status})`;
                toast(errMsg, 'error', 3500);
            }
        } catch (err) {
            console.error(err);
            toast('Network error. Please try again.', 'error', 3000);
        } finally {
            deleteModal.classList.add('hidden');
            deleteModal.style.display = 'none';
            deleteConfirm.disabled = false;
            deleteSpinner.classList.add('hidden');
            currentDeleteUrl = null;
        }
    });

    // Edit handler
    let currentEditUrl = null;
    function editBtnClickHandler(e) {
        const btn = e.currentTarget;
        currentEditUrl = btn.dataset.url;
        editModal.classList.remove('hidden');
        editModal.style.display = 'flex';
        editConfirm.disabled = false;
        editSpinner.classList.add('hidden');
        editConfirm.focus();
    }

    editCancel.addEventListener('click', () => {
        editModal.classList.add('hidden');
        editModal.style.display = 'none';
        currentEditUrl = null;
    });

    editConfirm.addEventListener('click', () => {
        if (!currentEditUrl) return;
        editConfirm.disabled = true;
        editSpinner.classList.remove('hidden');
        window.location.href = currentEditUrl;
    });

    // Pagination handler
    function ajaxPageClickHandler(e) {
        e.preventDefault();
        const href = e.currentTarget.getAttribute('href');
        if (!href) return;
        toast('Loading page...', 'info', 800);
        reloadTable(href);
        // Optionally update browser URL
        // history.pushState(null, '', href);
    }

    // Initial binding
    document.addEventListener('DOMContentLoaded', () => {
        initTableControls();
        window.addEventListener('popstate', (e) => {
            reloadTable(window.location.href);
        });
    });
})();
</script>
@endpush
@endsection
