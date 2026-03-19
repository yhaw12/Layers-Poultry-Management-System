@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-gray-900 dark:text-white">
    <!-- Header -->
    <section>
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Egg Records</h2>
            <a href="{{ route('eggs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white text-base font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" aria-label="Add new egg record">
                <span class="mr-2">➕</span> Add Egg Record
            </a>
        </div>
    </section>

    <!-- Search Form -->
    <section>
        <form method="GET" class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded-md p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Start date">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded-md p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="End date">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by date, crates, eggs, or pen..." class="border rounded-md p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Search egg records">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" aria-label="Search">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1116.65 16.65z"></path></svg>
                    Search
                </button>
            </div>
        </form>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Produced Crates</span>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 7l-10 6-10-6m0 0l10-6 10 6m-10 6v8"></path></svg>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalProducedCrates, 0) }}</p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Additional Eggs</span>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.21 0 4 3.58 4 8s-1.79 8-4 8-4-3.58-4-8 1.79-8 4-8m0 0v18"></path></svg>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalAdditionalEggs, 0) }}</p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Produced Eggs</span>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.21 0 4 3.58 4 8s-1.79 8-4 8-4-3.58-4-8 1.79-8 4-8m0 0v18"></path></svg>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($totalProducedEggs, 0) }}</p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Cracked Eggs</span>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.21 0 4 3.58 4 8s-1.79 8-4 8-4-3.58-4-8 1.79-8 4-8m0 0v18"></path></svg>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($totalCracked, 0) }}</p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Sold Crates</span>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 7l-10 6-10-6m0 0l10-6 10 6m-10 6v8"></path></svg>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($totalSoldCrates, 0) }}</p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Remaining Crates</span>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 7l-10 6-10-6m0 0l10-6 10 6m-10 6v8"></path></svg>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($remainingCrates, 0) }}</p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Remaining Eggs</span>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.21 0 4 3.58 4 8s-1.79 8-4 8-4-3.58-4-8 1.79-8 4-8m0 0v18"></path></svg>
                    <p class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ number_format($remainingEggs, 0) }}</p>
                </div>
            </div>
        </div>
    </section>

    

    <!-- Egg Records Table -->
    <section>
        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Egg Records</h3>
                <form method="POST" action="{{ route('eggs.bulkDelete') }}" id="bulk-delete-form">
                    @csrf
                    <button type="button" id="bulk-delete-btn" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition {{ $eggs->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}" aria-label="Delete selected egg records" {{ $eggs->isEmpty() ? 'disabled' : '' }}>
                        <span class="mr-2">🗑</span> Delete Selected
                    </button>
                </form>
            </div>
            <div id="toast-container" aria-live="polite" class="mb-4"></div>
            @if ($eggs->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No egg records found yet.</p>
                    <a href="{{ route('eggs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white text-base font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" aria-label="Add your first egg record">
                        <span class="mr-2">➕</span> Add Your First Egg Record
                    </a>
                </div>
            @else
                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200"><input type="checkbox" id="select-all" aria-label="Select all egg records"></th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Date Laid</th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Pen</th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Crates</th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Total Eggs</th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hidden md:table-cell">Cracked</th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hidden md:table-cell">Egg Size</th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hidden lg:table-cell">Created By</th>
                                <th scope="col" class="p-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($eggs as $egg)
                                <tr class="border-b dark:border-gray-700 even:bg-gray-50 dark:even:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <td class="p-3"><input type="checkbox" name="ids[]" value="{{ $egg->id }}" aria-label="Select egg record {{ $egg->id }}"></td>
                                    <td scope="row" class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $egg->date_laid->format('Y-m-d') }}</td>
                                    <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $egg->pen->name ?? 'N/A' }}</td>
                                    <td class="p-3 text-sm font-semibold text-blue-600 dark:text-blue-400">{{ number_format($egg->crates, 2) }}</td>
                                    <td class="p-3 text-sm text-gray-700 dark:text-gray-300">{{ $egg->total_eggs }}</td>
                                    <td class="p-3 text-sm hidden md:table-cell">
                                        @if ($egg->is_cracked)
                                            <span class="px-2 py-1 bg-red-200 text-red-800 text-xs rounded-full">Yes</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-200 text-green-800 text-xs rounded-full">No</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 dark:text-gray-300 hidden md:table-cell">{{ $egg->egg_size ?? 'N/A' }}</td>
                                    <td class="p-3 text-sm text-gray-700 dark:text-gray-300 hidden lg:table-cell">{{ $egg->createdBy->name ?? 'N/A' }}</td>
                                    <td class="p-3 flex space-x-2">
                                        <a href="{{ route('eggs.edit', $egg) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white text-xs font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition" aria-label="Edit egg record {{ $egg->id }}">
                                            <span class="mr-2">✏️</span> Edit
                                        </a>
                                        <button type="button" data-id="{{ $egg->id }}" data-url="{{ route('eggs.destroy', $egg) }}" class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition" aria-label="Delete egg record {{ $egg->id }}">
                                            <span class="mr-2">🗑</span> Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Mobile Card Layout -->
                <div class="sm:hidden space-y-4">
                    @foreach ($eggs as $egg)
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">ID: {{ $egg->id }}</h4>
                                <div class="flex space-x-2">
                                    <a href="{{ route('eggs.edit', $egg) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white text-xs font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition" aria-label="Edit egg record {{ $egg->id }}">
                                        <span class="mr-2">✏️</span> Edit
                                    </a>
                                    <button type="button" data-id="{{ $egg->id }}" data-url="{{ route('eggs.destroy', $egg) }}" class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition" aria-label="Delete egg record {{ $egg->id }}">
                                        <span class="mr-2">🗑</span> Delete
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <div><strong>Date Laid:</strong> {{ $egg->date_laid->format('Y-m-d') }}</div>
                                <div><strong>Pen:</strong> {{ $egg->pen->name ?? 'N/A' }}</div>
                                <div><strong>Crates:</strong> {{ number_format($egg->crates, 2) }}</div>
                                <div><strong>Total Eggs:</strong> {{ $egg->total_eggs }}</div>
                                <div><strong>Cracked:</strong> {{ $egg->is_cracked ? 'Yes' : 'No' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                 <!-- Pagination -->
                <div class="mt-6 flex justify-end">
                    {{ $eggs->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this egg record? This action cannot be undone.</p>
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

    <!-- Bulk Delete Confirmation Modal -->
    <div id="bulk-delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Bulk Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete the selected egg records? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="bulk-delete-cancel" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition" aria-label="Cancel bulk delete">Cancel</button>
                <button id="bulk-delete-confirm" class="px-4 py-2 rounded bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition" aria-label="Confirm bulk delete" disabled>
                    <span class="flex items-center">
                        <svg id="bulk-delete-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                        Delete Selected
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- <script src="{{ asset('js/chart.min.js') }}"></script> --}}
<script>
(function () {
    // Elements
    const toastContainer = document.getElementById('toast-container');
    const deleteModal = document.getElementById('delete-modal');
    const deleteCancel = document.getElementById('delete-cancel');
    const deleteConfirm = document.getElementById('delete-confirm');
    const deleteSpinner = document.getElementById('delete-spinner');
    const bulkDeleteModal = document.getElementById('bulk-delete-modal');
    const bulkDeleteCancel = document.getElementById('bulk-delete-cancel');
    const bulkDeleteConfirm = document.getElementById('bulk-delete-confirm');
    const bulkDeleteSpinner = document.getElementById('bulk-delete-spinner');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    // Toast function (consistent with settings and birds)
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

    // Show success toast if present
    @if (session('success'))
        toast('{{ session('success') }}', 'success', 4000);
    @endif

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function () {
        document.querySelectorAll('input[name="ids[]"]').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Delete modal handler
    let currentDeleteUrl = null;
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            currentDeleteUrl = btn.dataset.url;
            deleteModal.classList.remove('hidden');
            deleteModal.style.display = 'flex';
            deleteConfirm.disabled = false;
            deleteSpinner.classList.add('hidden');
            deleteConfirm.focus();
        });
    });

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
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (response.ok) {
                toast('Egg record deleted successfully', 'success', 2000);
                setTimeout(() => location.reload(), 1000);
            } else {
                toast('Failed to delete record', 'error');
            }
        } catch (err) {
            console.error(err);
            toast('Network error. Please try again.', 'error');
        } finally {
            deleteModal.classList.add('hidden');
            deleteModal.style.display = 'none';
            deleteConfirm.disabled = false;
            deleteSpinner.classList.add('hidden');
            currentDeleteUrl = null;
        }
    });

    // Bulk delete modal handler
    document.getElementById('bulk-delete-btn').addEventListener('click', () => {
        const checked = document.querySelectorAll('input[name="ids[]"]:checked');
        if (checked.length === 0) {
            toast('Please select at least one record to delete', 'error', 2000);
            return;
        }
        bulkDeleteModal.classList.remove('hidden');
        bulkDeleteModal.style.display = 'flex';
        bulkDeleteConfirm.disabled = false;
        bulkDeleteSpinner.classList.add('hidden');
        bulkDeleteConfirm.focus();
    });

    bulkDeleteCancel.addEventListener('click', () => {
        bulkDeleteModal.classList.add('hidden');
        bulkDeleteModal.style.display = 'none';
    });

    bulkDeleteConfirm.addEventListener('click', async () => {
        bulkDeleteConfirm.disabled = true;
        bulkDeleteSpinner.classList.remove('hidden');

        try {
            const formData = new FormData(bulkDeleteForm);
            formData.append('_method', 'POST');
            const response = await fetch(bulkDeleteForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (response.ok) {
                toast('Selected egg records deleted successfully', 'success', 2000);
                setTimeout(() => location.reload(), 1000);
            } else {
                toast('Failed to delete selected records', 'error');
            }
        } catch (err) {
            console.error(err);
            toast('Network error. Please try again.', 'error');
        } finally {
            bulkDeleteModal.classList.add('hidden');
            bulkDeleteModal.style.display = 'none';
            bulkDeleteConfirm.disabled = false;
            bulkDeleteSpinner.classList.add('hidden');
        }
    });

    // Close modals on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!deleteModal.classList.contains('hidden')) {
                deleteModal.classList.add('hidden');
                deleteModal.style.display = 'none';
                currentDeleteUrl = null;
            }
            if (!bulkDeleteModal.classList.contains('hidden')) {
                bulkDeleteModal.classList.add('hidden');
                bulkDeleteModal.style.display = 'none';
            }
        }
    });

    // Chart.js setup
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('eggChart')?.getContext('2d');
        if (!ctx) {
            console.error('Canvas eggChart not found');
            return;
        }

        const labels = @json($eggLabels);
        const data = @json($eggData);

        if (!labels.length || !data.length) {
            console.warn('No data available for egg chart');
            return;
        }

        const isDark = document.documentElement.classList.contains('dark');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Egg Crates',
                    data: data,
                    backgroundColor: isDark ? 'rgba(59, 130, 246, 0.5)' : 'rgba(75, 192, 192, 0.5)',
                    borderColor: isDark ? 'rgba(59, 130, 246, 1)' : 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        title: { display: true, text: 'Crates', color: isDark ? '#d1d5db' : '#374151' },
                        ticks: { color: isDark ? '#d1d5db' : '#374151' }
                    },
                    x: { 
                        title: { display: true, text: 'Month', color: isDark ? '#d1d5db' : '#374151' },
                        ticks: { color: isDark ? '#d1d5db' : '#374151' }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Egg Crates (Last 6 Months)',
                        font: { size: 16 },
                        color: isDark ? '#d1d5db' : '#374151'
                    },
                    legend: {
                        labels: { color: isDark ? '#d1d5db' : '#374151' }
                    }
                }
            }
        });
    });
})();
</script>
@endpush
@endsection