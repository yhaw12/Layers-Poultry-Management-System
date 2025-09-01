@extends('layouts.app')

@section('title', 'Mortalities')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Mortalities</h2>
        <a href="{{ route('mortalities.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" 
           aria-label="Add new mortality record">
            <span class="mr-2" aria-hidden="true">➕</span> Add Mortality
        </a>
    </section>

    <!-- Summary Card -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Mortalities</span>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($totalMortalities, 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
        </div>
    </section>

    <!-- Toast Container -->
    <div id="toast-container" aria-live="polite" class="mb-4"></div>

    <!-- Mortalities Table -->
    <section>
        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Mortality Records</h3>
            <div id="mortalities-table-wrapper" aria-live="polite">
                @if ($mortalities->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">No mortality records found yet.</p>
                        <a href="{{ route('mortalities.create') }}" 
                           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" 
                           aria-label="Add your first mortality record">
                            <span class="mr-2" aria-hidden="true">➕</span> Add Your First Mortality Record
                        </a>
                    </div>
                @else
                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto rounded-lg">
                        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Cause</th>
                                    <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($mortalities as $mortality)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                        <td class="p-4 text-gray-700 dark:text-gray-300">{{ $mortality->date ?: 'N/A' }}</td>
                                        <td class="p-4 font-semibold text-red-600 dark:text-red-400">{{ $mortality->quantity }}</td>
                                        <td class="p-4 text-gray-700 dark:text-gray-300">{{ $mortality->cause ?? 'N/A' }}</td>
                                        <td class="p-4 flex space-x-2">
                                            {{-- <a href="{{ route('mortalities.show', $mortality) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs focus:ring-2 focus:ring-blue-500 transition" 
                                               aria-label="View mortality record on {{ $mortality->date ?: 'N/A' }}">
                                                <span class="mr-2" aria-hidden="true">👀</span> View
                                            </a> --}}
                                            <button type="button" data-id="{{ $mortality->id }}" data-url="{{ route('mortalities.edit', $mortality) }}" 
                                                    class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs focus:ring-2 focus:ring-yellow-500 transition" 
                                                    aria-label="Edit mortality record on {{ $mortality->date ?: 'N/A' }}">
                                                <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                            </button>
                                            <button type="button" data-id="{{ $mortality->id }}" data-url="{{ route('mortalities.destroy', $mortality) }}" 
                                                    class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs focus:ring-2 focus:ring-red-500 transition" 
                                                    aria-label="Delete mortality record on {{ $mortality->date ?: 'N/A' }}">
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
                        @foreach ($mortalities as $mortality)
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $mortality->date ?: 'N/A' }}</h4>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('mortalities.show', $mortality) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs focus:ring-2 focus:ring-blue-500 transition" 
                                           aria-label="View mortality record on {{ $mortality->date ?: 'N/A' }}">
                                            <span class="mr-2" aria-hidden="true">👀</span> View
                                        </a>
                                        <button type="button" data-id="{{ $mortality->id }}" data-url="{{ route('mortalities.edit', $mortality) }}" 
                                                class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs focus:ring-2 focus:ring-yellow-500 transition" 
                                                aria-label="Edit mortality record on {{ $mortality->date ?: 'N/A' }}">
                                            <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                        </button>
                                        <button type="button" data-id="{{ $mortality->id }}" data-url="{{ route('mortalities.destroy', $mortality) }}" 
                                                class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs focus:ring-2 focus:ring-red-500 transition" 
                                                aria-label="Delete mortality record on {{ $mortality->date ?: 'N/A' }}">
                                            <span class="mr-2" aria-hidden="true">🗑</span> Delete
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <div><strong>Quantity:</strong> {{ $mortality->quantity }}</div>
                                    <div><strong>Cause:</strong> {{ $mortality->cause ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    @if ($mortalities instanceof \Illuminate\Pagination\LengthAwarePaginator && $mortalities->hasPages())
                        <div class="mt-6 flex justify-between items-center">
                            <div class="flex space-x-2">
                                <a href="{{ $mortalities->previousPageUrl() }}" 
                                   class="pagination-link inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ $mortalities->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                   aria-label="Previous page" {{ $mortalities->onFirstPage() ? 'disabled' : '' }} data-ajax="true">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                </a>
                                <a href="{{ $mortalities->nextPageUrl() }}" 
                                   class="pagination-link inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ !$mortalities->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                   aria-label="Next page" {{ !$mortalities->hasMorePages() ? 'disabled' : '' }} data-ajax="true">
                                    Next
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Page {{ $mortalities->currentPage() }} of {{ $mortalities->lastPage() }}
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
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this mortality record? This action cannot be undone.</p>
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
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to edit this mortality record?</p>
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
    async function extractFragmentFromHtml(htmlText, selector = '#mortalities-table-wrapper') {
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
            const newInner = await extractFragmentFromHtml(text);
            if (!newInner) {
                toast('Failed to refresh table. Reloading page...', 'error', 3000);
                setTimeout(() => window.location.reload(), 1200);
                return;
            }
            const wrapper = document.getElementById('mortalities-table-wrapper');
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
        document.querySelectorAll('#mortalities-table-wrapper .delete-btn').forEach(btn => {
            btn.removeEventListener('click', deleteBtnClickHandler);
            btn.addEventListener('click', deleteBtnClickHandler);
        });

        // Edit buttons
        document.querySelectorAll('#mortalities-table-wrapper .edit-btn').forEach(btn => {
            btn.removeEventListener('click', editBtnClickHandler);
            btn.addEventListener('click', editBtnClickHandler);
        });

        // Pagination links
        document.querySelectorAll('#mortalities-table-wrapper .pagination-link').forEach(link => {
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
                const msg = data ? (data.message ?? 'Mortality record deleted') : 'Mortality record deleted';
                if (ok) {
                    toast(msg, 'success', 2000);
                    await reloadTable();
                } else {
                    toast(data.message || 'Failed to delete mortality record', 'error', 3000);
                }
            } else {
                const errMsg = (data && data.message) ? data.message : `Failed to delete (status ${response.status})`;
                toast(errMsg, 'error', 3500);
            }
        } catch (err) {
            console.error('Delete error:', err);
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
        const link = e.currentTarget;
        if (link.classList.contains('opacity-50')) return; // Ignore disabled links
        const href = link.getAttribute('href');
        if (!href) return;
        toast('Loading page...', 'info', 800);
        reloadTable(href);
    }

    // Initial binding
    document.addEventListener('DOMContentLoaded', () => {
        initTableControls();
        window.addEventListener('popstate', () => {
            reloadTable(window.location.href);
        });
    });
})();
</script>
@endpush
@endsection