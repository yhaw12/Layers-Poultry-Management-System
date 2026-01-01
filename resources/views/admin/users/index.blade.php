@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Users</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage application users, roles, and permissions.</p>
        </div>

        <a href="{{ route('users.create') }}" class="inline-flex items-center bg-blue-600 text-white py-2 px-4 rounded-lg shadow hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New
        </a>
    </div>

    {{-- Filters Toolbar --}}
    <div class="bg-white dark:bg-[#0b1220] p-4 rounded-lg shadow mb-6">
        <form method="GET" class="flex flex-col md:flex-row md:items-center md:space-x-4 gap-3">
            <div class="flex-1">
                <input name="q" type="search" value="{{ request('q') }}" placeholder="Search by name or email..." class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex items-center gap-2">
                @if(isset($roles))
                    <select name="role" class="p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="">All roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Filter</button>
                <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table Identity Section --}}
    <div class="bg-white dark:bg-[#0b1220] shadow rounded-lg overflow-hidden border dark:border-gray-800">
        <div class="overflow-x-auto">
            @if ($users->count())
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">User</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hidden md:table-cell">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Role(s)</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900" id="user-row-{{ $user->id }}">
                                <td class="px-4 py-3 align-middle">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold uppercase">{{ substr($user->name, 0, 1) }}</div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500 md:hidden">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                <td class="px-4 py-3 user-roles-cell" data-user-id="{{ $user->id }}">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($user->roles as $role)
                                            <span class="px-2 py-1 text-xs font-medium rounded {{ $role->name === 'admin' ? 'bg-green-100 text-green-800' : 'bg-blue-50 text-blue-700' }}">{{ ucfirst($role->name) }}</span>
                                        @empty
                                            <span class="text-xs text-gray-400">No role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <button onclick="openPermissionsModal({{ $user->id }})" class="px-3 py-1.5 rounded border dark:border-gray-700 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition">Perms</button>
                                        <a href="{{ route('users.edit', $user) }}" class="px-3 py-1.5 rounded border dark:border-gray-700 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition">Edit</a>
                                        <button onclick="openDeleteModal({{ $user->id }})" class="px-3 py-1.5 rounded bg-red-50 text-red-700 text-sm hover:bg-red-100 transition">Del</button>
                                    </div>
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-gray-500">No users found.</div>
            @endif
        </div>
        @if($users->hasPages()) <div class="p-4 border-t dark:border-gray-800">{{ $users->links() }}</div> @endif
    </div>
</div>

