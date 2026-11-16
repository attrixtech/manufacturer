<x-app-layout>
    <x-slot:title>Users & Role-Based Access Control</x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-800">
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight">Staff & Access Control (RBAC)</h1>
                <p class="text-xs text-slate-400 mt-0.5">Manage operational personnel, security roles, and branch/plant authorisations</p>
            </div>

            <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-md shadow-amber-500/10 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Staff Member</span>
            </button>
        </div>

        <!-- Users Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Employee</th>
                            <th class="py-3 px-4">Role & Permissions</th>
                            <th class="py-3 px-4">Department</th>
                            <th class="py-3 px-4">Plant / Branch</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-amber-400 shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white">{{ $user->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $user->email }} • <span class="font-mono text-amber-500/90">{{ $user->employee_code ?? 'EMP' }}</span></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-3 px-4 text-slate-300">
                                    {{ $user->department?->name ?? 'Corporate' }}
                                    @if($user->designation)
                                        <div class="text-[10px] text-slate-400">{{ $user->designation }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-300">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>{{ $user->branch?->name ?? 'All Units' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] text-rose-400 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Suspended
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 transition">
                                            {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-slate-800">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Add User Modal (Hidden by default) -->
        <div id="addUserModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">Add New Staff Member</h3>
                    <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. Ramesh Chandra" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:border-amber-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Email Address</label>
                            <input type="email" name="email" required placeholder="ramesh@apexprecision.in" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:border-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Initial Password</label>
                            <input type="password" name="password" required value="password" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Employee Code</label>
                            <input type="text" name="employee_code" placeholder="EMP-007" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:border-amber-500 focus:outline-none font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Phone Number</label>
                            <input type="text" name="phone" placeholder="+91 98..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Role & Permissions</label>
                            <select name="role" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:border-amber-500 focus:outline-none">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Assigned Plant / Branch</label>
                            <select name="branch_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:border-amber-500 focus:outline-none">
                                <option value="">-- All Units --</option>
                                @foreach($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Department</label>
                        <select name="department_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:border-amber-500 focus:outline-none">
                            <option value="">-- General / Corporate --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl shadow-md shadow-amber-500/10 transition">
                            Create User Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
