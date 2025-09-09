@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Users</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage application users, roles, and permissions.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('users.create') }}" class="inline-flex items-center bg-blue-600 text-white py-2 px-4 rounded-lg shadow hover:bg-blue-700 transition" aria-label="Create new user">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success') || session('error'))
        <div class="mb-4" aria-live="polite">
            @if(session('success'))
                <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="bg-white dark:bg-[#0b1220] p-4 rounded-lg shadow mb-6">
        <form method="GET" id="filters-form" class="flex flex-col md:flex-row md:items-center md:space-x-4 gap-3" role="search" aria-label="Filter users">
            <div class="flex-1">
                <label for="q" class="sr-only">Search users</label>
                <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Search by name or email..." class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500" />
            </div>

            @if(isset($roles))
                <div>
                    <label for="role" class="sr-only">Filter by role</label>
                    <select name="role" id="role" class="p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="">All roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                                {{ ucfirst($r->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label for="per_page" class="sr-only">Per page</label>
                <select id="per_page" name="per_page" class="p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    @foreach([10,20,50,100] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}/page</option>
                    @endforeach
                </select>
            </div>

            <div class="ml-auto md:ml-0">
                <button type="submit" class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18A7.5 7.5 0 1010.5 3a7.5 7.5 0 000 15z"/></svg>
                    Filter
                </button>
                <a href="{{ route('users.index') }}" class="ml-2 text-sm text-gray-500 hover:underline">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#0b1220] shadow rounded-lg overflow-x-auto">
        @if ($users->count())
            <table class="min-w-full table-auto" role="table" aria-label="Users table">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">User</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:table-cell">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Role(s)</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900" id="user-row-{{ $user->id }}">
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-semibold" aria-hidden="true">
                                            {{ strtoupper(substr($user->name, 0, 1) ?: 'U') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 sm:hidden">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle hidden sm:table-cell">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</div>
                            </td>

                            {{-- roles cell: add class and data-user-id for optimistic updates --}}
                            <td class="px-4 py-3 align-middle user-roles-cell" data-user-id="{{ $user->id }}">
                                @if($user->roles->isEmpty())
                                    <span class="inline-block px-2 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">No role</span>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 text-xs font-medium rounded {{ $role->name === 'admin' ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-blue-50 text-blue-700 dark:bg-gray-700 dark:text-gray-200' }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3 align-middle text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">Edit</a>

                                    <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm"
                                        onclick="openPermissionsModal({{ $user->id }})">
                                        Permissions
                                    </button>

                                    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button data-user-id="{{ $user->id }}" class="inline-flex items-center px-3 py-1.5 rounded bg-red-50 text-red-700 hover:bg-red-100 text-sm" onclick="openDeleteModal(event)">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Bottom bar --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 p-4 border-t dark:border-gray-800">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <strong>{{ $users->firstItem() }}</strong> to <strong>{{ $users->lastItem() }}</strong> of <strong>{{ $users->total() }}</strong> users
                </div>

                <div>
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">No users found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Create the first user to get started.</p>
                <a href="{{ route('users.create') }}" class="inline-block mt-4 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">Create User</a>
            </div>
        @endif
    </div>
</div>

{{-- Delete confirmation modal --}}
<div id="users-delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="bg-white dark:bg-gray-900 rounded-lg max-w-md w-full shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b dark:border-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Confirm delete</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Are you sure you want to delete this user?</p>
            <div class="flex justify-end space-x-3">
                <button id="users-cancel-delete" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-800">Cancel</button>
                <button id="users-confirm-delete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Yes, delete</button>
            </div>
        </div>
    </div>
</div>

{{-- Permissions modal: rectangular, multi-column --}}
<div id="users-permissions-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="bg-white dark:bg-gray-900 rounded-lg max-w-6xl w-full shadow-lg overflow-hidden" role="document" aria-labelledby="perm-modal-title" style="box-shadow: 0 15px 35px rgba(0,0,0,0.25);">
        <div class="px-6 py-4 border-b dark:border-gray-800 flex items-center justify-between">
            <div>
                <h3 id="perm-modal-title" class="text-lg font-medium text-gray-900 dark:text-gray-100">Permissions</h3>
                <p id="perm-modal-sub" class="text-sm text-gray-500 dark:text-gray-400">Manage permissions for the user.</p>
            </div>

            <div class="flex items-center gap-3">
                <button id="perm-select-all-global" type="button" class="px-3 py-1 rounded border text-sm">Select all</button>
                <button id="perm-deselect-all-global" type="button" class="px-3 py-1 rounded border text-sm">Deselect all</button>
                <button id="users-close-perm-modal" class="px-3 py-1 rounded border">Close</button>
            </div>
        </div>

        <form id="users-permissions-form" class="p-4" autocomplete="off">
            <div class="mb-3 flex flex-col md:flex-row md:items-center md:gap-3">
                <div class="flex-1">
                    <label for="role_select" class="text-xs text-gray-600 dark:text-gray-400">Apply role defaults (select one or more)</label>
                    <select id="role_select" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm">
                        <option value="">-- Choose role(s) to apply --</option>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple roles. Applying multiple roles will union their permissions.</p>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button id="apply-role-btn" type="button" class="px-3 py-2 rounded border bg-gray-100 dark:bg-gray-800 text-sm">Apply role defaults</button>

                    <label class="inline-flex items-center space-x-2 text-sm">
                        <input id="assign-role-checkbox" type="checkbox" class="h-4 w-4">
                        <span>Assign selected role(s) to user on save</span>
                    </label>
                </div>
            </div>

            <div id="users-permissions-container" class="space-y-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" aria-live="polite" style="max-height: calc(80vh - 180px); overflow:auto; padding-right:6px;">
                {{-- filled dynamically --}}
            </div>

            <div class="flex justify-end gap-3 p-4 border-t dark:border-gray-800">
                <button type="button" id="users-cancel-perm-btn" class="px-4 py-2 rounded border">Cancel</button>
                <button type="submit" id="users-save-perm-btn" class="px-4 py-2 rounded bg-blue-600 text-white">Save changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // CSRF token (ensure your layout has <meta name="csrf-token" content="{{ csrf_token() }}">)
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    if (!csrfToken) {
        console.warn('CSRF token meta tag not found. Ensure your main layout includes: <meta name="csrf-token" content=\"{{ csrf_token() }}\">');
    }

    /* ---------- Delete modal ---------- */
    const deleteModal = document.getElementById('users-delete-modal');
    const cancelDeleteBtn = document.getElementById('users-cancel-delete');
    const confirmDeleteBtn = document.getElementById('users-confirm-delete');
    let targetDeleteFormAction = '';

    window.openDeleteModal = function(e) {
        e.preventDefault();
        const button = e.currentTarget || e.target.closest('[data-user-id]');
        const userId = button && button.dataset && button.dataset.userId;
        const form = document.getElementById('delete-form-' + userId);
        if (!form) {
            alert('Delete form not found for user ' + userId);
            return;
        }
        targetDeleteFormAction = form.getAttribute('action');
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
        deleteModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
        deleteModal.setAttribute('aria-hidden', 'true');
        targetDeleteFormAction = '';
        document.body.style.overflow = '';
    }

    if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', e => { e.preventDefault(); closeDeleteModal(); });

    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) closeDeleteModal();
    });

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function (e) {
            e.preventDefault();
            if (!targetDeleteFormAction) { closeDeleteModal(); return; }
            confirmDeleteBtn.disabled = true;
            try {
                console.debug('[delete] sending DELETE to', targetDeleteFormAction);
                const res = await fetch(targetDeleteFormAction, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                let json = null;
                try { json = await res.json(); } catch(_) { json = null; }
                console.debug('[delete] response', res.status, json);
                if (res.ok && (json ? (json.success ?? true) : true)) {
                    const match = targetDeleteFormAction.match(/\/users\/(\d+)$/);
                    if (match) {
                        const row = document.getElementById('user-row-' + match[1]);
                        if (row) row.remove();
                    } else {
                        window.location.reload();
                    }
                    closeDeleteModal();
                } else {
                    alert((json && (json.message || json.error)) || 'Failed to delete user. See console for details.');
                    console.error('Delete failed', res.status, json);
                }
            } catch (err) {
                console.error('Delete error', err);
                alert('An error occurred while deleting. See console for details.');
            } finally {
                confirmDeleteBtn.disabled = false;
            }
        });
    }

    /* ---------------- Permissions modal logic ---------------- */
    const permModal = document.getElementById('users-permissions-modal');
    const permContainer = document.getElementById('users-permissions-container');
    const permissionsForm = document.getElementById('users-permissions-form');
    const closePermBtn = document.getElementById('users-close-perm-modal');
    const cancelPermBtn = document.getElementById('users-cancel-perm-btn');
    const savePermBtn = document.getElementById('users-save-perm-btn');
    const globalSelectAll = document.getElementById('perm-select-all-global');
    const globalDeselectAll = document.getElementById('perm-deselect-all-global');
    const roleSelect = document.getElementById('role_select');
    const applyRoleBtn = document.getElementById('apply-role-btn');
    const assignRoleCheckbox = document.getElementById('assign-role-checkbox');

    let currentPermissionsUserId = null;
    let lastFetchedRoles = [];

    // Fallback submission via regular form
    function submitFallbackForm(userId, selectedPermissions, rolesToApply, assignRole) {
        console.warn('[fallback] Submitting regular form to server as fallback');
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = `/users/${userId}/permissions`;
        f.style.display = 'none';

        // CSRF token input if present
        if (csrfToken) {
            const t = document.createElement('input'); t.type = 'hidden'; t.name = '_token'; t.value = csrfToken; f.appendChild(t);
        }

        selectedPermissions.forEach(p => {
            const ip = document.createElement('input'); ip.type='hidden'; ip.name='permissions[]'; ip.value = p; f.appendChild(ip);
        });
        if (Array.isArray(rolesToApply) && rolesToApply.length) {
            rolesToApply.forEach(rn => {
                const r = document.createElement('input'); r.type='hidden'; r.name='roles[]'; r.value = rn; f.appendChild(r);
            });
            // sync_roles as hidden true -> server will replace roles
            const sr = document.createElement('input'); sr.type='hidden'; sr.name='sync_roles'; sr.value='1'; f.appendChild(sr);
        }
        if (assignRole) { const a = document.createElement('input'); a.type='hidden'; a.name='assign_role'; a.value='1'; f.appendChild(a); }

        document.body.appendChild(f);
        f.submit();
    }

    // Open permissions modal and fetch data
    window.openPermissionsModal = async function(userId) {
        currentPermissionsUserId = userId;
        permContainer.innerHTML = '<div class="p-4 text-sm text-gray-500">Loading permissions...</div>';
        permModal.classList.remove('hidden'); permModal.classList.add('flex');
        permModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        const url = `/users/${userId}/permissions`;
        console.debug('[perm] fetching', url);

        try {
            const res = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
            console.debug('[perm] fetch status', res.status, res.headers.get('content-type'));

            const ctype = (res.headers.get('content-type') || '').toLowerCase();
            if (res.status === 419) {
                throw new Error('CSRF token mismatch (419). Ensure your layout includes a csrf meta tag and the header X-CSRF-TOKEN is being sent.');
            }
            if (res.status === 401 || res.status === 302) {
                const t = await res.text().catch(()=>null);
                console.error('[perm] auth/redirect response (likely missing cookies/session):', res.status, t);
                throw new Error('Unauthorized or redirected. Check that fetch sends cookies (credentials) and you have a valid session.');
            }

            if (ctype.indexOf('application/json') === -1) {
                const txt = await res.text().catch(()=>null);
                console.error('[perm] expected JSON but got', ctype, res.status, txt);
                throw new Error('Server returned non-JSON response (see console). This often indicates a redirect to login or an exception page. Check network tab.');
            }

            const data = await res.json();
            console.debug('[perm] json', data);

            if (!data || data.success === false) {
                console.error('Permissions API returned success=false or invalid payload', data);
                throw new Error(data.message || 'Failed to fetch permissions');
            }

            // Header updates
            document.getElementById('perm-modal-title').textContent = 'Permissions — ' + (data.user?.name || 'User');
            document.getElementById('perm-modal-sub').textContent = data.user?.email || '';

            // populate roleSelect (multi-select)
            roleSelect.innerHTML = '<option value="">-- Choose role(s) to apply --</option>';
            lastFetchedRoles = data.roles || [];
            lastFetchedRoles.forEach(r => {
                const opt = document.createElement('option'); opt.value = r.name; opt.textContent = r.display || r.name; roleSelect.appendChild(opt);
            });

            // Render grouped permissions
            renderPermissions(data.grouped || {}, data.user_permissions || []);
            console.info('[perm] permissions loaded for user', userId);

            // if user_roles present, preselect them in roleSelect (optional)
            const userRoles = data.user_roles || [];
            Array.from(roleSelect.options).forEach(opt => {
                if (userRoles.indexOf(opt.value) !== -1) opt.selected = true;
            });

        } catch (err) {
            console.error('[perm] fetch error', err);
            permContainer.innerHTML = `<div class="p-4 text-sm text-red-600">Failed to load permissions: ${escapeHtml(err.message || String(err))}</div>`;
        }
    };

    // Render grouped permissions (adds group checkbox handlers etc.)
    function renderPermissions(grouped, userPerms) {
        permContainer.innerHTML = '';
        Object.keys(grouped || {}).forEach(resource => {
            const items = grouped[resource] || [];

            const card = document.createElement('section');
            card.className = 'p-3 border rounded-lg dark:border-gray-800 bg-white dark:bg-[#07102a]';
            card.setAttribute('data-group', resource);

            const header = document.createElement('div');
            header.className = 'flex items-center justify-between mb-2';

            const title = document.createElement('h4');
            title.className = 'font-semibold text-sm';
            title.textContent = (resource || 'general').replace(/_/g, ' ').toUpperCase();

            const groupSelect = document.createElement('label');
            groupSelect.className = 'inline-flex items-center space-x-2 text-sm';
            groupSelect.innerHTML = `<input type="checkbox" class="group-select-checkbox h-4 w-4" data-group="${escapeHtmlAttr(resource)}"><span class="text-xs">All</span>`;

            header.appendChild(title);
            header.appendChild(groupSelect);
            card.appendChild(header);

            const list = document.createElement('div');
            list.className = 'grid grid-cols-1 gap-2 sm:grid-cols-2';

            items.forEach(p => {
                const isChecked = Array.isArray(userPerms) && userPerms.indexOf(p.name) !== -1;
                const labelText = p.label || p.name || 'Unknown';
                const label = document.createElement('label');
                label.className = 'inline-flex items-center space-x-2 text-sm';
                label.innerHTML = `<input type="checkbox" name="permissions[]" value="${escapeHtmlAttr(p.name)}" class="perm-checkbox h-4 w-4" ${isChecked ? 'checked' : ''}><span class="text-sm break-words">${escapeHtml(labelText)}</span>`;
                list.appendChild(label);
            });

            card.appendChild(list);
            permContainer.appendChild(card);

            // group checkbox toggles all in group
            const groupCb = card.querySelector('.group-select-checkbox');
            groupCb && groupCb.addEventListener('change', function () {
                const checked = !!this.checked;
                card.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
                updateGroupCheckboxState(resource);
            });

            // each checkbox updates group state
            card.querySelectorAll('.perm-checkbox').forEach(cb => cb.addEventListener('change', () => updateGroupCheckboxState(resource)));

            updateGroupCheckboxState(resource);
        });
    }

    function updateGroupCheckboxState(group) {
        const card = document.querySelector(`[data-group="${CSS.escape(group)}"]`);
        if (!card) return;
        const total = card.querySelectorAll('.perm-checkbox').length;
        const checked = card.querySelectorAll('.perm-checkbox:checked').length;
        const groupCheckbox = card.querySelector('.group-select-checkbox');
        if (!groupCheckbox) return;
        groupCheckbox.indeterminate = checked > 0 && checked < total;
        groupCheckbox.checked = (checked === total && total > 0);
    }

    function escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function escapeHtmlAttr(s) {
        if (s == null) return '';
        return String(s).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    // Apply role defaults (union if multiple selected)
    if (applyRoleBtn) {
        applyRoleBtn.addEventListener('click', function () {
            const selectedRoleNames = Array.from(roleSelect.selectedOptions).map(o => o.value).filter(Boolean);
            if (!selectedRoleNames.length) return alert('Select at least one role to apply its default permissions.');
            const rolePermSet = new Set();
            selectedRoleNames.forEach(name => {
                const roleObj = lastFetchedRoles.find(r => r.name === name);
                if (roleObj && Array.isArray(roleObj.permissions)) {
                    roleObj.permissions.forEach(p => rolePermSet.add(p));
                }
            });
            permContainer.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = rolePermSet.has(cb.value));
            permContainer.querySelectorAll('[data-group]').forEach(node => updateGroupCheckboxState(node.getAttribute('data-group')));
        });
    }

    if (globalSelectAll) globalSelectAll.addEventListener('click', () => {
        permContainer.querySelectorAll('.perm-checkbox').forEach(c => c.checked = true);
        permContainer.querySelectorAll('.group-select-checkbox').forEach(cb => { cb.checked = true; cb.indeterminate = false; });
    });
    if (globalDeselectAll) globalDeselectAll.addEventListener('click', () => {
        permContainer.querySelectorAll('.perm-checkbox').forEach(c => c.checked = false);
        permContainer.querySelectorAll('.group-select-checkbox').forEach(cb => { cb.checked = false; cb.indeterminate = false; });
    });

    // Save handler with debug & fallback form submission
    if (permissionsForm) {
        permissionsForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!currentPermissionsUserId) { alert('No user selected'); return; }

            savePermBtn.disabled = true;
            const selected = Array.from(permContainer.querySelectorAll('.perm-checkbox:checked')).map(n => n.value);
            const selectedRoles = Array.from(roleSelect.selectedOptions).map(o => o.value).filter(Boolean);
            const assignRole = !!assignRoleCheckbox.checked;

            console.debug('[perm] saving', { userId: currentPermissionsUserId, selected, selectedRoles, assignRole });

            const url = `/users/${currentPermissionsUserId}/permissions`;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ permissions: selected, roles: selectedRoles, sync_roles: true, assign_role: assignRole })
                });

                console.debug('[perm] save response status', res.status, res.headers.get('content-type'));

                const ctype = (res.headers.get('content-type') || '').toLowerCase();
                if (res.status === 419) {
                    alert('CSRF failure: 419. Ensure csrf token present in layout and request headers.');
                    throw new Error('CSRF token mismatch (419)');
                }
                if (res.status === 401 || res.status === 302) {
                    const txt = await res.text().catch(()=>null);
                    console.error('[perm] unauthorized/redirect response', res.status, txt);
                    if (confirm('Save failed due to auth/redirect. Submit via normal form as a fallback?')) {
                        submitFallbackForm(currentPermissionsUserId, selected, selectedRoles, assignRole);
                        return;
                    }
                    throw new Error('Unauthorized or redirected. Check cookies/session.');
                }

                if (ctype.indexOf('application/json') === -1) {
                    const txt = await res.text().catch(()=>null);
                    console.error('[perm] expected application/json but got', ctype, txt);
                    if (confirm('Server returned a non-JSON response. Try submitting with normal form fallback?')) {
                        submitFallbackForm(currentPermissionsUserId, selected, selectedRoles, assignRole);
                        return;
                    }
                    throw new Error('Non-JSON response from server');
                }

                const json = await res.json().catch(()=>null);
                console.debug('[perm] save json', json);

                if ((res.ok && (json?.success === undefined || json.success)) || json?.success) {
                    const returnedRoles = Array.isArray(json.user_roles) ? json.user_roles : (json.user_roles || []);
                    const normalized = returnedRoles.map(r => typeof r === 'string' ? r : (r.name || r.role || 'unknown'));
                    updateRoleBadges(currentPermissionsUserId, normalized);
                    closePermModal();
                    console.info('[perm] saved successfully');
                } else {
                    const message = json?.message || json?.error || 'Failed to update permissions';
                    console.error('[perm] save failed', res.status, json);
                    alert(message);
                }
            } catch (err) {
                console.error('[perm] save exception', err);
                if (confirm('An error occurred while saving permissions. Try a normal form submit as a fallback?')) {
                    submitFallbackForm(currentPermissionsUserId, selected, selectedRoles, assignRole);
                } else {
                    alert('Save failed. See console and network tab for details.');
                }
            } finally {
                savePermBtn.disabled = false;
            }
        });
    }

    function updateRoleBadges(userId, roles) {
        try {
            const cell = document.querySelector('.user-roles-cell[data-user-id="'+userId+'"]');
            if (!cell) return;
            if (!roles || roles.length === 0) {
                cell.innerHTML = '<span class="inline-block px-2 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">No role</span>';
                return;
            }
            const htmlParts = roles.map(r => {
                const roleName = (typeof r === 'string') ? r : (r.name || r.role || '');
                const safeName = escapeHtml(roleName);
                if (roleName === 'admin') {
                    return '<span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">'+safeName+'</span>';
                } else {
                    return '<span class="px-2 py-1 text-xs font-medium rounded bg-blue-50 text-blue-700 dark:bg-gray-700 dark:text-gray-200">'+safeName+'</span>';
                }
            });
            cell.innerHTML = '<div class="flex flex-wrap gap-2">' + htmlParts.join('') + '</div>';
        } catch (err) {
            console.error('updateRoleBadges error', err);
        }
    }

    // close modal handlers & ESC
    if (closePermBtn) closePermBtn.addEventListener('click', () => closePermModal());
    if (cancelPermBtn) cancelPermBtn.addEventListener('click', (e) => { e.preventDefault(); closePermModal(); });
    permModal.addEventListener('click', (e) => { if (e.target === permModal) closePermModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { if (!deleteModal.classList.contains('hidden')) closeDeleteModal(); if (!permModal.classList.contains('hidden')) closePermModal(); } });

    function closePermModal() {
        permModal.classList.add('hidden');
        permModal.classList.remove('flex');
        permModal.setAttribute('aria-hidden', 'true');
        permContainer.innerHTML = '';
        currentPermissionsUserId = null;
        document.body.style.overflow = '';
    }
});
</script>
@endsection