{{-- Permissions Modal --}}
<div id="users-permissions-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-lg max-w-6xl w-full shadow-lg flex flex-col max-h-[90vh] overflow-hidden border dark:border-gray-700">
        <div class="px-6 py-4 border-b dark:border-gray-800 flex justify-between items-center">
            <div>
                <h3 id="perm-modal-title" class="text-lg font-medium text-gray-900 dark:text-gray-100">Permissions</h3>
                <p id="perm-modal-sub" class="text-sm text-gray-500"></p>
            </div>
            <button onclick="closePermModal()" class="text-gray-400 hover:text-gray-600 px-3 py-1 rounded border">Close</button>
        </div>

        <form id="users-permissions-form" class="flex-1 overflow-y-auto p-6" autocomplete="off">
            <div class="mb-4 flex flex-col md:flex-row md:items-end gap-3 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                <div class="flex-1">
                    <label class="text-xs text-gray-500 uppercase font-bold mb-1 block">Quick Apply Role Defaults</label>
                    <select id="role_select" class="w-full p-2 border rounded dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-blue-500"></select>
                </div>
                <div class="flex items-center gap-3">
                    <button id="apply-role-btn" type="button" class="px-4 py-2 bg-white dark:bg-gray-900 rounded border text-sm font-bold">Apply UI</button>
                    <label class="inline-flex items-center text-sm font-medium"><input id="assign-role-checkbox" type="checkbox" class="mr-2 rounded"> Assign on save</label>
                </div>
            </div>
            <div id="users-permissions-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"></div>
        </form>

        <div class="px-6 py-4 border-t dark:border-gray-800 flex justify-end gap-3">
            <button onclick="closePermModal()" class="px-4 py-2 rounded border">Cancel</button>
            <button id="users-save-perm-btn" class="px-4 py-2 rounded bg-blue-600 text-white font-bold shadow-md hover:bg-blue-700 transition">Save Changes</button>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="users-delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-lg max-w-md w-full p-6 text-center border dark:border-gray-800">
        <h3 class="text-lg font-bold mb-2">Delete User?</h3>
        <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 rounded border">Cancel</button>
            <button id="users-confirm-delete" class="px-4 py-2 rounded bg-red-600 text-white font-bold shadow-md">Yes, Delete</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const permModal = document.getElementById('users-permissions-modal');
    const permContainer = document.getElementById('users-permissions-container');
    const roleSelect = document.getElementById('role_select');
    let currentUserId = null;
    let cachedRoles = [];

    // Helper to extract JSON from string (ignores Spatie cache comments)
    const parseSafeJson = (text) => {
        const start = text.indexOf('{');
        const end = text.lastIndexOf('}');
        if (start === -1) throw new Error("Invalid response");
        return JSON.parse(text.substring(start, end + 1));
    };

    // --- OPEN PERMISSIONS ---
    window.openPermissionsModal = async function(id) {
        currentUserId = id;
        permContainer.innerHTML = '<div class="col-span-full text-center py-20 text-blue-500 font-bold">Synchronizing Data...</div>';
        permModal.classList.replace('hidden', 'flex');
        document.body.style.overflow = 'hidden';

        try {
            const res = await fetch(`/users/${id}/permissions`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = parseSafeJson(await res.text());

            document.getElementById('perm-modal-title').innerText = "Permissions: " + data.user.name;
            document.getElementById('perm-modal-sub').innerText = data.user.email;

            cachedRoles = data.roles || [];
            roleSelect.innerHTML = '<option value="">-- Choose Role Defaults --</option>' + cachedRoles.map(r => `<option value="${r.name}">${r.name}</option>`).join('');

            renderPermissions(data.grouped, data.user_permissions);
        } catch (e) { alert(e.message); }
    };

    function renderPermissions(grouped, userPerms) {
        permContainer.innerHTML = Object.entries(grouped).map(([category, perms]) => `
            <div class="p-3 border rounded-lg dark:border-gray-800 bg-white dark:bg-[#07102a] flex flex-col shadow-sm">
                <div class="flex items-center justify-between mb-2 pb-1 border-b dark:border-gray-800">
                    <h4 class="font-bold text-[10px] uppercase text-gray-500">${category}</h4>
                    <input type="checkbox" class="w-3 h-3 rounded" onclick="toggleCat(this)">
                </div>
                <div class="space-y-1">
                    ${perms.map(p => `
                        <label class="flex items-start gap-2 p-1 hover:bg-gray-50 dark:hover:bg-gray-800/40 cursor-pointer rounded transition">
                            <input type="checkbox" value="${p.name}" class="perm-check mt-0.5 h-3.5 w-3.5 text-blue-600 rounded" ${userPerms.includes(p.name) ? 'checked' : ''}>
                            <span class="text-xs text-gray-600 dark:text-gray-400">${p.label || p.name}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
        `).join('');
    }

    // --- SAVE PERMISSIONS ---
   document.getElementById('users-save-perm-btn').onclick = async function() {
    const btn = this;
    
    // 1. Initial Check
    console.log("Save button clicked for User:", currentUserId);
    
    const selectedPerms = Array.from(document.querySelectorAll('.perm-check:checked')).map(i => i.value);
    const roleName = document.getElementById('role_select').value;
    const shouldAssignRole = document.getElementById('assign-role-checkbox').checked;

    btn.disabled = true;
    btn.innerText = "Saving...";

    try {
        // 2. Fetch Call
        const response = await fetch(`/users/${currentUserId}/permissions`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json', 
                'X-CSRF-TOKEN': csrfToken 
            },
            body: JSON.stringify({ 
                permissions: selectedPerms, 
                roles: shouldAssignRole && roleName ? [roleName] : [], 
                sync_roles: shouldAssignRole 
            })
        });

        console.log("Server status code:", response.status);

        // 3. Handle response text
        const rawText = await response.text();
        console.log("Raw response from server:", rawText);

        const data = parseSafeJson(rawText);
        console.log("Parsed JSON data:", data);

        if (data.success) {
            updateTableCellRoles(currentUserId, data.user_roles);
            closePermModal();
            alert("Permissions updated successfully!");
        } else {
            alert("Server reported failure: " + (data.message || "Unknown error"));
        }

    } catch (e) {
        console.error("JavaScript Error:", e);
        alert("Request Failed. Look at the browser console (F12) for the red error.");
    } finally {
        btn.disabled = false;
        btn.innerText = "Save Changes";
    }
};

    function updateTableCellRoles(userId, roles) {
        const cell = document.querySelector(`.user-roles-cell[data-user-id="${userId}"]`);
        if (!cell) return;
        
        if (!roles || roles.length === 0) {
            cell.innerHTML = '<span class="text-xs text-gray-400">No role</span>';
            return;
        }

        cell.innerHTML = `<div class="flex flex-wrap gap-2">${roles.map(r => `
            <span class="px-2 py-1 text-xs font-medium rounded ${r.toLowerCase() == 'admin' ? 'bg-green-100 text-green-800' : 'bg-blue-50 text-blue-700'}">${r.charAt(0).toUpperCase() + r.slice(1)}</span>
        `).join('')}</div>`;
    }

    // --- HELPERS ---
    window.toggleCat = (el) => el.closest('div.flex-col').querySelectorAll('.perm-check').forEach(c => c.checked = el.checked);
    window.closePermModal = () => { permModal.classList.add('hidden'); document.body.style.overflow = 'auto'; };
    document.getElementById('apply-role-btn').onclick = () => {
        const role = cachedRoles.find(r => r.name === roleSelect.value);
        if (role) document.querySelectorAll('.perm-check').forEach(cb => cb.checked = role.permissions.includes(cb.value));
    };

    let targetDel = '';
    window.openDeleteModal = (id) => { targetDel = id; document.getElementById('users-delete-modal').classList.replace('hidden', 'flex'); };
    window.closeDeleteModal = () => document.getElementById('users-delete-modal').classList.replace('flex', 'hidden');
    document.getElementById('users-confirm-delete').onclick = async () => {
        const res = await fetch(`/users/${targetDel}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: new URLSearchParams({'_method':'DELETE'}) });
        if (res.ok) window.location.reload();
    };
});
</script>
@endsection