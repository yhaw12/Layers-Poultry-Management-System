@extends('layouts.app')

@section('content')
@role('admin')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Activity Logs</h2>

    <!-- Filters / actions -->
    <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3 items-end" id="filtersForm">
        <div class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs (user, action, details)..." class="flex-1 border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">Filter</button>
            <button type="button" onclick="clearFilters()" class="bg-gray-300 text-gray-700 py-2 px-3 rounded hover:bg-gray-400 dark:bg-gray-700 dark:text-gray-200">Clear</button>
        </div>

        <div class="flex gap-2">
            <input type="date" name="start_date" value="{{ $start ?? now()->startOfMonth()->format('Y-m-d') }}" class="border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
            <input type="date" name="end_date" value="{{ $end ?? now()->endOfMonth()->format('Y-m-d') }}" class="border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
        </div>

        <div class="flex gap-2 items-center">
            <select name="user_id" class="border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                <option value="">All users</option>
                @if(!empty($users))
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                @endif
            </select>

            <select name="action" class="border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                <option value="">All actions</option>
                @if(!empty($actions))
                    @foreach($actions as $act)
                        <option value="{{ $act }}" @selected(request('action') == $act)>{{ $act }}</option>
                    @endforeach
                @endif
            </select>

            <select name="per_page" onchange="document.getElementById('filtersForm').submit()" class="border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @php $per = request('per_page', 15); @endphp
                <option value="10" @selected($per==10)>10</option>
                <option value="15" @selected($per==15)>15</option>
                <option value="25" @selected($per==25)>25</option>
                <option value="50" @selected($per==50)>50</option>
            </select>

            <!-- Export buttons: preserve filters -->
            <div class="ml-auto flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="text-sm inline-flex items-center gap-2 border px-3 py-2 rounded hover:bg-gray-50 dark:bg-[#0e1229] dark:border-gray-700">
                    Export CSV
                </a>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="text-sm inline-flex items-center gap-2 border px-3 py-2 rounded hover:bg-gray-50 dark:bg-[#0e1229] dark:border-gray-700">
                    Export PDF
                </a>
            </div>
        </div>
    </form>

    <!-- Table card -->
    <div class="bg-white dark:bg-[#0f1228] p-4 rounded-2xl shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full table-auto text-left border-collapse">
                <thead class="sticky top-0 bg-white/90 dark:bg-[#0f1228]/95 backdrop-blur z-10">
                    <tr class="text-gray-700 dark:text-gray-300">
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">User</th>
                        <th class="px-3 py-2">Action</th>
                        <th class="px-3 py-2">Details</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#08102a] transition-colors">
                            <td class="px-3 py-3 align-top text-sm">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-3 py-3 align-top text-sm">{{ $log->user ? $log->user->name : 'System' }}</td>
                            <td class="px-3 py-3 align-top text-sm font-medium">{{ $log->action }}</td>

                            <!-- Truncated details with show more -->
                            <td class="px-3 py-3 align-top text-sm">
                                @php $short = \Illuminate\Support\Str::limit($log->details, 120); @endphp
                                <div id="details_short_{{ $log->id }}" class="break-words">{{ $short }}</div>
                                @if(strlen($log->details) > 120)
                                    <button type="button" onclick="showFullDetails('{{ addslashes($log->details) }}')" class="text-xs text-blue-600 hover:underline mt-1">Show more</button>
                                @endif
                            </td>

                            <td class="px-3 py-3 align-top text-sm text-right">
                                <button type="button" onclick="copyText(`{{ addslashes($log->details) }}`)" title="Copy details" class="inline-flex items-center gap-2 px-3 py-1 border rounded text-xs hover:bg-gray-50 dark:bg-[#0b1122] dark:border-gray-700">
                                    Copy
                                </button>

                                <button type="button" onclick="openPreview({{ $log->id }}, `{{ addslashes($log->details) }}`, `{{ $log->user ? addslashes($log->user->name) : 'System' }}`, `{{ $log->created_at->format('Y-m-d H:i:s') }}`)" class="inline-flex items-center gap-2 px-3 py-1 border rounded text-xs ml-2 hover:bg-gray-50 dark:bg-[#0b1122] dark:border-gray-700">
                                    Preview
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="mx-auto max-w-md">
                                    <svg width="64" height="64" viewBox="0 0 24 24" class="mx-auto mb-4 opacity-60" fill="none" stroke="currentColor"><path d="M3 7h18M3 12h18M3 17h18" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="text-lg font-medium">No logs found.</p>
                                    <p class="text-sm mt-2">Try changing filters or clearing the search.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Modal / Preview -->
<div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/50" onclick="closePreview()"></div>
    <div class="bg-white dark:bg-[#07102a] rounded-lg shadow-xl max-w-3xl w-full mx-4 z-10 overflow-hidden">
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
            <div>
                <h3 id="previewUser" class="text-lg font-semibold dark:text-white">User</h3>
                <div id="previewDate" class="text-xs text-gray-500 dark:text-gray-400">Date</div>
            </div>
            <button class="px-3 py-1 rounded border" onclick="closePreview()">Close</button>
        </div>
        <div class="p-4">
            <pre id="previewDetails" class="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200"></pre>
        </div>
    </div>
</div>

<!-- Small inline scripts (vanilla JS) -->
<script>
function clearFilters(){
    const url = new URL(window.location.href);
    ['search','start_date','end_date','user_id','action','per_page'].forEach(k => url.searchParams.delete(k));
    window.location.href = url.toString();
}

function copyText(text){
    if (!navigator.clipboard) {
        // fallback
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
        alert('Copied to clipboard');
        return;
    }
    navigator.clipboard.writeText(text).then(() => {
        const el = document.createElement('div');
        el.innerText = 'Copied';
        document.body.appendChild(el);
        setTimeout(()=>el.remove(), 800);
    }).catch(()=>alert('Copy failed'));
}

function showFullDetails(details){
    // Simple modal used for long text
    openPreview('preview', details, 'Details', new Date().toISOString());
}

function openPreview(id, details, user, date){
    document.getElementById('previewUser').innerText = user;
    document.getElementById('previewDate').innerText = date;
    document.getElementById('previewDetails').innerText = details;
    const modal = document.getElementById('previewModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePreview(){
    const modal = document.getElementById('previewModal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}
</script>

@else
<div class="container mx-auto px-4 py-8 text-center text-red-600 dark:text-red-400">
    <p>You do not have permission to view activity logs.</p>
</div>
@endrole
@endsection
