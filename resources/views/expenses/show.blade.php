@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-600 dark:text-gray-300 mb-4" aria-label="Breadcrumb">
        <ol class="list-reset flex items-center space-x-2">
            <li><a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a></li>
            <li>/</li>
            <li><a href="{{ route('expenses.index') }}" class="hover:underline">Expenses</a></li>
            <li>/</li>
            <li class="text-gray-800 dark:text-gray-200 font-medium">Expense Details</li>
        </ol>
    </nav>

    <!-- Page header -->
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="rounded-lg bg-gradient-to-br from-indigo-600 to-sky-500 p-3 shadow-lg">
                <!-- receipt icon -->
                <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l2-2 4 4M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Expense — {{ $expense->category }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">Details and quick actions for this expense record</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded shadow">
                <!-- edit icon -->
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M16 3l5 5L8 21H3v-5L16 3z"/></svg>
                Edit
            </a>

            <button id="printBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18v4h12v-4M6 14h12v4H6z"/></svg>
                Print
            </button>

            <button id="deleteBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-7 4h10"/></svg>
                Delete
            </button>
        </div>
    </header>

    <!-- Main card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Details -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0f1724] p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Expense Information</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">A concise view of the expense with quick-copy features.</p>
                </div>
                <div class="text-sm text-gray-400 dark:text-gray-400">ID: <span class="font-medium text-gray-700 dark:text-gray-200">{{ $expense->id }}</span></div>
            </div>

            <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Category</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white">{{ $expense->category }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Amount</dt>
                    <dd class="mt-1 text-sm font-medium flex items-center gap-2 text-gray-800 dark:text-white">
                        <span id="amountText">₵ {{ number_format($expense->amount, 2) }}</span>
                        <button id="copyAmount" class="inline-flex items-center p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800" title="Copy amount">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V4a1 1 0 011-1h8a1 1 0 011 1v12a1 1 0 01-1 1h-3M8 7H6a2 2 0 00-2 2v8a2 2 0 002 2h8"/></svg>
                        </button>
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $expense->description ?? 'N/A' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Date</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ optional($expense->date)->format('Y-m-d') ?? 'N/A' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Recorded</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ optional($expense->created_at)->format('Y-m-d H:i') ?? 'N/A' }}</dd>
                </div>
            </dl>

            <!-- Additional notes / long description -->
            <div class="mt-6 border-t border-gray-100 dark:border-gray-800 pt-4">
                <h3 class="text-sm font-medium text-gray-800 dark:text-white">Notes</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $expense->notes ?? 'No additional notes.' }}</p>
            </div>
        </div>

        <!-- Right: Meta & actions -->
        <aside class="bg-white dark:bg-[#0b1220] p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Quick Summary</h3>

            <div class="mt-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Category</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $expense->category }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Amount</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">₵ {{ number_format($expense->amount, 2) }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Date</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ optional($expense->date)->format('Y-m-d') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30">{{ $expense->status ?? 'Recorded' }}</span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('expenses.index') }}" class="block text-center w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 rounded">Back to expenses</a>
                </div>
            </div>
        </aside>
    </div>

    <!-- Delete form (hidden, used by modal) -->
    <form id="deleteForm" action="{{ route('expenses.destroy', $expense) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="bg-white dark:bg-[#071428] rounded-lg shadow-xl p-6 z-10 w-full max-w-md">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Delete Expense</h4>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Are you sure you want to delete this expense? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end space-x-3">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded text-gray-800 dark:text-gray-100">Cancel</button>
                <button id="confirmDelete" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Copy amount to clipboard
    const copyBtn = document.getElementById('copyAmount');
    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            const text = document.getElementById('amountText').innerText.replace('₵','').trim();
            try {
                await navigator.clipboard.writeText(text);
                showToast('Amount copied to clipboard');
            } catch (e) {
                showToast('Unable to copy', true);
            }
        });
    }

    // Print handler
    const printBtn = document.getElementById('printBtn');
    if (printBtn) {
        printBtn.addEventListener('click', () => {
            window.print();
        });
    }

    // Delete modal logic
    const deleteBtn = document.getElementById('deleteBtn');
    const modal = document.getElementById('confirmModal');
    const cancelDelete = document.getElementById('cancelDelete');
    const confirmDelete = document.getElementById('confirmDelete');
    const deleteForm = document.getElementById('deleteForm');

    if (deleteBtn && modal) {
        deleteBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    }
    if (cancelDelete) {
        cancelDelete.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    }
    if (confirmDelete && deleteForm) {
        confirmDelete.addEventListener('click', (e) => {
            confirmDelete.setAttribute('disabled', true);
            confirmDelete.textContent = 'Deleting...';
            deleteForm.submit();
        });
    }

    // Simple toast
    function showToast(message, isError = false) {
        // create small ephemeral toast
        const t = document.createElement('div');
        t.className = 'fixed right-6 bottom-6 z-50 rounded-md px-4 py-2 shadow-lg ' + (isError ? 'bg-red-600 text-white' : 'bg-gray-900 text-white');
        t.innerText = message;
        document.body.appendChild(t);
        setTimeout(() => {
            t.classList.add('opacity-0');
            setTimeout(() => t.remove(), 400);
        }, 1600);
    }
});
</script>
@endpush
