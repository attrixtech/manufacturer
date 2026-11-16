<x-app-layout>
    <x-slot:title>{{ $roleData['title'] }}</x-slot:title>

    <div class="space-y-6">

        <!-- Top Greeting and Current Role Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-slate-800">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-extrabold text-white tracking-tight">{{ $roleData['title'] }}</h1>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 font-bold font-mono border border-amber-500/30">
                        {{ $roleName }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $roleData['subtitle'] }} • Active User: <strong class="text-slate-200">{{ $user->name }}</strong> ({{ $company->name }})
                </p>
            </div>

            <!-- Role-specific top header buttons -->
            <div class="flex items-center gap-2">
                @if(in_array($roleName, ['Super Administrator', 'Plant Manager']))
                    <a href="{{ route('roadmap.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-semibold rounded-xl text-slate-200 flex items-center gap-2 transition">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>ERP Roadmap</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="px-3 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-md shadow-amber-500/10 flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Manage Staff</span>
                    </a>
                @elseif($roleName === 'Quality Inspector')
                    <button class="px-3 py-2 bg-purple-500 hover:bg-purple-400 text-white font-bold text-xs rounded-xl shadow-md shadow-purple-500/10 flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>New Inspection Entry</span>
                    </button>
                    <button class="px-3 py-2 bg-rose-500/20 hover:bg-rose-500/30 text-rose-400 border border-rose-500/30 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Raise NCR</span>
                    </button>
                @elseif($roleName === 'Warehouse & Inventory Manager')
                    <button class="px-3 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-md shadow-emerald-500/10 flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span>Stock Transfer</span>
                    </button>
                    <button class="px-3 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-md shadow-amber-500/10 flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Record Stock Movement</span>
                    </button>
                @elseif($roleName === 'Machine Operator')
                    <button class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-md shadow-emerald-500/10 flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Resume Cycle</span>
                    </button>
                    <button class="px-3.5 py-2 bg-rose-500/20 hover:bg-rose-500/30 text-rose-400 border border-rose-500/30 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Report Machine Breakdown</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 4 Role-Specific Top KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($roleData['cards'] as $card)
                @php
                    $colors = [
                        'blue' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        'amber' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                        'emerald' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'purple' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                        'rose' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                    ];
                    $badgeStyle = $colors[$card['color']] ?? 'bg-slate-800 text-slate-300';
                @endphp
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">{{ $card['label'] }}</span>
                        <span class="w-6 h-6 rounded-lg {{ $badgeStyle }} flex items-center justify-center font-bold text-xs">
                            ●
                        </span>
                    </div>
                    <div class="text-2xl font-black text-white tracking-tight">{{ $card['value'] }}</div>
                    <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-1.5">
                        <span class="text-slate-300 font-medium">{{ $card['sub'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Role-Specific Core Content View -->

        @if($roleData['view_type'] === 'quality')
            <!-- ================= QUALITY INSPECTOR DASHBOARD ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Pending Inspections Queue -->
                <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                                <span>Inspection Clearance Queue (IQC / IPQC / FQC)</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-purple-500/20 text-purple-400 font-semibold font-mono">4 Pending</span>
                            </h2>
                            <p class="text-[11px] text-slate-400">Incoming GRNs & In-Process lots quarantined awaiting QA certification</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="py-2.5 px-3">Batch / Order</th>
                                    <th class="py-2.5 px-3">Material / Item</th>
                                    <th class="py-2.5 px-3">Stage</th>
                                    <th class="py-2.5 px-3">Quarantine Loc</th>
                                    <th class="py-2.5 px-3">Qty</th>
                                    <th class="py-2.5 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                @foreach($roleData['pending_inspections'] as $insp)
                                    <tr class="hover:bg-slate-800/30 transition">
                                        <td class="py-2.5 px-3 font-mono font-bold text-amber-400">{{ $insp['lot_no'] }}</td>
                                        <td class="py-2.5 px-3">
                                            <div class="font-semibold text-white">{{ $insp['item'] }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $insp['supplier'] }}</div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-800 text-slate-200">
                                                {{ $insp['type'] }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">{{ $insp['warehouse'] }}</td>
                                        <td class="py-2.5 px-3 font-semibold text-white">{{ $insp['qty'] }}</td>
                                        <td class="py-2.5 px-3 text-right">
                                            <button class="px-2 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 font-bold text-[11px] transition">
                                                Inspect
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Non-Conformance Reports (NCR) -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white">Active NCR & CAPA</h2>
                            <p class="text-[11px] text-slate-400">Non-conformance investigations</p>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 font-mono font-bold">2 OPEN</span>
                    </div>

                    <div class="space-y-3 flex-1">
                        @foreach($roleData['open_ncrs'] as $ncr)
                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-mono font-bold text-rose-400">{{ $ncr['code'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">{{ $ncr['severity'] }}</span>
                                </div>
                                <div class="text-[11px] text-slate-200 font-medium mb-1">{{ $ncr['issue'] }}</div>
                                <div class="text-[10px] text-amber-400 font-mono">Status: {{ $ncr['disposition'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @elseif($roleData['view_type'] === 'inventory')
            <!-- ================= WAREHOUSE & INVENTORY DASHBOARD ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Reorder Alerts -->
                <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                                <span>Critical Low Stock & Reorder Triggers</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500/20 text-rose-400 font-semibold font-mono">Immediate Action</span>
                            </h2>
                            <p class="text-[11px] text-slate-400">Items breached safety stock thresholds requiring Purchase Requisitions (PR)</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="py-2.5 px-3">Item Code</th>
                                    <th class="py-2.5 px-3">Description</th>
                                    <th class="py-2.5 px-3">Warehouse Bay</th>
                                    <th class="py-2.5 px-3">Current Stock</th>
                                    <th class="py-2.5 px-3">Min Buffer</th>
                                    <th class="py-2.5 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                @foreach($roleData['reorder_alerts'] as $alert)
                                    <tr class="hover:bg-slate-800/30 transition">
                                        <td class="py-2.5 px-3 font-mono font-bold text-rose-400">{{ $alert['code'] }}</td>
                                        <td class="py-2.5 px-3 font-semibold text-white">{{ $alert['name'] }}</td>
                                        <td class="py-2.5 px-3 text-slate-400">{{ $alert['warehouse'] }}</td>
                                        <td class="py-2.5 px-3 font-bold text-rose-400 font-mono">{{ $alert['current_stock'] }}</td>
                                        <td class="py-2.5 px-3 text-slate-300 font-mono">{{ $alert['min_reorder'] }}</td>
                                        <td class="py-2.5 px-3 text-right">
                                            <button class="px-2.5 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-400 font-bold text-[11px] transition">
                                                Create PR
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Movements -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white">Live Stock Ledger Moves</h2>
                            <p class="text-[11px] text-slate-400">Atomic inventory postings</p>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-mono font-bold">PERPETUAL</span>
                    </div>

                    <div class="space-y-3 flex-1">
                        @foreach($roleData['recent_movements'] as $mv)
                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-mono font-bold text-amber-400">{{ $mv['doc'] }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $mv['time'] }}</span>
                                </div>
                                <div class="text-[11px] text-white font-medium mb-1">{{ $mv['item'] }}</div>
                                <div class="text-[10px] text-slate-400 flex items-center justify-between">
                                    <span>From: {{ $mv['source'] }}</span>
                                    <span>To: <strong class="text-emerald-400">{{ $mv['dest'] }}</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @elseif($roleData['view_type'] === 'finance')
            <!-- ================= FINANCE & ACCOUNTS DASHBOARD ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Manufacturing Cost Breakdown -->
                <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                                <span>Manufacturing WIP Cost Absorption (Month-to-Date)</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/20 text-emerald-400 font-semibold font-mono">Balanced GL</span>
                            </h2>
                            <p class="text-[11px] text-slate-400">Standard vs actual cost distribution across production orders</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-emerald-400">Total: ₹1.35 Cr</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($roleData['cost_breakdown'] as $cost)
                            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-white">{{ $cost['head'] }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono font-bold text-amber-400">{{ $cost['amount'] }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300">{{ $cost['share'] }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-500 to-emerald-500 rounded-full" style="width: {{ $cost['share'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pending Invoices / Vouchers -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white">Pending Vouchers & Invoices</h2>
                            <p class="text-[11px] text-slate-400">3-way matching and collections</p>
                        </div>
                    </div>

                    <div class="space-y-3 flex-1">
                        @foreach($roleData['pending_vouchers'] as $vch)
                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-mono font-bold text-amber-400">{{ $vch['ref'] }}</span>
                                    <span class="font-mono font-bold text-white">{{ $vch['amount'] }}</span>
                                </div>
                                <div class="text-[11px] text-slate-200 font-medium">{{ $vch['entity'] }}</div>
                                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between">
                                    <span>{{ $vch['type'] }}</span>
                                    <span class="text-amber-400 font-medium">{{ $vch['due'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @elseif($roleData['view_type'] === 'operator')
            <!-- ================= MACHINE OPERATOR TERMINAL ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Active Job Card Details -->
                <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                        <div>
                            <div class="text-xs text-amber-400 font-mono font-bold uppercase tracking-wider">Active Shop Floor Job Card</div>
                            <h2 class="text-lg font-black text-white mt-0.5">{{ $roleData['job_card']['code'] }}</h2>
                            <p class="text-xs text-slate-400">{{ $roleData['job_card']['work_order'] }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> IN PROGRESS
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6 text-xs">
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-slate-400 text-[10px] uppercase font-semibold">Part Number</div>
                            <div class="font-bold text-white mt-0.5 font-mono">{{ $roleData['job_card']['part_no'] }}</div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-slate-400 text-[10px] uppercase font-semibold">Operation</div>
                            <div class="font-bold text-white mt-0.5">{{ $roleData['job_card']['operation'] }}</div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-slate-400 text-[10px] uppercase font-semibold">Target Cycle Time</div>
                            <div class="font-bold text-amber-400 mt-0.5 font-mono">{{ $roleData['job_card']['target_cycle_time'] }}</div>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 mb-6">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-semibold text-white">Shift Production Output</span>
                            <span class="font-mono text-amber-400 font-bold">{{ $roleData['job_card']['completed_qty'] }} / {{ $roleData['job_card']['planned_qty'] }} Pcs (71.6%)</span>
                        </div>
                        <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-500 to-emerald-500 h-full rounded-full" style="width: 71.6%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2">
                            <span>Accepted: <strong class="text-emerald-400">85 Pcs</strong></span>
                            <span>Rework: <strong class="text-amber-400">1 Pc</strong></span>
                            <span>Scrap / Rejection: <strong class="text-rose-400">0 Pcs</strong></span>
                        </div>
                    </div>

                    <!-- Operator Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        <button class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Record Batch Piece (+1)</span>
                        </button>
                        <button class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                            Log Operator Break
                        </button>
                    </div>
                </div>

                <!-- Machine Telemetry & Maintenance -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white mb-1">Machine VMC-01 Telemetry</h3>
                        <p class="text-[11px] text-slate-400 mb-4">Live sensor feeds from Siemens 828D CNC Controller</p>

                        <div class="space-y-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center">
                                <span class="text-slate-400">Spindle Load</span>
                                <span class="font-mono font-bold text-emerald-400">72% (Normal)</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center">
                                <span class="text-slate-400">Coolant Pressure</span>
                                <span class="font-mono font-bold text-cyan-400">4.8 Bar (Optimal)</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center">
                                <span class="text-slate-400">Tool Life (T04 Endmill)</span>
                                <span class="font-mono font-bold text-amber-400">84% Remaining</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-800">
                        <button class="w-full py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Trigger Maintenance Breakdown</span>
                        </button>
                    </div>
                </div>
            </div>

        @else
            <!-- ================= EXECUTIVE & PLANT COMMAND CENTER ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left 2 Cols: Production Lines Telemetry -->
                <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                                <span>Shop Floor Live Production Centers</span>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            </h2>
                            <p class="text-[11px] text-slate-400">Live telemetry and active job card execution across lines</p>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">Pune Heavy Fab Unit</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($roleData['production_telemetry'] ?? $roleData['lines'] as $line)
                            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 transition">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs mb-2">
                                    <div class="font-bold text-white flex items-center gap-2">
                                        <span>{{ $line['name'] }}</span>
                                        @if($line['status'] === 'running')
                                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium">RUNNING</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-amber-500/10 text-amber-400 border border-amber-500/20 font-medium">COOLING/SETUP</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        Op: <span class="text-slate-200">{{ $line['operator'] }}</span> | <span class="text-amber-400">{{ $line['speed_rpm'] ?? $line['speed'] }}</span>
                                    </div>
                                </div>

                                <div class="text-[11px] text-slate-300 font-mono mb-2 flex items-center justify-between">
                                    <span class="text-amber-400 font-medium">{{ $line['current_wo'] }}</span>
                                    <span class="font-bold text-slate-200">{{ $line['progress'] }}% Completed</span>
                                </div>

                                <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $line['progress'] == 100 ? 'bg-purple-500' : 'bg-gradient-to-r from-amber-500 to-orange-500' }}" style="width: {{ $line['progress'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Col: System Activity & Audit Log -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white">Activity & Audit Trail</h2>
                            <p class="text-[11px] text-slate-400">Tamper-evident operations log</p>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono">Immutable</span>
                    </div>

                    <div class="space-y-3 flex-1 overflow-y-auto max-h-96 pr-1">
                        @forelse($recentActivities as $log)
                            <div class="p-2.5 rounded-xl bg-slate-950/40 border border-slate-800/60 text-xs">
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                    <span class="font-bold text-amber-400 uppercase tracking-wider font-mono">{{ $log->action }}</span>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-slate-200 text-[11px] leading-relaxed">
                                    {{ $log->description }}
                                </div>
                                <div class="mt-1 text-[10px] text-slate-400 flex items-center justify-between">
                                    <span>{{ $log->user?->name ?? 'System' }}</span>
                                    <span class="font-mono text-[9px] text-slate-500">{{ $log->ip_address }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-xs text-slate-400">
                                No activity logged yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
