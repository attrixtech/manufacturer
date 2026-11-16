<x-app-layout>
    <x-slot:title>Executive & Plant Operations Dashboard</x-slot:title>

    <div class="space-y-6">

        <!-- Top Greeting and Current Plant Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-slate-800">
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>Manufacturing Command Center</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-semibold font-mono border border-emerald-500/30">Live Plant Operations</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Welcome back, <strong class="text-slate-200">{{ auth()->user()->name }}</strong> ({{ auth()->user()->roles->first()?->name ?? 'Administrator' }}). Operating under <span class="text-amber-400 font-medium">{{ $company->name }}</span>.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('roadmap.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-semibold rounded-xl text-slate-200 flex items-center gap-2 transition">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>ERP Development Roadmap</span>
                </a>
                <a href="{{ route('users.index') }}" class="px-3 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-md shadow-amber-500/10 flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Manage Staff & Access</span>
                </a>
            </div>
        </div>

        <!-- 4 Top KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Active Work Orders -->
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Active Work Orders</span>
                    <span class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-white">{{ $stats['active_work_orders'] }}</div>
                <div class="flex items-center gap-2 mt-2 text-[11px]">
                    <span class="text-emerald-400 font-medium">12 On Schedule</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-rose-400 font-medium">{{ $stats['work_orders_delayed'] }} Behind Schedule</span>
                </div>
            </div>

            <!-- Card 2: Plant OEE & Uptime -->
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Plant OEE / Uptime</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-white">{{ $stats['oee_percentage'] }}%</div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full" style="width: {{ $stats['oee_percentage'] }}%"></div>
                </div>
                <div class="text-[10px] text-slate-400 mt-1.5 flex justify-between">
                    <span>Target: 85%</span>
                    <span class="text-emerald-400">+2.4% vs benchmark</span>
                </div>
            </div>

            <!-- Card 3: Inventory Valuation -->
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Stock Valuation (FIFO)</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-white">₹{{ number_format($stats['inventory_valuation'] / 10000000, 2) }} Cr</div>
                <div class="flex items-center gap-2 mt-2 text-[11px] text-slate-400">
                    <span>RM: 55%</span>
                    <span class="text-slate-600">•</span>
                    <span>WIP: 25%</span>
                    <span class="text-slate-600">•</span>
                    <span>FG: 20%</span>
                </div>
            </div>

            <!-- Card 4: Quality Pass Rate -->
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">QA/QC Pass Rate</span>
                    <span class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-white">{{ $stats['quality_pass_rate'] }}%</div>
                <div class="flex items-center gap-2 mt-2 text-[11px]">
                    <span class="text-emerald-400 font-medium">Zero Fatal Defects</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-slate-400">0.8% Scrap/Rework</span>
                </div>
            </div>
        </div>

        <!-- Shop Floor Operations Live Tracking & Active Work Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Production Lines Status -->
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-white flex items-center gap-2">
                            <span>Shop Floor Live Production Centers</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Real-time telemetry and active job card execution across lines</p>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Pune Heavy Fab Unit</span>
                </div>

                <div class="space-y-3">
                    @foreach($productionLines as $line)
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
                                    Op: <span class="text-slate-200">{{ $line['operator'] }}</span> | <span class="text-amber-400">{{ $line['speed_rpm'] }}</span>
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
                        <p class="text-[11px] text-slate-400">Tamper-evident security and operations log</p>
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

        <!-- ERP Implementation Architecture Phase Status Card -->
        <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-900/90 border border-amber-500/30 shadow-lg relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Phase 1 Completed: Architecture, Core Security & Multi-Company Foundation</span>
                    </div>
                    <h3 class="text-lg font-bold text-white">Foundation Successfully Deployed</h3>
                    <p class="text-xs text-slate-400 max-w-2xl mt-1 leading-relaxed">
                        Multi-company, multi-plant hierarchy, Spatie RBAC with manufacturing roles, database architecture, immutable audit logging, and responsive enterprise workspace are operational. Ready to proceed with Master Data (Phase 2) and Inventory Ledger (Phase 3).
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('roadmap.index') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                        <span>View 12-Phase Roadmap</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
