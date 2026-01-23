@extends('layouts.app')

@section('content')
@role('admin')
{{-- Main Container: Keeps your original colors --}}
<div class="w-full max-w-7xl mx-auto px-3 py-4 sm:py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white min-h-screen overflow-x-hidden">
    
    <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-white mb-4 sm:mb-6">Activity Logs</h2>

    <form method="GET" action="{{ request()->url() }}" class="space-y-3 mb-6" id="filtersForm">
        
        {{-- Top Row: Search & Actions --}}
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." 
                class="w-full border rounded-lg p-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm" />
            
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Filter</button>
                <button type="button" onclick="clearFilters()" class="flex-1 sm:flex-none bg-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-400 dark:bg-gray-700 dark:text-gray-200 transition">Clear</button>
            </div>
        </div>

        {{-- Second Row: Dates (Side-by-side on mobile) --}}
        <div class="flex gap-2">
            <div class="flex flex-col flex-1 min-w-0">
                <label class="text-[9px] uppercase font-bold text-gray-500 dark:text-gray-400 mb-1 ml-1">Start</label>
                <input type="date" name="start_date" value="{{ $start ?? now()->startOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 text-xs dark:bg-gray-800 dark:border-gray-600 dark:text-white shadow-sm" />
            </div>
            <div class="flex flex-col flex-1 min-w-0">
                <label class="text-[9px] uppercase font-bold text-gray-500 dark:text-gray-400 mb-1 ml-1">End</label>
                <input type="date" name="end_date" value="{{ $end ?? now()->endOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 text-xs dark:bg-gray-800 dark:border-gray-600 dark:text-white shadow-sm" />
            </div>
        </div>

        {{-- Third Row: Selects (Stacked on mobile) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <select name="user_id" class="w-full border rounded-lg p-2 text-xs dark:bg-gray-800 dark:border-gray-600 dark:text-white shadow-sm">
                <option value="">User: All</option>
                @if(!empty($users))
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ Str::limit($u->name, 12) }}</option>
                    @endforeach
                @endif
            </select>

            <select name="action" class="w-full border rounded-lg p-2 text-xs dark:bg-gray-800 dark:border-gray-600 dark:text-white shadow-sm">
                <option value="">Action: All</option>
                @if(!empty($actions))
                    @foreach($actions as $act)
                        <option value="{{ $act }}" @selected(request('action') == $act)>{{ $act }}</option>
                    @endforeach
                @endif
            </select>
            
            <select name="per_page" onchange="document.getElementById('filtersForm').submit()" class="w-full border rounded-lg p-2 text-xs dark:bg-gray-800 dark:border-gray-600 dark:text-white shadow-sm">
                @php $per = request('per_page', 15); @endphp
                <option value="10" @selected($per==10)>10 / page</option>
                <option value="15" @selected($per==15)>15 / page</option>
                <option value="25" @selected($per==25)>25 / page</option>
            </select>

            <div class="flex border rounded-lg overflow-hidden shadow-sm h-[34px] border-gray-200 dark:border-gray-600">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="flex-1 flex items-center justify-center bg-white dark:bg-[#0e1229] hover:bg-gray-50 dark:hover:bg-gray-800 border-r dark:border-gray-600 text-[10px] font-bold uppercase dark:text-white">CSV</a>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="flex-1 flex items-center justify-center bg-white dark:bg-[#0e1229] hover:bg-gray-50 dark:hover:bg-gray-800 text-[10px] font-bold uppercase dark:text-white">PDF</a>
            </div>
        </div>
    </form>

    {{-- Table Container --}}
    <div class="bg-white dark:bg-[#0f1228] rounded-xl shadow-sm border dark:border-gray-800 overflow-hidden">
        {{-- Horizontal Scroll for Table --}}
        <div class="overflow-x-auto w-full no-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-[#161b36] text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                    <tr>
                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider w-16">Date</th>
                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider">User</th>
                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider">Action</th>
                        {{-- Hide Details on Mobile --}}
                        <th class="hidden sm:table-cell px-3 py-2 text-[10px] font-bold uppercase tracking-wider">Details</th>
                        <th class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-right">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#08102a] transition-colors group">
                            
                            {{-- Date --}}
                            <td class="px-3 py-2.5 text-xs leading-tight dark:text-gray-300">
                                <div class="font-bold">{{ $log->created_at->format('M d') }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->format('H:i') }}</div>
                            </td>
                            
                            {{-- User --}}
                            <td class="px-3 py-2.5 text-xs font-medium dark:text-white">
                                {{ $log->user ? Str::limit($log->user->name, 12) : 'System' }}
                            </td>
                            
                            {{-- Action --}}
                            <td class="px-3 py-2.5">
                                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 border border-blue-100 dark:border-blue-800/50">
                                    {{ Str::limit($log->action, 10) }}
                                </span>
                            </td>
                            
                            {{-- Details (Hidden on mobile) --}}
                            <td class="hidden sm:table-cell px-3 py-2.5 text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate">
                                {{ $log->details }}
                            </td>
                            
                            {{-- View Button --}}
                            <td class="px-3 py-2.5 text-right">
                                <button onclick="openPreview({{ $log->id }}, `{{ addslashes($log->details) }}`, `{{ $log->user ? addslashes($log->user->name) : 'System' }}`, `{{ $log->created_at->format('Y-m-d H:i:s') }}`)" 
                                    class="text-blue-600 dark:text-blue-400 p-1.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 8.142 1.98 10.336 6.41.147.301.147.645 0 .947C18.142 15.02 14.257 17 10 17c-4.257 0-8.142-1.98-10.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-xs text-gray-400 italic">No logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-3 py-3 bg-gray-50 dark:bg-[#161b36] border-t dark:border-gray-700">
            {{ $logs->withQueryString()->links('pagination::tailwind') }} 
        </div>
        @endif
    </div>
</div>

{{-- Responsive Modal: Uses original colors --}}
<div id="previewModal" class="fixed inset-0 z-[100] hidden items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm transition-opacity" onclick="closePreview()"></div>
    
    <div class="relative bg-white dark:bg-[#0f1228] w-full sm:max-w-lg rounded-t-xl sm:rounded-xl shadow-2xl flex flex-col max-h-[85vh] sm:max-h-[600px] overflow-hidden transform transition-all animate-slide-up">
        
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-[#0b1122]">
            <div>
                <h3 id="previewUser" class="text-sm font-bold dark:text-white">User Name</h3>
                <div id="previewDate" class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">Date</div>
            </div>
            <button class="p-2 text-gray-400 hover:text-red-500 dark:text-gray-300" onclick="closePreview()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </div>

        <div class="p-4 overflow-y-auto bg-white dark:bg-[#0f1228]">
            <div class="bg-gray-100 dark:bg-[#08102a] p-3 rounded-lg border dark:border-gray-700">
                <pre id="previewDetails" class="whitespace-pre-wrap text-xs font-mono text-gray-700 dark:text-gray-300 break-all leading-relaxed"></pre>
            </div>
        </div>

        <div class="p-3 border-t dark:border-gray-700 flex justify-end gap-3 bg-gray-50 dark:bg-[#0b1122]">
            <button onclick="copyModalText()" class="px-3 py-1.5 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg text-xs font-medium text-gray-700 dark:text-white shadow-sm">Copy</button>
            <button onclick="closePreview()" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold shadow-md">Close</button>
        </div>
    </div>
</div>

<script>
    // Keeping utility functions exactly the same
    const clearFilters = () => window.location.href = "{{ request()->url() }}";
    
    function openPreview(id, details, user, date){
        document.getElementById('previewUser').innerText = user;
        document.getElementById('previewDate').innerText = date;
        document.getElementById('previewDetails').innerText = details;
        
        const modal = document.getElementById('previewModal');
        modal.classList.remove('hidden'); 
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closePreview(){
        const modal = document.getElementById('previewModal');
        modal.classList.add('hidden'); 
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function copyModalText() {
        const text = document.getElementById('previewDetails').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.currentTarget;
            const originalText = btn.innerText;
            btn.innerText = "Copied!";
            btn.classList.add('text-green-600', 'dark:text-green-400');
            setTimeout(() => {
                btn.innerText = originalText;
                btn.classList.remove('text-green-600', 'dark:text-green-400');
            }, 1500);
        });
    }
</script>

<style>
    @keyframes slide-up {
        0% { transform: translateY(100%); }
        100% { transform: translateY(0); }
    }
    .animate-slide-up { animation: slide-up 0.3s ease-out forwards; }
    /* Hide scrollbar for clean look */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@else
<div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-[#0a0a23]">
    <p class="text-red-500 font-bold bg-red-50 dark:bg-red-900/20 px-4 py-2 rounded-lg border border-red-100 dark:border-red-800">Access Denied.</p>
</div>
@endrole
@endsection