@extends('layouts.app')

@section('title', 'Vaccination Logs')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-6 bg-gray-100 dark:bg-gray-900 dark:text-white">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Vaccination Logs</h2>
        <a href="{{ route('vaccination-logs.create') }}" class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" aria-label="Add new vaccination log">
            <span class="mr-2">➕</span> Add Vaccination
        </a>
    </div>

    <!-- Summary Card -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow flex justify-center items-center gap-4">
        <span class="text-sm text-gray-500 dark:text-gray-400">Total Vaccinations</span>
        <p class="text-2xl font-bold text-teal-600 dark:text-teal-400">{{ $logs->total() }}</p>
        <span class="text-gray-600 dark:text-gray-300">Records</span>
    </div>

    <!-- Date Filter Form -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Vaccination Logs</h3>
        <form method="GET" action="{{ route('vaccination-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $start }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('start_date')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $end }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('end_date')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" aria-label="Apply date filters">
                    <span class="mr-2">🔍</span> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Vaccination Logs Table -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Vaccination Records</h3>
        <div id="vaccination-table-wrapper">
            @if ($logs->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No vaccination logs found.</p>
                    <a href="{{ route('vaccination-logs.create') }}" class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition" aria-label="Add your first vaccination log">
                        <span class="mr-2">➕</span> Add First Log
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-3 text-left text-gray-700 dark:text-gray-200">Bird</th>
                                <th class="p-3 text-left text-gray-700 dark:text-gray-200">Vaccine</th>
                                <th class="p-3 text-left text-gray-700 dark:text-gray-200">Date Administered</th>
                                <th class="p-3 text-left text-gray-700 dark:text-gray-200">Notes</th>
                                <th class="p-3 text-left text-gray-700 dark:text-gray-200">Next Vaccination</th>
                                <th class="p-3 text-left text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="p-3">{{ $log->bird ? $log->bird->name : 'Unknown' }}</td>
                                    <td class="p-3 font-semibold text-teal-600 dark:text-teal-400">{{ $log->vaccine_name }}</td>
                                    <td class="p-3">{{ \Carbon\Carbon::parse($log->date_administered)->format('Y-m-d') }}</td>
                                    <td class="p-3">{{ $log->notes ?? 'N/A' }}</td>
                                    <td class="p-3">{{ $log->next_vaccination_date ? \Carbon\Carbon::parse($log->next_vaccination_date)->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="p-3 flex space-x-2">
                                        <a href="{{ route('vaccination-logs.edit', $log->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-xs focus:ring-2 focus:ring-yellow-500" aria-label="Edit vaccination log {{ $log->id }}">✏️ Edit</a>
                                        <button data-id="{{ $log->id }}" data-url="{{ route('vaccination-logs.destroy', $log->id) }}" class="delete-btn px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs focus:ring-2 focus:ring-red-500" aria-label="Delete vaccination log {{ $log->id }}">🗑 Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($logs->hasPages())
                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <a href="{{ $logs->previousPageUrl() }}" class="pagination-link px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 {{ $logs->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}" aria-label="Previous page">← Previous</a>
                            <a href="{{ $logs->nextPageUrl() }}" class="pagination-link px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 {{ !$logs->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}" aria-label="Next page">Next →</a>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this vaccination log? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="delete-cancel" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 focus:ring-2 focus:ring-gray-500" aria-label="Cancel delete">Cancel</button>
                <button id="delete-confirm" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500" aria-label="Confirm delete" disabled>
                    <span class="flex items-center">
                        <svg id="delete-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                        Delete
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const toastContainer = document.getElementById('toast-container');
    const deleteModal = document.getElementById('delete-modal');
    const deleteCancel = document.getElementById('delete-cancel');
    const deleteConfirm = document.getElementById('delete-confirm');
    const deleteSpinner = document.getElementById('delete-spinner');

    function toast(message, type = 'info') {
        const id = 't-' + Date.now();
        const colors = { info: 'bg-blue-600', success: 'bg-green-600', error: 'bg-red-600' };
        const el = document.createElement('div');
        el.id = id;
        el.className = `px-4 py-2 rounded shadow text-white ${colors[type]} max-w-sm flex justify-between items-center`;
        el.innerHTML = `<span>${message}</span><button class="ml-4 hover:text-gray-200" aria-label="Dismiss toast">✕</button>`;
        toastContainer.appendChild(el);
        el.querySelector('button').addEventListener('click', () => el.remove());
        setTimeout(() => el.remove(), 3000);
    }

    @if (session('success'))
        toast('{{ session('success') }}', 'success');
    @endif
    @if (session('error'))
        toast('{{ session('error') }}', 'error');
    @endif

    async function reloadTable(url = window.location.href) {
        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            });
            const text = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const wrapper = document.getElementById('vaccination-table-wrapper');
            wrapper.innerHTML = doc.querySelector('#vaccination-table-wrapper').innerHTML;
            initTableControls();
        } catch (err) {
            toast('Failed to refresh table.', 'error');
        }
    }

    function initTableControls() {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.removeEventListener('click', deleteBtnClickHandler);
            btn.addEventListener('click', deleteBtnClickHandler);
        });
        document.querySelectorAll('.pagination-link').forEach(link => {
            link.removeEventListener('click', paginationClickHandler);
            link.addEventListener('click', paginationClickHandler);
        });
    }

    function deleteBtnClickHandler(e) {
        deleteConfirm.dataset.url = e.currentTarget.dataset.url;
        deleteModal.classList.remove('hidden');
        deleteConfirm.disabled = false;
        deleteSpinner.classList.add('hidden');
        deleteConfirm.focus();
    }

    deleteCancel.addEventListener('click', () => {
        deleteModal.classList.add('hidden');
        deleteConfirm.dataset.url = '';
    });

    deleteConfirm.addEventListener('click', async () => {
        const url = deleteConfirm.dataset.url;
        if (!url) return;
        deleteConfirm.disabled = true;
        deleteSpinner.classList.remove('hidden');
        try {
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (res.ok && data.success) {
                toast(data.message, 'success');
                await reloadTable();
            } else {
                toast(data.message || 'Failed to delete vaccination log', 'error');
            }
        } catch (err) {
            toast('Network error. Please try again.', 'error');
        } finally {
            deleteModal.classList.add('hidden');
            deleteConfirm.disabled = false;
            deleteSpinner.classList.add('hidden');
            deleteConfirm.dataset.url = '';
        }
    });

    function paginationClickHandler(e) {
        e.preventDefault();
        if (e.currentTarget.classList.contains('opacity-50')) return;
        reloadTable(e.currentTarget.href);
    }

    document.addEventListener('DOMContentLoaded', initTableControls);
})();
</script>
@endpush
@endsection