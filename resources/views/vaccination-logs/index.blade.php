@extends('layouts.app')

@section('title', 'Vaccination Logs')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Toast Container -->
    <div id="toast-container" aria-live="polite" class="mb-4"></div>

    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Vaccination Logs</h2>
        {{-- @can('manage_vaccinations', 'admin') --}}
            <button type="button" id="create-btn" 
                    class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                           dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition"
                    aria-label="Add new vaccination log">
                <span class="mr-2" aria-hidden="true">➕</span> Add Vaccination
            </button>
        {{-- @endcan --}}
    </section>

    <!-- Summary Card -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center hover:shadow-lg transition-shadow">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Vaccinations</span>
                <p class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ number_format($logs->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
        </div>
    </section>

    <!-- Date Filter Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Vaccination Logs</h3>
            <form method="GET" action="{{ route('vaccination-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $start ?? now()->subMonths(6)->startOfMonth()->toDateString() }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                           aria-describedby="start_date-error">
                    @error('start_date')
                        <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $end ?? now()->endOfMonth()->toDateString() }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                           aria-describedby="end_date-error">
                    @error('end_date')
                        <p id="end_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition flex items-center justify-center"
                            aria-label="Apply date filters">
                        <span class="mr-2" aria-hidden="true">🔍</span> Filter Logs
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Vaccination Logs Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Vaccination Records</h3>
            <div id="vaccination-table-wrapper" aria-live="polite">
                @if ($logs->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">No vaccination logs found yet.</p>
                        @can('manage_vaccinations')
                            <button type="button" id="create-btn-empty" 
                                    class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                                           dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition"
                                    aria-label="Add your first vaccination log">
                                <span class="mr-2" aria-hidden="true">➕</span> Add Your First Vaccination Log
                            </button>
                        @endcan
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg">
                        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bird</th>
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Vaccine</th>
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date Administered</th>
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Notes</th>
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Next Vaccination</th>
                                    @can('manage_vaccinations')
                                        <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                        <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->bird ? $log->bird->name : 'Unknown Bird' }}</td>
                                        <td class="p-4 font-semibold text-teal-600 dark:text-teal-400">{{ $log->vaccine_name }}</td>
                                        <td class="p-4 text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($log->date_administered)->format('Y-m-d') }}</td>
                                        <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->notes ?? 'N/A' }}</td>
                                        <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->next_vaccination_date ? \Carbon\Carbon::parse($log->next_vaccination_date)->format('Y-m-d') : 'N/A' }}</td>
                                        @can('manage_vaccinations')
                                            <td class="p-4 flex space-x-2">
                                                <button type="button" data-id="{{ $log->id }}" 
                                                        data-url="{{ route('vaccination-logs.edit', $log->id) }}"
                                                        data-bird="{{ $log->bird ? $log->bird->name : 'Unknown Bird' }}"
                                                        data-vaccine="{{ $log->vaccine_name }}"
                                                        data-date="{{ \Carbon\Carbon::parse($log->date_administered)->format('Y-m-d') }}"
                                                        data-notes="{{ $log->notes ?? '' }}"
                                                        data-next-date="{{ $log->next_vaccination_date ? \Carbon\Carbon::parse($log->next_vaccination_date)->format('Y-m-d') : '' }}"
                                                        class="edit-btn inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs focus:ring-2 focus:ring-yellow-500 transition" 
                                                        aria-label="Edit vaccination log {{ $log->id }}">
                                                    <span class="mr-2" aria-hidden="true">✏️</span> Edit
                                                </button>
                                                <button type="button" data-id="{{ $log->id }}" data-url="{{ route('vaccination-logs.destroy', $log->id) }}" 
                                                        class="delete-btn inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs focus:ring-2 focus:ring-red-500 transition" 
                                                        aria-label="Delete vaccination log {{ $log->id }}">
                                                    <span class="mr-2" aria-hidden="true">🗑</span> Delete
                                                </button>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $logs->hasPages())
                        <div class="mt-6 flex justify-between items-center">
                            <div class="flex space-x-2">
                                <a href="{{ $logs->previousPageUrl() }}" 
                                   class="pagination-link inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ $logs->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                   aria-label="Previous page" {{ $logs->onFirstPage() ? 'disabled' : '' }} data-ajax="true">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                </a>
                                <a href="{{ $logs->nextPageUrl() }}" 
                                   class="pagination-link inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ !$logs->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                   aria-label="Next page" {{ !$logs->hasMorePages() ? 'disabled' : '' }} data-ajax="true">
                                    Next
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
                            </span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>

    <!-- Create Modal -->
    @can('manage_vaccinations')
        <div id="create-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Add Vaccination Log</h3>
                <form id="create-form" method="POST" action="{{ route('vaccination-logs.store') }}">
                    @csrf
                    <div class="space-y-4 mt-2">
                        <div>
                            <label for="create_bird_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bird</label>
                            <select id="create_bird_id" name="bird_id" required 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                    aria-describedby="create_bird_id-error">
                                <option value="">Select a Bird</option>
                                @foreach ($birds ?? [] as $bird)
                                    <option value="{{ $bird->id }}">{{ $bird->name }}</option>
                                @endforeach
                            </select>
                            <p id="create_bird_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="create_vaccine_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vaccine Name</label>
                            <input type="text" id="create_vaccine_name" name="vaccine_name" required 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                   aria-describedby="create_vaccine_name-error">
                            <p id="create_vaccine_name-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="create_date_administered" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Administered</label>
                            <input type="date" id="create_date_administered" name="date_administered" required 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                   aria-describedby="create_date_administered-error">
                            <p id="create_date_administered-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="create_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea id="create_notes" name="notes" rows="3" 
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                      aria-describedby="create_notes-error"></textarea>
                            <p id="create_notes-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="create_next_vaccination_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Next Vaccination Date</label>
                            <input type="date" id="create_next_vaccination_date" name="next_vaccination_date" 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                   aria-describedby="create_next_vaccination_date-error">
                            <p id="create_next_vaccination_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" id="create-cancel" 
                                class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition" 
                                aria-label="Cancel create">Cancel</button>
                        <button type="submit" id="create-confirm" 
                                class="px-4 py-2 rounded bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                                aria-label="Confirm create">
                            <span class="flex items-center">
                                <svg id="create-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                                Create
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <!-- Edit Modal -->
    @can('manage_vaccinations')
        <div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Edit Vaccination Log</h3>
                <form id="edit-form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="space-y-4 mt-2">
                        <div>
                            <label for="edit_bird_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bird</label>
                            <select id="edit_bird_id" name="bird_id" required 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                    aria-describedby="edit_bird_id-error">
                                <option value="">Select a Bird</option>
                                @foreach ($birds ?? [] as $bird)
                                    <option value="{{ $bird->id }}">{{ $bird->name }}</option>
                                @endforeach
                            </select>
                            <p id="edit_bird_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="edit_vaccine_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vaccine Name</label>
                            <input type="text" id="edit_vaccine_name" name="vaccine_name" required 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                   aria-describedby="edit_vaccine_name-error">
                            <p id="edit_vaccine_name-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="edit_date_administered" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Administered</label>
                            <input type="date" id="edit_date_administered" name="date_administered" required 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                   aria-describedby="edit_date_administered-error">
                            <p id="edit_date_administered-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="edit_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea id="edit_notes" name="notes" rows="3" 
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                      aria-describedby="edit_notes-error"></textarea>
                            <p id="edit_notes-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="edit_next_vaccination_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Next Vaccination Date</label>
                            <input type="date" id="edit_next_vaccination_date" name="next_vaccination_date" 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition"
                                   aria-describedby="edit_next_vaccination_date-error">
                            <p id="edit_next_vaccination_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1 hidden"></p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" id="edit-cancel" 
                                class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition" 
                                aria-label="Cancel edit">Cancel</button>
                        <button type="submit" id="edit-confirm" 
                                class="px-4 py-2 rounded bg-yellow-500 text-white text-sm font-medium hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition" 
                                aria-label="Confirm edit">
                            <span class="flex items-center">
                                <svg id="edit-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                                Update
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <!-- Delete Confirmation Modal -->
    @can('manage_vaccinations')
        <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-modal="true" role="dialog">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this vaccination log? This action cannot be undone.</p>
                <div class="mt-4 flex justify-end gap-2">
                    <button id="delete-cancel" 
                            class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition" 
                            aria-label="Cancel delete">Cancel</button>
                    <button id="delete-confirm" 
                            class="px-4 py-2 rounded bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition" 
                            aria-label="Confirm delete" disabled>
                        <span class="flex items-center">
                            <svg id="delete-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                            Delete
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endcan
</div>

