<x-app-layout>
    <x-slot:title>Companies & Manufacturing Plants</x-slot:title>

    <div class="space-y-6" x-data="{ activeSettingsTab: 'plants' }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Enterprise Entities & Plants</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 font-mono">Multi-Tenant Setup</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage corporate entities, manufacturing plants, factory units, warehouses, and organizational departments</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="document.getElementById('newDeptModal').classList.remove('hidden')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 shadow-xs flex items-center gap-1.5 transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>+ New Department</span>
                </button>

                <button onclick="document.getElementById('newPlantModal').classList.remove('hidden')" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>+ New Plant / Unit</span>
                </button>

                <button onclick="document.getElementById('newCompanyModal').classList.remove('hidden')" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ New Company Entity</span>
                </button>
            </div>
        </div>

        <!-- Sub Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-bold">
            <button @click="activeSettingsTab = 'plants'" :class="activeSettingsTab === 'plants' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Companies & Manufacturing Plants</span>
                <span class="px-1.5 py-0.2 rounded-md bg-white border border-slate-200 text-[10px] font-mono">{{ $allBranches->count() }} Plants</span>
            </button>
            <button @click="activeSettingsTab = 'departments'" :class="activeSettingsTab === 'departments' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Operational Departments</span>
                <span class="px-1.5 py-0.2 rounded-md bg-white border border-slate-200 text-[10px] font-mono">{{ $allDepartments->count() }}</span>
            </button>
        </div>

        <!-- TAB 1: Companies & Plants -->
        <div x-show="activeSettingsTab === 'plants'" class="grid grid-cols-1 gap-6">
            @foreach($companies as $comp)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-bold text-lg shrink-0">
                                {{ substr($comp->code, 0, 4) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-base font-bold text-slate-900">{{ $comp->name }}</h2>
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">Active Entity</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">Legal Name: <span class="text-slate-700 font-medium">{{ $comp->legal_name }}</span></div>
                                <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-slate-600">
                                    <span>GSTIN: <strong class="text-slate-900 font-mono">{{ $comp->gstin ?? 'N/A' }}</strong></span>
                                    <span>PAN: <strong class="text-slate-900 font-mono">{{ $comp->pan ?? 'N/A' }}</strong></span>
                                    <span>Currency: <strong class="text-amber-700 font-mono font-bold">{{ $comp->currency }} ({{ $comp->currency_symbol }})</strong></span>
                                    <span>Plants/Branches: <strong class="text-slate-900">{{ $comp->branches_count }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button onclick="openPlantModalForCompany('{{ $comp->id }}', '{{ $comp->name }}')" class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-xs font-bold text-amber-800 transition cursor-pointer">
                                + Add Plant
                            </button>

                            @if(session('current_company_id') == $comp->id)
                                <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active Workspace
                                </span>
                            @else
                                <form action="{{ route('companies.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="company_id" value="{{ $comp->id }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-slate-800 transition shadow-2xs cursor-pointer">
                                        Switch Workspace
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Branches Table -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">
                            <span>Manufacturing Units, Plants & Warehouses</span>
                            <span class="text-slate-400 font-normal lowercase">{{ $comp->branches->count() }} active units</span>
                        </div>
                        <div class="overflow-x-auto border border-slate-200 rounded-xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">Unit / Plant Name</th>
                                        <th class="py-2.5 px-3">Code</th>
                                        <th class="py-2.5 px-3">Plant Type</th>
                                        <th class="py-2.5 px-3">GSTIN</th>
                                        <th class="py-2.5 px-3">Location</th>
                                        <th class="py-2.5 px-3 text-right">Switch Active Plant</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($comp->branches as $branch)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-3 font-bold text-slate-900 flex items-center gap-2">
                                                <span>{{ $branch->name }}</span>
                                                @if($branch->is_head_office)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] bg-blue-50 text-blue-700 border border-blue-200 font-mono font-bold">HQ</span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-slate-500 font-semibold">{{ $branch->code }}</td>
                                            <td class="py-2.5 px-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $branch->type === 'factory' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700' }}">
                                                    {{ strtoupper($branch->type) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-600">{{ $branch->gstin ?? 'N/A' }}</td>
                                            <td class="py-2.5 px-3 text-slate-600">{{ $branch->city }}, {{ $branch->state }}</td>
                                            <td class="py-2.5 px-3 text-right">
                                                @if(session('current_branch_id') == $branch->id)
                                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-bold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selected Plant
                                                    </span>
                                                @else
                                                    <form action="{{ route('branches.switch') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                                        <button type="submit" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] cursor-pointer">
                                                            Select
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-slate-400 text-xs">No manufacturing plants registered for this entity.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- TAB 2: Departments -->
        <div x-show="activeSettingsTab === 'departments'" class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Enterprise Operational Departments</h3>
                <span class="text-[11px] text-slate-500">{{ $allDepartments->count() }} records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Department Name</th>
                            <th class="py-3 px-4">Code</th>
                            <th class="py-3 px-4">Company Entity</th>
                            <th class="py-3 px-4">Plant / Branch Location</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($allDepartments as $dept)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $dept->name }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-slate-500">{{ $dept->code }}</td>
                                <td class="py-3 px-4">{{ $dept->company?->name }}</td>
                                <td class="py-3 px-4">{{ $dept->branch?->name ?? 'All Plants' }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $dept->description ?? 'Standard operational department' }}</td>
                                <td class="py-3 px-4 text-right">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-400 text-xs">No departments registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Plant Modal -->
    <div id="newPlantModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Add Manufacturing Plant / Unit</h3>
                <button onclick="document.getElementById('newPlantModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('plants.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Company Entity</label>
                    <select name="company_id" id="plantCompanySelect" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ session('current_company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Plant / Unit Name</label>
                        <input type="text" name="name" placeholder="e.g. Nashik Heavy Forging Plant" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Plant Code</label>
                        <input type="text" name="code" placeholder="e.g. NSK-PLT" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Plant Type</label>
                        <select name="type" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="factory" selected>Manufacturing Factory / Plant</option>
                            <option value="warehouse">Central Logistics Warehouse</option>
                            <option value="head_office">Corporate Head Office</option>
                            <option value="branch">Regional Branch / Sales Depot</option>
                            <option value="subcontractor_unit">Subcontractor Jobbing Unit</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Plant GSTIN</label>
                        <input type="text" name="gstin" placeholder="27AAAAA0000A1Z5" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">City</label>
                        <input type="text" name="city" placeholder="e.g. Nashik" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">State</label>
                        <input type="text" name="state" placeholder="e.g. Maharashtra" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Address / Industrial Area</label>
                    <textarea name="address" rows="2" placeholder="Plot No. 42, MIDC Ambad Industrial Area..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newPlantModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition">Create Plant</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Company Entity Modal -->
    <div id="newCompanyModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Create New Company Entity</h3>
                <button onclick="document.getElementById('newCompanyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('companies.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Entity Trade Name</label>
                        <input type="text" name="name" placeholder="e.g. Apex Aerospace Pvt. Ltd." required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Entity Code</label>
                        <input type="text" name="code" placeholder="e.g. APEX-AERO" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono uppercase">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Legal Registered Name</label>
                    <input type="text" name="legal_name" placeholder="e.g. Apex Aerospace & Precision Engineering Private Limited" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Corporate GSTIN</label>
                        <input type="text" name="gstin" placeholder="27AAACA1234A1Z5" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono uppercase">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">PAN #</label>
                        <input type="text" name="pan" placeholder="AAACA1234A" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Base Currency</label>
                        <select name="currency" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="INR" selected>INR (₹ - Indian Rupee)</option>
                            <option value="USD">USD ($ - US Dollar)</option>
                            <option value="EUR">EUR (€ - Euro)</option>
                            <option value="GBP">GBP (£ - British Pound)</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">City / State</label>
                        <input type="text" name="city" placeholder="e.g. Pune, Maharashtra" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newCompanyModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition">Create Company Entity</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Department Modal -->
    <div id="newDeptModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Add Operational Department</h3>
                <button onclick="document.getElementById('newDeptModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('departments.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Company</label>
                    <select name="company_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ session('current_company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Plant (Optional)</label>
                    <select name="branch_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">-- All Plants / Corporate --</option>
                        @foreach($allBranches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Department Name</label>
                        <input type="text" name="name" placeholder="e.g. Tool Room & Dies" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Dept Code</label>
                        <input type="text" name="code" placeholder="e.g. TOOL-ROOM" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono uppercase">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Description</label>
                    <textarea name="description" rows="2" placeholder="Tool maintenance, jigs and fixtures fabrication..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newDeptModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition">Create Department</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPlantModalForCompany(compId, compName) {
            document.getElementById('plantCompanySelect').value = compId;
            document.getElementById('newPlantModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
