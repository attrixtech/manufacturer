<x-app-layout>
    <x-slot:title>Breakdown & Machine Maintenance</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Breakdown & Machine Maintenance</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 font-mono">Downtime Sentinel</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Asset health telemetry, live machine diagnostics, breakdown incidents, and MTTR tracking</p>
            </div>

            <button onclick="document.getElementById('newBreakdownModal').classList.remove('hidden')" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Report Breakdown</span>
            </button>
        </div>

        <!-- 4 Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Machine Assets</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $machines->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Operational Assets</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $machines->where('status', 'running')->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active Breakdowns</div>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $breakdowns->whereIn('status', ['reported', 'in_repair'])->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Recorded Downtime</div>
                <div class="text-2xl font-black text-amber-600 mt-1 font-mono">{{ $breakdowns->sum('downtime_minutes') }} min</div>
            </div>
        </div>

        <!-- Live Machine Telemetry Cards -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Machine Asset Registry & Telemetry
                </h3>
                <span class="text-[11px] font-medium text-slate-500">{{ $machines->count() }} active units</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($machines as $machine)
                    @php
                        $mStatusBadge = match($machine->status) {
                            'running' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'idle' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'maintenance' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'breakdown' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:border-slate-300 transition">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $machine->code }}</span>
                                <h4 class="text-sm font-black text-slate-900">{{ $machine->name }}</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $mStatusBadge }} uppercase font-mono">
                                {{ str_replace('_', ' ', $machine->status) }}
                            </span>
                        </div>

                        <div class="text-[11px] text-slate-500 mb-4 capitalize">Work Center: <strong class="text-slate-700">{{ $machine->work_center }}</strong></div>

                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <div>
                                <div class="flex justify-between text-[11px] mb-1">
                                    <span class="text-slate-500">Spindle Load</span>
                                    <span class="font-mono font-bold text-slate-800">{{ (int)$machine->spindle_load }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ min(100, (int)$machine->spindle_load) }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-[11px] mb-1">
                                    <span class="text-slate-500">Coolant Pressure</span>
                                    <span class="font-mono font-bold text-slate-800">{{ (float)$machine->coolant_pressure }} Bar</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-cyan-500 h-1.5 rounded-full" style="width: {{ min(100, ((float)$machine->coolant_pressure / 8) * 100) }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-[11px] mb-1">
                                    <span class="text-slate-500">Tool Remaining Life</span>
                                    <span class="font-mono font-bold {{ (int)$machine->tool_life < 25 ? 'text-rose-600' : 'text-emerald-600' }}">{{ (int)$machine->tool_life }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ (int)$machine->tool_life < 25 ? 'bg-rose-500' : 'bg-emerald-500' }} h-1.5 rounded-full" style="width: {{ min(100, (int)$machine->tool_life) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Breakdown Incidents Log -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Breakdown & Incident Log
                </h3>
                <span class="text-[11px] font-medium text-slate-500">{{ $breakdowns->count() }} incidents</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Incident #</th>
                            <th class="py-3 px-4">Machine</th>
                            <th class="py-3 px-4">Issue Description</th>
                            <th class="py-3 px-4">Severity</th>
                            <th class="py-3 px-4">Downtime</th>
                            <th class="py-3 px-4">Reported By</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($breakdowns as $bk)
                            @php
                                $bSev = match($bk->severity) {
                                    'critical' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'high' => 'bg-orange-50 text-orange-700 border-orange-200',
                                    'medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                                $bStatus = match($bk->status) {
                                    'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'in_repair' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $bk->incident_number }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $bk->machine_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $bk->machine_code }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-700 max-w-xs">{{ $bk->issue_description }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $bSev }} uppercase font-mono">
                                        {{ $bk->severity }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-900 font-semibold">
                                    {{ $bk->downtime_minutes }} min
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $bk->reported_by }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $bStatus }} capitalize font-mono">
                                        {{ str_replace('_', ' ', $bk->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400 text-xs">No machine breakdown incidents reported.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Report Breakdown Modal -->
    <div id="newBreakdownModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Report Machine Breakdown Incident</h3>
                <button onclick="document.getElementById('newBreakdownModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('maintenance.breakdown.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Incident #</label>
                        <input type="text" name="incident_number" value="BD-{{ date('Ymd') }}-{{ rand(100,999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Machine Asset</label>
                        <select name="machine_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            @foreach($machines as $m)
                                <option value="{{ $m->id }}">{{ $m->code }} — {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Severity Classification</label>
                    <select name="severity" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                        <option value="low">Low (Minor noise, non-halting)</option>
                        <option value="medium">Medium (Reduced speed / feed rate)</option>
                        <option value="high" selected>High (Line halted)</option>
                        <option value="critical">Critical (Catastrophic tool / spindle failure)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Issue Description</label>
                    <textarea name="issue_description" rows="3" required placeholder="Describe alarm code, axis error, hydraulic pressure drop, vibration..." class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newBreakdownModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition">Log Incident Alert</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
