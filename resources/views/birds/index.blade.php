@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Bird Management</h2>
        <a href="{{ route('birds.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" aria-label="Add new bird batch">
            <span class="mr-2" aria-hidden="true">➕</span> Add Bird Batch
        </a>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalQuantity ?? 0, 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Birds</span>
            </div>
            <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                <span class="text-sm text-gray-500 dark:text-gray-400">Layers</span>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($layers ?? 0, 0) }}</p>
            </div>
            <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                <span class="text-sm text-gray-500 dark:text-gray-400">Broilers</span>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($broilers ?? 0, 0) }}</p>
            </div>
            <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                <span class="text-sm text-gray-500 dark:text-gray-400">Chicks</span>
                <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ number_format($chicks ?? 0, 0) }}</p>
            </div>
        </div>
    </section>

    <!-- Birds Table -->
    <section>
        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-4 sm:p-6 rounded-2xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Bird Records</h3>
            <div id="toast-container" aria-live="polite" class="mb-4"></div>

            @if ($birds->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No birds found yet.</p>
                    <a href="{{ route('birds.create') }}" 
                       class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" aria-label="Add your first bird batch">
                        <span class="mr-2" aria-hidden="true">➕</span> Add Your First Batch
                    </a>
                </div>
            @else
                {{-- WRAPPER: this is replaced by AJAX when paging / after deletes --}}
                <div id="birds-table-wrapper">
                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto rounded-lg">
                        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">ID</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Breed</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Type</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Stage</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Quantity</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Alive</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Dead</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Cost</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Working</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Age (Weeks)</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Entry Date</th>
                                    <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($birds as $bird)
                                    <tr class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $bird->id }}</td>
                                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $bird->breed ?? 'N/A' }}</td>
                                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($bird->type ?? 'N/A') }}</td>
                                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($bird->stage ?? 'N/A') }}</td>
                                        <td class="p-3 text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $bird->quantity ?? 'N/A' }}</td>
                                        <td class="p-3 text-sm text-green-600 dark:text-green-400 font-bold">{{ $bird->alive ?? 'N/A' }}</td>
                                        <td class="p-3 text-sm text-red-600 dark:text-red-400 font-bold">{{ $bird->dead ?? 'N/A' }}</td>
                                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300" aria-describedby="currency-description">
                                            {{ $bird->cost ? '₵' . number_format($bird->cost, 2) : 'N/A' }}
                                        </td>
                                        <td class="p-3 text-sm">
                                            @if($bird->working)
                                                <span class="px-2 py-1 bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-100 text-xs rounded-full">Yes</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-100 text-xs rounded-full">No</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $bird->age ?? 'N/A' }}</td>
                                        <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ isset($bird->entry_date) ? $bird->entry_date->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="p-3 flex space-x-2">
                                            <button type="button" data-id="{{ $bird->id }}" data-url="{{ route('birds.edit', $bird->id) }}" 
                                                    class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white text-xs font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition" 
                                                    aria-label="Edit bird record {{ $bird->id }}">
                                                <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                            </button>
                                            <button type="button" data-id="{{ $bird->id }}" data-url="{{ route('birds.destroy', $bird->id) }}" 
                                                    class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition" 
                                                    aria-label="Delete bird record {{ $bird->id }}">
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
                        @foreach ($birds as $bird)
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">ID: {{ $bird->id }}</h4>
                                    <div class="flex space-x-2">
                                        <button type="button" data-id="{{ $bird->id }}" data-url="{{ route('birds.edit', $bird->id) }}" 
                                                class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white text-xs font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition" 
                                                aria-label="Edit bird record {{ $bird->id }}">
                                            <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                        </button>
                                        <button type="button" data-id="{{ $bird->id }}" data-url="{{ route('birds.destroy', $bird->id) }}" 
                                                class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition" 
                                                aria-label="Delete bird record {{ $bird->id }}">
                                            <span class="mr-2" aria-hidden="true">🗑</span> Delete
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <div><strong>Breed:</strong> {{ $bird->breed ?? 'N/A' }}</div>
                                    <div><strong>Type:</strong> {{ ucfirst($bird->type ?? 'N/A') }}</div>
                                    <div><strong>Stage:</strong> {{ ucfirst($bird->stage ?? 'N/A') }}</div>
                                    <div><strong>Quantity:</strong> {{ $bird->quantity ?? 'N/A' }}</div>
                                    <div><strong>Alive:</strong> {{ $bird->alive ?? 'N/A' }}</div>
                                    <div><strong>Dead:</strong> {{ $bird->dead ?? 'N/A' }}</div>
                                    <div><strong>Cost:</strong> {{ $bird->cost ? '₵' . number_format($bird->cost, 2) : 'N/A' }}</div>
                                    <div><strong>Working:</strong> {{ $bird->working ? 'Yes' : 'No' }}</div>
                                    <div><strong>Age:</strong> {{ $bird->age ?? 'N/A' }}</div>
                                    <div><strong>Entry Date:</strong> {{ isset($bird->entry_date) ? $bird->entry_date->format('Y-m-d') : 'N/A' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex justify-between items-center">
                        @if ($birds->hasPages())
                            <div class="flex space-x-2">
                                <a href="{{ $birds->previousPageUrl() }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ $birds->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   aria-label="Previous page" {{ $birds->onFirstPage() ? 'disabled' : '' }} data-ajax="true">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                </a>
                                <a href="{{ $birds->nextPageUrl() }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ !$birds->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   aria-label="Next page" {{ !$birds->hasMorePages() ? 'disabled' : '' }} data-ajax="true">
                                    Next
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Page {{ $birds->currentPage() }} of {{ $birds->lastPage() }}
                            </span>
                        @endif
                    </div>
                </div>
                {{-- END wrapper --}}
            @endif
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this bird record? This action cannot be undone.</p>
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
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to edit this bird record?</p>
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
    // Elements that remain outside the AJAX-replaced fragment
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

    // Show server-set success message
    @if (session('success'))
        toast('{{ session('success') }}', 'success', 4000);
    @endif

    // Helper: find the fragment in an HTML string and return innerHTML
    async function extractFragmentFromHtml(htmlText, selector = '#birds-table-wrapper') {
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');
        const fragment = doc.querySelector(selector);
        return fragment ? fragment.innerHTML : null;
    }

    // reloadTable: fetches page (or specific url) and replaces table wrapper
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
            const newInner = await extractFragmentFromHtml(text, '#birds-table-wrapper');
            if (!newInner) {
                // fallback: just reload if fragment cannot be found
                window.location.reload();
                return;
            }
            const wrapper = document.getElementById('birds-table-wrapper');
            wrapper.innerHTML = newInner;

            // re-bind controls for new content
            initTableControls();
        } catch (err) {
            console.error('reloadTable error', err);
            toast('Failed to refresh table. Reloading page...', 'error', 3000);
            setTimeout(() => window.location.reload(), 1200);
        }
    }

    // Initialize the event listeners inside the table fragment (for delete/edit/pagination)
    function initTableControls() {
        // Bind delete buttons inside the fragment
        document.querySelectorAll('#birds-table-wrapper .delete-btn').forEach(btn => {
            btn.removeEventListener('click', deleteBtnClickHandler);
            btn.addEventListener('click', deleteBtnClickHandler);
        });

        // Bind edit buttons inside the fragment
        document.querySelectorAll('#birds-table-wrapper .edit-btn').forEach(btn => {
            btn.removeEventListener('click', editBtnClickHandler);
            btn.addEventListener('click', editBtnClickHandler);
        });

        // Bind ajax pagination links inside the fragment
        document.querySelectorAll('#birds-table-wrapper [data-ajax="true"]').forEach(link => {
            link.removeEventListener('click', ajaxPageClickHandler);
            link.addEventListener('click', ajaxPageClickHandler);
        });
    }

    // Handlers
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

            // try parse JSON, but some backends may redirect. handle gracefully
            let data = {};
            try {
                data = await response.json();
            } catch (_) {
                data = null;
            }

            if (response.ok) {
                // if API returns success flag use it, otherwise assume successful 2xx means deleted
                const ok = data ? (data.success ?? true) : true;
                const msg = data ? (data.message ?? 'Record deleted') : 'Record deleted';
                if (ok) {
                    toast(msg, 'success', 2000);
                    // refresh just the table fragment (do not reload whole page)
                    await reloadTable();
                } else {
                    toast(data.message || 'Failed to delete record', 'error', 3000);
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

    // Edit handler: open modal and redirect to edit URL on confirm
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
        // we navigate to edit page (you could also fetch a fragment if you prefer)
        window.location.href = currentEditUrl;
    });

    // Intercept pagination clicks and load via AJAX
    function ajaxPageClickHandler(e) {
        e.preventDefault();
        const href = e.currentTarget.getAttribute('href');
        if (!href) return;
        // show a small loading toast
        toast('Loading page...', 'info', 800);
        reloadTable(href);
        // optionally update browser URL (uncomment to enable)
        // history.pushState(null, '', href);
    }

    // handle Escape to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!deleteModal.classList.contains('hidden')) {
                deleteModal.classList.add('hidden');
                deleteModal.style.display = 'none';
                currentDeleteUrl = null;
            }
            if (!editModal.classList.contains('hidden')) {
                editModal.classList.add('hidden');
                editModal.style.display = 'none';
                currentEditUrl = null;
            }
        }
    });

    // Initial binding on page load
    document.addEventListener('DOMContentLoaded', () => {
        // initial binding for elements that are present
        initTableControls();

        // Optional: capture popstate to reload fragment when user uses browser Back/Forward
        window.addEventListener('popstate', (e) => {
            // reload the current location fragment so the table matches URL
            reloadTable(window.location.href);
        });
    });
})();
</script>
@endpush
@endsection
