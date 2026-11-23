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
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Finished Product</label>
                            <button type="button" onclick="openQuickProductModal()" class="text-[10px] font-bold text-cyan-600 hover:text-cyan-800 hover:underline cursor-pointer flex items-center gap-1">
                                <span>+ Add New Product</span>
                            </button>
                        </div>
                        <select name="product_id" id="woProductSelect" onchange="if(this.value==='NEW_PROD'){openQuickProductModal(); this.value='{{ $products->first()?->id }}';}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="NEW_PROD" class="font-bold text-cyan-600 bg-cyan-50">+ Add New Product / Finished Good...</option>
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
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Bill of Materials (BOM)</label>
                            <button type="button" onclick="openQuickBomModal()" class="text-[10px] font-bold text-cyan-600 hover:text-cyan-800 hover:underline cursor-pointer flex items-center gap-1">
                                <span>+ Add New BOM</span>
                            </button>
                        </div>
                        <select name="bom_header_id" id="woBomSelect" onchange="if(this.value==='NEW_BOM'){openQuickBomModal(); this.value='';}" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">-- No BOM Assigned --</option>
                            <option value="NEW_BOM" class="font-bold text-cyan-600 bg-cyan-50">+ Add New BOM...</option>
                            @foreach($boms as $b)
                                <option value="{{ $b->id }}">{{ $b->bom_number }} ({{ $b->version }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Manufacturing Routing</label>
                            <button type="button" onclick="openQuickRoutingModal()" class="text-[10px] font-bold text-cyan-600 hover:text-cyan-800 hover:underline cursor-pointer flex items-center gap-1">
                                <span>+ Add New Routing</span>
                            </button>
                        </div>
                        <select name="routing_id" id="woRoutingSelect" onchange="if(this.value==='NEW_ROUTING'){openQuickRoutingModal(); this.value='';}" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">-- No Routing Assigned --</option>
                            <option value="NEW_ROUTING" class="font-bold text-cyan-600 bg-cyan-50">+ Add New Routing...</option>
                            @foreach($routings as $r)
                                <option value="{{ $r->id }}">{{ $r->routing_code }} — {{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Work Center / Line</label>
                            <button type="button" onclick="openQuickWorkCenterModal()" class="text-[10px] font-bold text-amber-600 hover:text-amber-800 hover:underline cursor-pointer flex items-center gap-1">
                                <span>+ Add New Line</span>
                            </button>
                        </div>
                        <select name="work_center" id="woWorkCenterSelect" onchange="if(this.value==='NEW_LINE'){openQuickWorkCenterModal(); this.value='CNC Milling Line A (Pune)';}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="NEW_LINE" class="font-bold text-amber-600 bg-amber-50">+ Add New Work Center / Line...</option>
                            @foreach($workCenters as $wc)
                                <option value="{{ $wc }}" {{ $wc === 'CNC Milling Line A (Pune)' ? 'selected' : '' }}>{{ $wc }}</option>
                            @endforeach
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

    <!-- Quick Add Product Modal -->
    <div id="quickProductModal" class="hidden fixed inset-0 z-60 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-2xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                    <h3 class="text-sm font-black text-slate-900">Create New Product / Finished Good</h3>
                </div>
                <button type="button" onclick="closeQuickProductModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Item Type</label>
                        <select name="type" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-bold">
                            <option value="finished_good" selected>Finished Good (FG)</option>
                            <option value="semi_finished">Semi-Finished Assembly</option>
                            <option value="raw_material">Raw Material</option>
                            <option value="consumable">Consumable / Tool</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">SKU / Item Code</label>
                        <input type="text" name="code" placeholder="e.g. PART-FLG-500" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono uppercase font-bold">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Product Name</label>
                    <input type="text" name="name" placeholder="e.g. Heavy Duty ANSI Flange 500mm Class 300" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Category</label>
                        <input type="text" name="category" placeholder="Flanges & Valves" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Unit of Measure</label>
                        <select name="uom" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                            <option value="Pcs" selected>Pcs</option>
                            <option value="Kg">Kg</option>
                            <option value="Meters">Meters</option>
                            <option value="Sets">Sets</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Standard Cost (₹)</label>
                        <input type="number" step="0.01" min="0" name="standard_cost" value="2500.00" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono font-bold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Initial Stock</label>
                        <input type="number" step="0.01" min="0" name="current_stock" value="0.00" class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Reorder Level</label>
                        <input type="number" step="0.01" min="0" name="reorder_level" value="10.00" class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeQuickProductModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition">Save & Select Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Add BOM Modal -->
    <div id="quickBomModal" class="hidden fixed inset-0 z-60 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-2xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                    <h3 class="text-sm font-black text-slate-900">Quick Create Bill of Materials (BOM)</h3>
                </div>
                <button type="button" onclick="closeQuickBomModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('bom.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">BOM Code #</label>
                        <input type="text" name="bom_number" value="BOM-{{ date('Y') }}-{{ rand(100,999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Revision Version</label>
                        <input type="text" name="version" value="REV-01" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Finished Product</label>
                        <select name="product_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Batch Yield</label>
                        <input type="number" step="0.01" name="output_qty" value="1.00" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <!-- Primary Component Row -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Primary Raw Material Component</div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-2">
                            <select name="components[0][product_id]" required class="w-full text-xs rounded-lg border-slate-200">
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="number" step="0.0001" min="0.0001" name="components[0][quantity]" value="1.0000" placeholder="Qty" required class="w-full text-xs font-mono rounded-lg border-slate-200">
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Engineering Notes</label>
                    <textarea name="notes" rows="2" placeholder="Quick BOM definition created from Work Order screen..." class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeQuickBomModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition">Create & Link BOM</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Add Routing Modal -->
    <div id="quickRoutingModal" class="hidden fixed inset-0 z-60 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-2xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                    <h3 class="text-sm font-black text-slate-900">Quick Create Manufacturing Routing</h3>
                </div>
                <button type="button" onclick="closeQuickRoutingModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('bom.routings.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Routing Code #</label>
                        <input type="text" name="routing_code" value="RT-{{ date('Y') }}-{{ rand(100,999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Version</label>
                        <input type="text" name="version" value="v1.0" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Routing Sequence Name</label>
                    <input type="text" name="name" placeholder="e.g. 4-Axis CNC Milling & QA Inspection" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                </div>

                <!-- Primary Operation Row -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Primary Operation Step (Op 10)</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] text-slate-500">Operation Name</label>
                            <input type="text" name="operations[0][operation_name]" value="CNC Machining & Turning" required class="w-full text-xs rounded-lg border-slate-200">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500">Work Center</label>
                            <input type="text" name="operations[0][work_center]" value="CNC Milling Line A (Pune)" required class="w-full text-xs rounded-lg border-slate-200">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="text-[10px] text-slate-500">Setup (min)</label>
                            <input type="number" name="operations[0][setup_time_mins]" value="15" class="w-full text-xs font-mono rounded-lg border-slate-200">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500">Run (min)</label>
                            <input type="number" name="operations[0][run_time_mins]" value="30" class="w-full text-xs font-mono rounded-lg border-slate-200">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500">Hourly Rate (₹)</label>
                            <input type="number" name="operations[0][hourly_cost]" value="550" class="w-full text-xs font-mono rounded-lg border-slate-200">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeQuickRoutingModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition">Create & Link Routing</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Add Work Center Modal -->
    <div id="quickWorkCenterModal" class="hidden fixed inset-0 z-60 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="text-sm font-black text-slate-900">Add New Work Center / Production Line</h3>
                </div>
                <button type="button" onclick="closeQuickWorkCenterModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('work-orders.work-centers.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Work Center / Production Line Name</label>
                    <input type="text" id="newWcNameInput" name="work_center" placeholder="e.g. 5-Axis Horizontal Machining Cell 3" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Machine Code</label>
                        <input type="text" name="code" value="MC-LINE-{{ rand(10,99) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Asset Unit Name</label>
                        <input type="text" name="name" value="Production Cell Asset" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="useLineDirectly()" class="text-xs text-amber-600 font-bold hover:underline cursor-pointer">
                        Use Immediately in Form
                    </button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeQuickWorkCenterModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition">Save Work Center</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openQuickProductModal() {
            document.getElementById('quickProductModal').classList.remove('hidden');
        }
        function closeQuickProductModal() {
            document.getElementById('quickProductModal').classList.add('hidden');
        }

        function openOutputModal(id, num, prodName, planned, completed, uom) {
            document.getElementById('outputForm').action = '/work-orders/' + id + '/record-output';
            document.getElementById('modalWoNum').innerText = num;
            document.getElementById('modalProdName').innerText = prodName;
            document.getElementById('modalProgress').innerText = completed + ' / ' + planned + ' ' + uom;
            document.getElementById('outputModal').classList.remove('hidden');
        }

        function openQuickBomModal() {
            document.getElementById('quickBomModal').classList.remove('hidden');
        }
        function closeQuickBomModal() {
            document.getElementById('quickBomModal').classList.add('hidden');
        }

        function openQuickRoutingModal() {
            document.getElementById('quickRoutingModal').classList.remove('hidden');
        }
        function closeQuickRoutingModal() {
            document.getElementById('quickRoutingModal').classList.add('hidden');
        }

        function openQuickWorkCenterModal() {
            document.getElementById('quickWorkCenterModal').classList.remove('hidden');
        }
        function closeQuickWorkCenterModal() {
            document.getElementById('quickWorkCenterModal').classList.add('hidden');
        }

        function useLineDirectly() {
            const val = document.getElementById('newWcNameInput').value.trim();
            if (val) {
                const select = document.getElementById('woWorkCenterSelect');
                const opt = document.createElement('option');
                opt.value = val;
                opt.text = val;
                opt.selected = true;
                select.appendChild(opt);
                closeQuickWorkCenterModal();
            }
        }
    </script>
</x-app-layout>
