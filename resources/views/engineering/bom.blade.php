<x-app-layout>
    <x-slot:title>Bill of Materials (BOM) & Routings</x-slot:title>

    <div class="space-y-6" x-data="{ 
        activeTab: 'boms', 
        explodeBomId: '{{ $boms->first()?->id ?? 1 }}', 
        targetQty: 100,
        bomsData: {{ json_encode($boms) }}
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Bill of Materials & Routings</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-200 font-mono">ENG / Phase 6 Enterprise</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Multi-level component engineering, version control, manufacturing routing operations, and dynamic MRP explosion</p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="document.getElementById('newRoutingModal').classList.remove('hidden')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 shadow-xs flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>New Routing</span>
                </button>

                <button onclick="document.getElementById('newBomModal').classList.remove('hidden')" class="px-3.5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Master BOM</span>
                </button>
            </div>
        </div>

        <!-- 4 Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Active BOMs</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_boms'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Approved Revisions</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['approved_boms'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Standard Routings</div>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['total_routings'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Average Unit Mfg Cost</div>
                <div class="text-2xl font-black text-cyan-600 mt-1 font-mono">₹{{ number_format($stats['avg_cost'], 2) }}</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 overflow-x-auto text-xs font-bold">
            <button @click="activeTab = 'boms'" :class="activeTab === 'boms' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Multi-Level BOM Registry</span>
                <span class="px-1.5 py-0.2 rounded-md bg-white border border-slate-200 text-[10px] font-mono">{{ $boms->count() }}</span>
            </button>
            <button @click="activeTab = 'explosion'" :class="activeTab === 'explosion' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Interactive BOM Explosion (MRP)</span>
                <span class="px-1.5 py-0.2 rounded-md bg-amber-100 text-amber-800 text-[10px] font-mono">Live</span>
            </button>
            <button @click="activeTab = 'routings'" :class="activeTab === 'routings' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Manufacturing Routings</span>
                <span class="px-1.5 py-0.2 rounded-md bg-white border border-slate-200 text-[10px] font-mono">{{ $routings->count() }}</span>
            </button>
            <button @click="activeTab = 'costing'" :class="activeTab === 'costing' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Cost Rollup & Absorption</span>
            </button>
        </div>

        <!-- TAB 1: Multi-Level BOM Registry -->
        <div x-show="activeTab === 'boms'" class="space-y-6">
            @forelse($boms as $bom)
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:border-slate-300 transition">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span class="font-mono font-bold text-sm text-slate-900">{{ $bom->bom_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200 font-mono">{{ $bom->version }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $bom->approval_status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }} uppercase font-mono">
                                    {{ $bom->approval_status }}
                                </span>
                                @if($bom->routing_code)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                        Routing: {{ $bom->routing_code }} ({{ $bom->total_cycle_time_mins }}m)
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 mt-1">{{ $bom->product_name }}</h3>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">Part SKU: {{ $bom->product_code }} | Batch Yield: {{ (float)$bom->output_qty }} {{ $bom->uom }}</div>
                            @if($bom->change_reason)
                                <div class="text-[11px] text-slate-400 mt-1 italic">Revision Note: {{ $bom->change_reason }}</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                            <div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase">Material</div>
                                <div class="text-xs font-mono font-bold text-slate-800">₹{{ number_format($bom->material_cost, 2) }}</div>
                            </div>
                            <div class="w-px h-6 bg-slate-200"></div>
                            <div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase">Labour</div>
                                <div class="text-xs font-mono font-bold text-slate-800">₹{{ number_format($bom->labour_cost, 2) }}</div>
                            </div>
                            <div class="w-px h-6 bg-slate-200"></div>
                            <div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase">Overhead</div>
                                <div class="text-xs font-mono font-bold text-slate-800">₹{{ number_format($bom->overhead_cost, 2) }}</div>
                            </div>
                            <div class="w-px h-6 bg-slate-200"></div>
                            <div>
                                <div class="text-[9px] font-bold text-cyan-600 uppercase">Unit Cost</div>
                                <div class="text-sm font-mono font-black text-cyan-700">₹{{ number_format($bom->standard_cost, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Component Explosion Breakdown -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                            <span>Bill of Materials Structure ({{ $bom->components->count() }} Components)</span>
                            <span class="text-slate-400 lowercase">click explosion tab to simulate target run</span>
                        </div>

                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">Seq / Stage</th>
                                        <th class="py-2.5 px-3">Item / Description</th>
                                        <th class="py-2.5 px-3">Component Type</th>
                                        <th class="py-2.5 px-3 text-right">Qty / Unit</th>
                                        <th class="py-2.5 px-3 text-right">Scrap %</th>
                                        <th class="py-2.5 px-3 text-right">Unit Cost</th>
                                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                                        <th class="py-2.5 px-3">Warehouse / Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($bom->components as $c)
                                        @php
                                            $typeBadge = match($c->component_type) {
                                                'raw_material' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'semi_finished' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'packaging' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'consumable' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                default => 'bg-slate-50 text-slate-700 border-slate-200',
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-2.5 px-3 font-mono font-semibold text-slate-500">Op {{ $c->operation_seq }}</td>
                                            <td class="py-2.5 px-3">
                                                <div class="font-bold text-slate-900">{{ $c->component_name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $c->component_code }}</div>
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $typeBadge }} uppercase font-mono">
                                                    {{ str_replace('_', ' ', $c->component_type) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                                {{ (float)$c->quantity }} {{ $c->component_uom }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono text-slate-500">{{ (float)$c->scrap_percentage }}%</td>
                                            <td class="py-2.5 px-3 text-right font-mono text-slate-600">₹{{ number_format($c->unit_cost, 2) }}</td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">₹{{ number_format($c->subtotal_cost, 2) }}</td>
                                            <td class="py-2.5 px-3 text-[11px] text-slate-500">
                                                <div>{{ $c->issue_warehouse ?? 'Central Store' }}</div>
                                                <div class="text-[10px] text-slate-400 italic">{{ $c->remarks }}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-slate-400 text-xs">No component rows linked to this BOM.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-400 bg-white border border-slate-200 rounded-2xl">
                    No Bill of Materials created yet.
                </div>
            @endforelse
        </div>

        <!-- TAB 2: Interactive BOM Explosion (MRP) -->
        <div x-show="activeTab === 'explosion'" class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Material Requirements Planning (MRP Explosion)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Recursively calculate raw materials, scrap allowances, warehouse availability, and shortage analysis for any production run</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-600">Target Quantity:</label>
                            <input type="number" min="1" step="1" x-model="targetQty" class="w-24 text-xs font-mono font-bold rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-600">Select BOM:</label>
                            <select x-model="explodeBomId" class="text-xs font-bold rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                                @foreach($boms as $b)
                                    <option value="{{ $b->id }}">{{ $b->bom_number }} — {{ $b->product_name }} ({{ $b->version }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Live Calculated Matrix -->
                @foreach($boms as $b)
                    <div x-show="explodeBomId == '{{ $b->id }}'" class="space-y-4">
                        <div class="p-3 bg-cyan-50 border border-cyan-200 rounded-xl flex items-center justify-between">
                            <div class="text-xs text-cyan-900">
                                Simulating Production Run for: <strong class="font-bold">{{ $b->product_name }}</strong> (<span class="font-mono">{{ $b->product_code }}</span>) 
                                | Target Output: <span class="font-bold font-mono" x-text="targetQty"></span> {{ $b->uom }}
                            </div>
                            <div class="text-xs font-mono font-black text-cyan-900">
                                Est Total Mfg Cost: ₹<span x-text="((targetQty * {{ $b->standard_cost }})).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>

                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">Item / SKU</th>
                                        <th class="py-2.5 px-3">Type</th>
                                        <th class="py-2.5 px-3 text-right">Qty / Finished Unit</th>
                                        <th class="py-2.5 px-3 text-right">Gross Lot Required</th>
                                        <th class="py-2.5 px-3 text-right">Scrap Adjusted Net</th>
                                        <th class="py-2.5 px-3 text-right">Warehouse Current Stock</th>
                                        <th class="py-2.5 px-3 text-center">Shortage Status</th>
                                        <th class="py-2.5 px-3 text-right">Estimated Line Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @foreach($b->components as $c)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-2.5 px-3">
                                                <div class="font-bold text-slate-900">{{ $c->component_name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $c->component_code }}</div>
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-700 uppercase font-mono">
                                                    {{ str_replace('_', ' ', $c->component_type) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-700">
                                                {{ (float)$c->quantity }} {{ $c->component_uom }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900" x-text="((targetQty * {{ (float)$c->quantity }})).toFixed(2) + ' {{ $c->component_uom }}'">
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-cyan-700" x-text="((targetQty * {{ (float)$c->quantity }} * (1 + ({{ (float)$c->scrap_percentage }} / 100)))).toFixed(2) + ' {{ $c->component_uom }}'">
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-800">
                                                {{ (float)$c->current_stock }} {{ $c->component_uom }}
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                <template x-if="({{ (float)$c->current_stock }} >= (targetQty * {{ (float)$c->quantity }}))">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        ✓ In Stock
                                                    </span>
                                                </template>
                                                <template x-if="({{ (float)$c->current_stock }} < (targetQty * {{ (float)$c->quantity }}))">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                        ⚠ Shortage: <span x-text="((targetQty * {{ (float)$c->quantity }}) - {{ (float)$c->current_stock }}).toFixed(1)"></span>
                                                    </span>
                                                </template>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900" x-text="'₹' + ((targetQty * {{ (float)$c->quantity }} * {{ (float)$c->unit_cost }})).toLocaleString('en-IN', {minimumFractionDigits: 2})">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- TAB 3: Manufacturing Routings -->
        <div x-show="activeTab === 'routings'" class="space-y-6">
            @forelse($routings as $routing)
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-black text-sm text-slate-900">{{ $routing->routing_code }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 font-mono">{{ $routing->version }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase font-mono">{{ $routing->status }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 mt-1">{{ $routing->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $routing->description }}</p>
                        </div>

                        <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200">
                            <div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase">Total Cycle Time</div>
                                <div class="text-sm font-mono font-black text-slate-900">{{ $routing->total_cycle_time_mins }} mins</div>
                            </div>
                            <div class="w-px h-6 bg-slate-200"></div>
                            <div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase">Operations</div>
                                <div class="text-sm font-mono font-black text-blue-600">{{ $routing->operations->count() }} steps</div>
                            </div>
                        </div>
                    </div>

                    <!-- Operations Timeline -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3">Sequence</th>
                                    <th class="py-2.5 px-3">Operation Name</th>
                                    <th class="py-2.5 px-3">Work Center / Machine</th>
                                    <th class="py-2.5 px-3 text-right">Setup Time</th>
                                    <th class="py-2.5 px-3 text-right">Cycle Run Time</th>
                                    <th class="py-2.5 px-3 text-right">Hourly Rate</th>
                                    <th class="py-2.5 px-3 text-center">QC Checkpoint</th>
                                    <th class="py-2.5 px-3">Standard Operating Instructions (SOP)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($routing->operations as $op)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-2.5 px-3 font-mono font-black text-cyan-600">Step {{ $op->sequence_no }}</td>
                                        <td class="py-2.5 px-3 font-bold text-slate-900">{{ $op->operation_name }}</td>
                                        <td class="py-2.5 px-3 text-slate-700">{{ $op->work_center }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono text-slate-600">{{ $op->setup_time_mins }}m</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">{{ $op->run_time_mins }}m</td>
                                        <td class="py-2.5 px-3 text-right font-mono text-slate-600">₹{{ number_format($op->hourly_cost, 2) }}/hr</td>
                                        <td class="py-2.5 px-3 text-center">
                                            @if($op->requires_qc)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    Mandatory QA
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[10px]">Self-Inspect</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-xs text-slate-500 italic max-w-xs">{{ $op->instructions }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-400 bg-white border border-slate-200 rounded-2xl">
                    No manufacturing routings configured.
                </div>
            @endforelse
        </div>

        <!-- TAB 4: Cost Rollup & Absorption -->
        <div x-show="activeTab === 'costing'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($boms as $b)
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="font-mono text-xs font-bold text-slate-400">{{ $b->bom_number }}</span>
                                <h4 class="text-sm font-black text-slate-900">{{ $b->product_name }}</h4>
                            </div>
                            <div class="text-right">
                                <div class="text-[9px] uppercase font-bold text-slate-400">Total Unit Standard Cost</div>
                                <div class="font-mono text-lg font-black text-cyan-600">₹{{ number_format($b->standard_cost, 2) }}</div>
                            </div>
                        </div>

                        <!-- Cost Breakdown Visual -->
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-slate-600 font-medium">Direct Raw Materials</span>
                                    <span class="font-mono font-bold text-slate-900">₹{{ number_format($b->material_cost, 2) }} ({{ $b->standard_cost > 0 ? round(($b->material_cost / $b->standard_cost) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $b->standard_cost > 0 ? min(100, ($b->material_cost / $b->standard_cost) * 100) : 0 }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-slate-600 font-medium">Direct Shop Floor Labour</span>
                                    <span class="font-mono font-bold text-slate-900">₹{{ number_format($b->labour_cost, 2) }} ({{ $b->standard_cost > 0 ? round(($b->labour_cost / $b->standard_cost) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $b->standard_cost > 0 ? min(100, ($b->labour_cost / $b->standard_cost) * 100) : 0 }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-slate-600 font-medium">Factory Overhead Absorption</span>
                                    <span class="font-mono font-bold text-slate-900">₹{{ number_format($b->overhead_cost, 2) }} ({{ $b->standard_cost > 0 ? round(($b->overhead_cost / $b->standard_cost) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-cyan-500 h-2 rounded-full" style="width: {{ $b->standard_cost > 0 ? min(100, ($b->overhead_cost / $b->standard_cost) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Create New Master BOM Modal -->
    <div id="newBomModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-3xl w-full p-6 border border-slate-200 shadow-xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Create Comprehensive Bill of Materials (BOM)</h3>
                <button onclick="document.getElementById('newBomModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('bom.store') }}" class="space-y-4" id="bomForm">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">BOM Code #</label>
                        <input type="text" name="bom_number" value="BOM-{{ date('Y') }}-{{ rand(100,999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Finished Product</label>
                        <select name="product_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">BOM Revision / Version</label>
                        <input type="text" name="version" value="REV-01" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Output Batch Yield</label>
                        <input type="number" step="0.01" name="output_qty" value="1.00" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Manufacturing Routing</label>
                        <select name="routing_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                            <option value="">-- No Routing Assigned --</option>
                            @foreach($routings as $r)
                                <option value="{{ $r->id }}">{{ $r->routing_code }} — {{ $r->name }} ({{ $r->version }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Effective Release Date</label>
                        <input type="date" name="effective_date" value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <!-- Components Multi-Row Table -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">BOM Components & Sub-Assemblies</label>
                        <button type="button" onclick="addComponentRow()" class="text-xs text-cyan-600 font-bold hover:underline cursor-pointer">+ Add Component</button>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs" id="componentsTable">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-2 px-3">Item / SKU</th>
                                    <th class="py-2 px-3">Type</th>
                                    <th class="py-2 px-3 text-right">Qty</th>
                                    <th class="py-2 px-3 text-right">Scrap %</th>
                                    <th class="py-2 px-3">Warehouse</th>
                                    <th class="py-2 px-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="componentsBody">
                                <tr>
                                    <td class="py-2 px-3">
                                        <select name="components[0][product_id]" required class="w-full text-xs rounded-lg border-slate-200">
                                            @foreach($products as $prod)
                                                <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2 px-3">
                                        <select name="components[0][component_type]" class="w-full text-xs rounded-lg border-slate-200">
                                            <option value="raw_material">Raw Material</option>
                                            <option value="semi_finished">Semi-Finished</option>
                                            <option value="consumable">Consumable</option>
                                            <option value="packaging">Packaging</option>
                                        </select>
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="number" step="0.0001" min="0.0001" name="components[0][quantity]" value="1.0000" required class="w-20 text-xs font-mono text-right rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="number" step="0.1" min="0" name="components[0][scrap_percentage]" value="1.0" class="w-16 text-xs font-mono text-right rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="text" name="components[0][issue_warehouse]" placeholder="Central Store" class="w-full text-xs rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" onclick="this.closest('tr').remove()" class="text-rose-500 font-bold hover:text-rose-700">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Engineering Revision Notes</label>
                    <textarea name="notes" rows="2" placeholder="Standard ANSI tolerance specifications, heat treatment requirements..." class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newBomModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition">Publish Approved BOM</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create New Routing Modal -->
    <div id="newRoutingModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-3xl w-full p-6 border border-slate-200 shadow-xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Define Manufacturing Routing & Operation Steps</h3>
                <button onclick="document.getElementById('newRoutingModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('bom.routings.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Routing Code #</label>
                        <input type="text" name="routing_code" value="RT-{{ date('Y') }}-{{ rand(100,999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Routing Name</label>
                        <input type="text" name="name" placeholder="e.g. 5-Axis Flange Precision Routing" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Version</label>
                        <input type="text" name="version" value="v1.0" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Routing Description</label>
                    <textarea name="description" rows="2" placeholder="Standard machining and quality control path for product line..." class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"></textarea>
                </div>

                <!-- Operations Steps -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Sequential Manufacturing Operations</label>
                        <button type="button" onclick="addOperationRow()" class="text-xs text-cyan-600 font-bold hover:underline cursor-pointer">+ Add Operation</button>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs" id="operationsTable">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-2 px-3">Seq</th>
                                    <th class="py-2 px-3">Operation Name</th>
                                    <th class="py-2 px-3">Work Center</th>
                                    <th class="py-2 px-3 text-right">Run Time (m)</th>
                                    <th class="py-2 px-3 text-right">Hourly Rate (₹)</th>
                                    <th class="py-2 px-3 text-center">QC Check?</th>
                                    <th class="py-2 px-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="operationsBody">
                                <tr>
                                    <td class="py-2 px-3">
                                        <input type="number" step="10" name="operations[0][sequence_no]" value="10" required class="w-14 text-xs font-mono text-center rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="text" name="operations[0][operation_name]" value="Rough CNC Turning" required class="w-full text-xs rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="text" name="operations[0][work_center]" value="CNC Bay 1" required class="w-full text-xs rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="number" name="operations[0][run_time_mins]" value="30" required class="w-16 text-xs font-mono text-right rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="number" name="operations[0][hourly_cost]" value="550" required class="w-20 text-xs font-mono text-right rounded-lg border-slate-200">
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <input type="checkbox" name="operations[0][requires_qc]" value="1" class="rounded text-cyan-600 focus:ring-cyan-500">
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" onclick="this.closest('tr').remove()" class="text-rose-500 font-bold hover:text-rose-700">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newRoutingModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition">Save Routing</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let compIndex = 1;
        function addComponentRow() {
            const tableBody = document.getElementById('componentsBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 px-3">
                    <select name="components[${compIndex}][product_id]" required class="w-full text-xs rounded-lg border-slate-200">
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="py-2 px-3">
                    <select name="components[${compIndex}][component_type]" class="w-full text-xs rounded-lg border-slate-200">
                        <option value="raw_material">Raw Material</option>
                        <option value="semi_finished">Semi-Finished</option>
                        <option value="consumable">Consumable</option>
                        <option value="packaging">Packaging</option>
                    </select>
                </td>
                <td class="py-2 px-3">
                    <input type="number" step="0.0001" min="0.0001" name="components[${compIndex}][quantity]" value="1.0000" required class="w-20 text-xs font-mono text-right rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3">
                    <input type="number" step="0.1" min="0" name="components[${compIndex}][scrap_percentage]" value="0.0" class="w-16 text-xs font-mono text-right rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3">
                    <input type="text" name="components[${compIndex}][issue_warehouse]" placeholder="Central Store" class="w-full text-xs rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-500 font-bold hover:text-rose-700">✕</button>
                </td>
            `;
            tableBody.appendChild(row);
            compIndex++;
        }

        let opIndex = 1;
        function addOperationRow() {
            const tableBody = document.getElementById('operationsBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 px-3">
                    <input type="number" step="10" name="operations[${opIndex}][sequence_no]" value="${(opIndex + 1) * 10}" required class="w-14 text-xs font-mono text-center rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3">
                    <input type="text" name="operations[${opIndex}][operation_name]" placeholder="Operation Name" required class="w-full text-xs rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3">
                    <input type="text" name="operations[${opIndex}][work_center]" placeholder="Work Center" required class="w-full text-xs rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3">
                    <input type="number" name="operations[${opIndex}][run_time_mins]" value="20" required class="w-16 text-xs font-mono text-right rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3">
                    <input type="number" name="operations[${opIndex}][hourly_cost]" value="500" required class="w-20 text-xs font-mono text-right rounded-lg border-slate-200">
                </td>
                <td class="py-2 px-3 text-center">
                    <input type="checkbox" name="operations[${opIndex}][requires_qc]" value="1" class="rounded text-cyan-600 focus:ring-cyan-500">
                </td>
                <td class="py-2 px-3 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-500 font-bold hover:text-rose-700">✕</button>
                </td>
            `;
            tableBody.appendChild(row);
            opIndex++;
        }
    </script>
</x-app-layout>
