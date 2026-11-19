<x-layouts.admin title="User Management">
    <div class="space-y-6">
        {{-- Header & Create Button --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">User Account Management</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Manage institutional accounts, administrative privileges, and security credentials.
                </p>
            </div>
            <button type="button"
                    onclick="document.getElementById('create-user-modal').classList.toggle('hidden')"
                    class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-500 transition self-start sm:self-auto">
                + Add Institutional Account
            </button>
        </div>

        {{-- Create User Modal / Box --}}
        <div id="create-user-modal" class="hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-base font-bold text-slate-900 mb-4">Create Institutional Account</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-4 sm:grid-cols-2">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700">Full Name *</label>
                    <input type="text" name="name" id="name" required class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700">Institutional Email *</label>
                    <input type="email" name="email" id="email" required placeholder="name@school.edu" class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                </div>
                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700">System Role *</label>
                    <select name="role" id="role" class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                        <option value="user">Regular User (Student / Faculty)</option>
                        <option value="admin">Campus Administrator</option>
                    </select>
                </div>
                <div>
                    <label for="department" class="block text-xs font-semibold text-slate-700">Department / Office</label>
                    <input type="text" name="department" id="department" placeholder="College of Engineering" class="mt-1 w-full rounded-xl border-0 p-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                </div>
                <div class="sm:col-span-2 flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('create-user-modal').classList.add('hidden')" class="rounded-xl bg-white px-3 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-500">
                        Create Account
                    </button>
                </div>
            </form>
        </div>

        {{-- Search & Filters --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, or ID..." class="flex-1 rounded-xl border-0 py-2 px-3 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                <select name="role" onchange="this.form.submit()" class="rounded-xl border-0 py-2 px-3 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                    <option value="all" @selected($role === 'all')>All Roles</option>
                    <option value="user" @selected($role === 'user')>Regular Users</option>
                    <option value="admin" @selected($role === 'admin')>Administrators</option>
                </select>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Search</button>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Registered</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $u)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 font-bold text-brand-700">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <span>{{ $u->name }}</span>
                                            <span class="block text-[11px] text-slate-400 font-normal">{{ $u->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span @class([
                                        'rounded-md px-2 py-0.5 text-[10px] font-semibold',
                                        'bg-purple-50 text-purple-700 ring-1 ring-purple-200' => $u->isAdmin(),
                                        'bg-slate-100 text-slate-600' => ! $u->isAdmin(),
                                    ])>
                                        {{ $u->role->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span @class([
                                        'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                        'bg-emerald-50 text-emerald-700' => $u->is_active,
                                        'bg-rose-50 text-rose-700' => ! $u->is_active,
                                    ])>
                                        <span class="h-1.5 w-1.5 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $u->is_active ? 'Active' : 'Deactivated' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500">
                                    {{ $u->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4 text-right space-x-1">
                                    @if ($u->id !== auth()->id())
                                        {{-- Toggle Active --}}
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-200">
                                                {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        {{-- Toggle Role --}}
                                        <form method="POST" action="{{ route('admin.users.toggle-role', $u) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-200">
                                                {{ $u->isAdmin() ? 'Make Student' : 'Make Admin' }}
                                            </button>
                                        </form>

                                        {{-- Reset Password --}}
                                        <form method="POST" action="{{ route('admin.users.reset-password', $u) }}" class="inline" onsubmit="return confirm('Generate a temporary password for this user?');">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-200">
                                                Reset Password
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Your Account</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>
