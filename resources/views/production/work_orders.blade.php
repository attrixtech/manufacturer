<x-app-layout>
    <x-slot:title>Work Orders & Shop Floor MES</x-slot:title>

    <div class="space-y-6" x-data="{ 
        activeTab: 'dispatch', 
        selectedWo: null,
        filterPriority: 'all',
        filterStatus: 'all'
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Work Orders & Manufacturing Execution (MES)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 font-mono">MES / Shop Floor Live</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Job order dispatching, material availability & kitting check, operation progress tracking, and scrap recording</p>
            </div>

            <button onclick="document.getElementById('newWoModal').classList.remove('hidden')" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Dispatch Work Order</span>
            </button>
        </div>

        <!-- 4 Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Dispatched WOs</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Running on Floor</div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['in_progress'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Urgent / High Priority</div>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['urgent_count'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Completed Batches</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['completed'] }}</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 overflow-x-auto text-xs font-bold">
            <button @click="activeTab = 'dispatch'" :class="activeTab === 'dispatch' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Shop Floor Dispatch Board</span>
                <span class="px-1.5 py-0.2 rounded-md bg-white border border-slate-200 text-[10px] font-mono">{{ $workOrders->count() }}</span>
            </button>
            <button @click="activeTab = 'materials'" :class="activeTab === 'materials' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Material Kitting & Shortage Analysis</span>
                <span class="px-1.5 py-0.2 rounded-md bg-rose-100 text-rose-700 text-[10px] font-mono">BOM Sync</span>
            </button>
            <button @click="activeTab = 'routings'" :class="activeTab === 'routings' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'text-slate-600 hover:bg-slate-100 border-transparent'" class="px-4 py-2 rounded-xl border transition flex items-center gap-2 cursor-pointer">
                <span>Work Center & Machine Lines</span>
            </button>
        </div>

        <!-- TAB 1: Shop Floor Dispatch Board -->
        <div x-show="activeTab === 'dispatch'" class="space-y-4">
            <!-- Filter Bar -->
            <div class="bg-white border border-slate-200 rounded-xl p-3 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 font-bold">Status:</span>
                        <select x-model="filterStatus" class="text-xs rounded-lg border-slate-200">
                            <option value="all">All Statuses</option>
                            <option value="released">Released</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 font-bold">Priority:</span>
                        <select x-model="filterPriority" class="text-xs rounded-lg border-slate-200">
                            <option value="all">All Priorities</option>
                            <option value="urgent">Urgent</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="text-slate-400 text-[11px]">Showing verified shop floor production orders</div>
            </div>

            <!-- Work Orders Table -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">WO # / Order Ref</th>
                                <th class="py-3 px-4">Finished Item / SKU</th>
                                <th class="py-3 px-4">Production Line / Supervisor</th>
                                <th class="py-3 px-4 text-center">Priority</th>
                                <th class="py-3 px-4 text-center">Material Kitting</th>
                                <th class="py-3 px-4">Yield Progress</th>
                                <th class="py-3 px-4">Target Date</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Shop Floor Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($workOrders as $wo)
                                @php
                                    $prioBadge = match($wo->priority) {
                                        'urgent' => 'bg-rose-50 text-rose-700 border-rose-200 font-black',
                                        'high' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'medium' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                    $statusBadge = match($wo->status) {
                                        'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'released' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'on_hold' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                    $matBadge = match($wo->material_status) {
                                        'available', 'issued' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'shortage' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition" 
                                    x-show="(filterStatus === 'all' || filterStatus === '{{ $wo->status }}') && (filterPriority === 'all' || filterPriority === '{{ $wo->priority }}')">
                                    <td class="py-3 px-4">
                                        <div class="font-mono font-black text-slate-900 text-xs">{{ $wo->wo_number }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $wo->sales_order_ref ?? 'MTS' }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900">{{ $wo->product_name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1.5">
                                            <span>{{ $wo->product_code }}</span>
                                            @if($wo->bom_number)
                                                <span>• BOM: {{ $wo->bom_number }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-800">{{ $wo->work_center }}</div>
                                        <div class="text-[10px] text-slate-400">Sup: <span class="text-slate-600 font-medium">{{ $wo->supervisor ?? 'Shop Floor Lead' }}</span></div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $prioBadge }} uppercase font-mono">
                                            {{ $wo->priority }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $matBadge }} uppercase font-mono">
                                            {{ $wo->material_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 min-w-[150px]">
                                        <div class="flex items-center justify-between text-[11px] font-mono mb-1">
                                            <span class="font-bold text-slate-900">{{ (int)$wo->completed_qty }} / {{ (int)$wo->planned_qty }} {{ $wo->uom }}</span>
                                            <span class="text-slate-500 font-semibold">{{ $wo->progress_pct }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-amber-500 h-1.5 rounded-full transition-all" style="width: {{ $wo->progress_pct }}%"></div>
                                        </div>
                                        @if($wo->scrap_qty > 0)
                                            <div class="text-[10px] text-rose-600 font-mono mt-0.5 font-semibold">Scrap: {{ (int)$wo->scrap_qty }} pcs</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-[11px] text-slate-500 font-mono">
                                        {{ $wo->target_date ? \Carbon\Carbon::parse($wo->target_date)->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }} uppercase font-mono">
                                            {{ str_replace('_', ' ', $wo->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button onclick="openOutputModal('{{ $wo->id }}', '{{ $wo->wo_number }}', '{{ $wo->product_name }}', '{{ $wo->planned_qty }}', '{{ $wo->completed_qty }}', '{{ $wo->uom }}')" 
                                                    class="px-2.5 py-1 text-[11px] font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-2xs transition cursor-pointer">
                                                Log Run
                                            </button>

                                            <!-- Status Quick Menu -->
                                            <form method="POST" action="{{ route('work-orders.update-status', $wo->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" onchange="this.form.submit()" class="text-[10px] font-bold rounded-lg border-slate-200 py-1 px-1 bg-slate-50">
                                                    <option value="released" {{ $wo->status === 'released' ? 'selected' : '' }}>Released</option>
                                                    <option value="in_progress" {{ $wo->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="completed" {{ $wo->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="on_hold" {{ $wo->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                </select>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-8 text-slate-400 text-xs">No active manufacturing work orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: Material Kitting & Shortage Analysis -->
        <div x-show="activeTab === 'materials'" class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900">Work Order Material Availability Matrix</h3>
                    <p class="text-xs text-slate-500">Live comparison between BOM component requirements and warehouse current stock</p>
                </div>

                <div class="space-y-6">
                    @foreach($workOrders as $wo)
                        @if(count($wo->components) > 0)
                            <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/30">
                                <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-xs text-slate-900">{{ $wo->wo_number }}</span>
                                        <span class="text-xs font-semibold text-slate-700">— {{ $wo->product_name }} (Target: {{ (int)$wo->planned_qty }} {{ $wo->uom }})</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $wo->has_shortage ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $wo->has_shortage ? '⚠ Shortage Detected' : '✓ All Materials In Stock' }}
                                    </span>
                                </div>

                                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                                            <tr>
                                                <th class="py-2 px-3">BOM Component</th>
                                                <th class="py-2 px-3 text-right">Required for Lot</th>
                                                <th class="py-2 px-3 text-right">Available in Stores</th>
                                                <th class="py-2 px-3 text-right">Shortage</th>
                                                <th class="py-2 px-3 text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($wo->components as $c)
                                                <tr>
                                                    <td class="py-2 px-3">
                                                        <span class="font-bold text-slate-800">{{ $c->item_name }}</span>
                                                        <span class="text-slate-400 font-mono text-[10px] ml-1">({{ $c->item_code }})</span>
                                                    </td>
                                                    <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                                                        {{ number_format($c->required_qty, 2) }} {{ $c->item_uom }}
                                                    </td>
                                                    <td class="py-2 px-3 text-right font-mono text-slate-700">
                                                        {{ number_format((float)$c->current_stock, 2) }} {{ $c->item_uom }}
                                                    </td>
                                                    <td class="py-2 px-3 text-right font-mono font-bold {{ $c->shortage_qty > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                                        {{ $c->shortage_qty > 0 ? '-' . number_format($c->shortage_qty, 2) : '0.00' }}
                                                    </td>
                                                    <td class="py-2 px-3 text-center">
                                                        @if($c->shortage_qty > 0)
                                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                                Raise PO
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                Ready to Kit
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TAB 3: Work Center & Machine Lines -->
        <div x-show="activeTab === 'routings'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($machines as $m)
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-mono text-[10px] font-bold text-slate-400">{{ $m->code }}</span>
                                <h4 class="text-sm font-black text-slate-900">{{ $m->name }}</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase font-mono">
                                {{ $m->status }}
                            </span>
                        </div>

                        <div class="text-xs text-slate-500">Assigned Line: <strong class="text-slate-800">{{ $m->work_center }}</strong></div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-mono">
                            <span class="text-slate-500">Spindle Load: <strong class="text-slate-800">{{ $m->spindle_load }}%</strong></span>
                            <span class="text-slate-500">Pressure: <strong class="text-slate-800">{{ $m->coolant_pressure }} Bar</strong></span>
                            <span class="text-slate-500">Tool Life: <strong class="text-slate-800">{{ $m->tool_life }}%</strong></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Record Production Output Modal -->
    <div id="outputModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Record Production Run Output</h3>
                <button onclick="document.getElementById('outputModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form id="outputForm" method="POST" action="" class="space-y-4">
                @csrf
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1">
                    <div class="text-xs text-slate-500">Work Order: <span id="modalWoNum" class="font-mono font-bold text-slate-900"></span></div>
                    <div class="text-xs text-slate-500">Item: <span id="modalProdName" class="font-bold text-slate-900"></span></div>
                    <div class="text-xs text-slate-500">Progress: <span id="modalProgress" class="font-mono font-bold text-amber-600"></span></div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Good Produced (+Qty)</label>
                        <input type="number" step="1" min="1" name="completed_delta" value="10" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Scrap / Defect (+Qty)</label>
                        <input type="number" step="1" min="0" name="scrap_delta" value="0" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 font-mono">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Operator Run Notes</label>
                    <textarea name="operator_notes" rows="2" placeholder="Shift 1 run, tooling wear within limit..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('outputModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition">Submit Output</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dispatch Work Order Modal -->
    <div id="newWoModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 border border-slate-200 shadow-xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Dispatch Work Order to Shop Floor</h3>
                <button onclick="document.getElementById('newWoModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('work-orders.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">WO Number</label>
                        <input type="text" name="wo_number" value="WO-{{ date('Y') }}-{{ rand(1000,9999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Priority</label>
                        <select name="priority" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Sales Order Ref</label>
                        <input type="text" name="sales_order_ref" placeholder="SO-2026-0491 or MTS" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Finished Product</label>
                        <select name="product_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Planned Lot Quantity</label>
                        <input type="number" step="1" min="1" name="planned_qty" value="100" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Bill of Materials (BOM)</label>
                        <select name="bom_header_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">-- No BOM Assigned --</option>
                            @foreach($boms as $b)
                                <option value="{{ $b->id }}">{{ $b->bom_number }} ({{ $b->version }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Manufacturing Routing</label>
                        <select name="routing_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">-- No Routing Assigned --</option>
                            @foreach($routings as $r)
                                <option value="{{ $r->id }}">{{ $r->routing_code }} — {{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Work Center / Line</label>
                        <select name="work_center" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="CNC Milling Line A (Pune)">CNC Milling Line A (Pune)</option>
                            <option value="Robotic Arc Welding Cell 2">Robotic Arc Welding Cell 2</option>
                            <option value="VMC Precision Line 4">VMC Precision Line 4</option>
                            <option value="Heat Treatment Furnace 1">Heat Treatment Furnace 1</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Assigned Shift Supervisor</label>
                        <input type="text" name="supervisor" value="{{ auth()->user()->name }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Scheduled Start Date</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Target Completion Date</label>
                        <input type="date" name="target_date" value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Special Execution Notes</label>
                    <textarea name="notes" rows="2" placeholder="Quality hold requirements, customer expedited delivery..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newWoModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition">Release to Floor</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openOutputModal(id, num, prodName, planned, completed, uom) {
            document.getElementById('outputForm').action = '/work-orders/' + id + '/record-output';
            document.getElementById('modalWoNum').innerText = num;
            document.getElementById('modalProdName').innerText = prodName;
            document.getElementById('modalProgress').innerText = completed + ' / ' + planned + ' ' + uom;
            document.getElementById('outputModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
