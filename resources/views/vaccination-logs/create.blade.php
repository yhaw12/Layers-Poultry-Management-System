@extends('layouts.app')

@section('title', 'Add Vaccination Log')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Vaccination Log</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            @if (session('success') || session('error'))
                <div class="p-4 {{ session('success') ? 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400' }} rounded-lg flex items-center mb-6">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ session('success') ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}" />
                    </svg>
                    {{ session('success') ?? session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('vaccination-logs._form', [
                'action' => route('vaccination-logs.store'),
                'birds' => $birds,
                'log' => new \App\Models\VaccinationLog(),
                'submitText' => 'Save',
                'formId' => 'create-form'
            ])
        </div>
    </section>
</div>

@push('scripts')
<script>
(() => {
    const toastContainer = document.getElementById('toast-container');

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

    document.getElementById('create-form').addEventListener('submit', (e) => {
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('#form-spinner');
        submitBtn.disabled = true;
        spinner.classList.remove('hidden');
        setTimeout(() => {
            submitBtn.disabled = false;
            spinner.classList.add('hidden');
        }, 2000);
    });
})();
</script>
@endpush
@endsection