@push('scripts')
<script>
(function () {
    // Elements
    const toastContainer = document.getElementById('toast-container');
    const createModal = document.getElementById('create-modal');
    const createForm = document.getElementById('create-form');
    const createCancel = document.getElementById('create-cancel');
    const createConfirm = document.getElementById('create-confirm');
    const createSpinner = document.getElementById('create-spinner');
    const editModal = document.getElementById('edit-modal');
    const editForm = document.getElementById('edit-form');
    const editCancel = document.getElementById('edit-cancel');
    const editConfirm = document.getElementById('edit-confirm');
    const editSpinner = document.getElementById('edit-spinner');
    const deleteModal = document.getElementById('delete-modal');
    const deleteCancel = document.getElementById('delete-cancel');
    const deleteConfirm = document.getElementById('delete-confirm');
    const deleteSpinner = document.getElementById('delete-spinner');

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
    async function extractFragmentFromHtml(htmlText, selector) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');
        const fragment = doc.querySelector(selector);
        return fragment ? fragment.innerHTML : null;
    }

    // reloadTable: fetches page and replaces table wrapper
    async function reloadTable(url = null, selector = '#vaccination-table-wrapper') {
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
            const newInner = await extractFragmentFromHtml(text, selector);
            if (!newInner) {
                toast('Failed to refresh table. Reloading page...', 'error', 3000);
                setTimeout(() => window.location.reload(), 1200);
                return;
            }
            const wrapper = document.querySelector(selector);
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
        // Create buttons
        document.querySelectorAll('#create-btn, #create-btn-empty').forEach(btn => {
            btn.removeEventListener('click', createBtnClickHandler);
            btn.addEventListener('click', createBtnClickHandler);
        });

        // Edit buttons
        document.querySelectorAll('#vaccination-table-wrapper .edit-btn').forEach(btn => {
            btn.removeEventListener('click', editBtnClickHandler);
            btn.addEventListener('click', editBtnClickHandler);
        });

        // Delete buttons
        document.querySelectorAll('#vaccination-table-wrapper .delete-btn').forEach(btn => {
            btn.removeEventListener('click', deleteBtnClickHandler);
            btn.addEventListener('click', deleteBtnClickHandler);
        });

        // Pagination links
        document.querySelectorAll('#vaccination-table-wrapper .pagination-link').forEach(link => {
            link.removeEventListener('click', ajaxPageClickHandler);
            link.addEventListener('click', ajaxPageClickHandler);
        });
    }

    // Create handler
    function createBtnClickHandler() {
        createForm.reset();
        document.querySelectorAll('#create-form .text-red-600').forEach(el => el.classList.add('hidden'));
        createModal.classList.remove('hidden');
        createModal.style.display = 'flex';
        createConfirm.disabled = false;
        createSpinner.classList.add('hidden');
        document.getElementById('create_bird_id').focus();
        trapFocus(createModal);
    }

    createCancel.addEventListener('click', () => {
        createModal.classList.add('hidden');
        createModal.style.display = 'none';
        releaseFocus();
    });

    createForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        createConfirm.disabled = true;
        createSpinner.classList.remove('hidden');
        document.querySelectorAll('#create-form .text-red-600').forEach(el => el.classList.add('hidden'));

        try {
            const formData = new FormData(createForm);
            const response = await fetch(createForm.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (response.ok && data.success !== false) {
                toast(data.message || 'Vaccination log created', 'success', 2000);
                createModal.classList.add('hidden');
                createModal.style.display = 'none';
                await reloadTable();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorEl = document.getElementById(`create_${key}-error`);
                        if (errorEl) {
                            errorEl.textContent = data.errors[key][0];
                            errorEl.classList.remove('hidden');
                        }
                    });
                    toast('Please correct the errors in the form.', 'error', 3000);
                } else {
                    toast(data.message || 'Failed to create vaccination log', 'error', 3000);
                }
            }
        } catch (err) {
            console.error('Create error:', err);
            toast('Network error. Please try again.', 'error', 3000);
        } finally {
            createConfirm.disabled = false;
            createSpinner.classList.add('hidden');
        }
    });

    // Edit handler
    let currentEditUrl = null;
    function editBtnClickHandler(e) {
        const btn = e.currentTarget;
        currentEditUrl = btn.dataset.url;
        editForm.reset();
        document.querySelectorAll('#edit-form .text-red-600').forEach(el => el.classList.add('hidden'));

        // Populate form fields
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_bird_id').value = ''; // Requires bird_id mapping
        document.getElementById('edit_vaccine_name').value = btn.dataset.vaccine;
        document.getElementById('edit_date_administered').value = btn.dataset.date;
        document.getElementById('edit_notes').value = btn.dataset.notes;
        document.getElementById('edit_next_vaccination_date').value = btn.dataset.nextDate;
        editForm.action = currentEditUrl;

        editModal.classList.remove('hidden');
        editModal.style.display = 'flex';
        editConfirm.disabled = false;
        editSpinner.classList.add('hidden');
        document.getElementById('edit_bird_id').focus();
        trapFocus(editModal);
    }

    editCancel.addEventListener('click', () => {
        editModal.classList.add('hidden');
        editModal.style.display = 'none';
        currentEditUrl = null;
        releaseFocus();
    });

    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!currentEditUrl) return;
        editConfirm.disabled = true;
        editSpinner.classList.remove('hidden');
        document.querySelectorAll('#edit-form .text-red-600').forEach(el => el.classList.add('hidden'));

        try {
            const formData = new FormData(editForm);
            const response = await fetch(currentEditUrl, {
                method: 'PUT',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (response.ok && data.success !== false) {
                toast(data.message || 'Vaccination log updated', 'success', 2000);
                editModal.classList.add('hidden');
                editModal.style.display = 'none';
                await reloadTable();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorEl = document.getElementById(`edit_${key}-error`);
                        if (errorEl) {
                            errorEl.textContent = data.errors[key][0];
                            errorEl.classList.remove('hidden');
                        }
                    });
                    toast('Please correct the errors in the form.', 'error', 3000);
                } else {
                    toast(data.message || 'Failed to update vaccination log', 'error', 3000);
                }
            }
        } catch (err) {
            console.error('Edit error:', err);
            toast('Network error. Please try again.', 'error', 3000);
        } finally {
            editConfirm.disabled = false;
            editSpinner.classList.add('hidden');
        }
    });

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
        trapFocus(deleteModal);
    }

    deleteCancel.addEventListener('click', () => {
        deleteModal.classList.add('hidden');
        deleteModal.style.display = 'none';
        currentDeleteUrl = null;
        releaseFocus();
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
                const msg = data ? (data.message ?? 'Vaccination log deleted') : 'Vaccination log deleted';
                if (ok) {
                    toast(msg, 'success', 2000);
                    await reloadTable();
                } else {
                    toast(data.message || 'Failed to delete vaccination log', 'error', 3000);
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
            releaseFocus();
        }
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
                if (!createModal.classList.contains('hidden')) createCancel.click();
                if (!editModal.classList.contains('hidden')) editCancel.click();
                if (!deleteModal.classList.contains('hidden')) deleteCancel.click();
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
        const modals = [createModal, editModal, deleteModal];
        modals.forEach(m => m && m.removeEventListener('keydown', trapHandler));
        if (_previousActive) _previousActive.focus();
        _previousActive = null;
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