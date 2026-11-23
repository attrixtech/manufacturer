<x-app-layout>
    <x-slot:title>Work Orders & Shop Floor MES</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Work Orders & Shop Floor MES</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 font-mono">Phase 7 Live</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Manufacturing execution, work order dispatch, routing progress, and shop floor tracking</p>
            </div>

            <button onclick="document.getElementById('newWoModal').classList.remove('hidden')" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Work Order</span>
            </button>
        </div>

        <!-- 4 Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Work Orders</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">In Progress</div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['in_progress'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Released to Floor</div>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['released'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Completed</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['completed'] }}</div>
            </div>
        </div>

        <!-- Work Orders Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">WO Number</th>
                            <th class="py-3 px-4">Product / Item</th>
                            <th class="py-3 px-4">Work Center</th>
                            <th class="py-3 px-4">Progress / Qty</th>
                            <th class="py-3 px-4">Target Date</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($workOrders as $wo)
                            @php
                                $percent = $wo->planned_qty > 0 ? round(($wo->completed_qty / $wo->planned_qty) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-amber-700">
                                    {{ $wo->wo_number }}
                                    @if($wo->bom_number)
                                        <div class="text-[10px] text-slate-400 font-normal font-sans">{{ $wo->bom_number }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $wo->product_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $wo->product_code }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-700">
                                    {{ $wo->work_center }}
                                </td>
                                <td class="py-3 px-4 min-w-44">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="font-bold text-slate-900">{{ $wo->completed_qty }} / {{ $wo->planned_qty }} {{ $wo->uom }}</span>
                                        <span class="font-mono font-bold text-amber-700">{{ $percent }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-amber-500 to-emerald-500 rounded-full" style="width: {{ min(100, $percent) }}%"></div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-medium">
                                    {{ $wo->target_date ? \Carbon\Carbon::parse($wo->target_date)->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($wo->status === 'in_progress')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">IN PROGRESS</span>
                                    @elseif($wo->status === 'completed')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">COMPLETED</span>
                                    @elseif($wo->status === 'released')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">RELEASED</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ strtoupper($wo->status) }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($wo->status !== 'completed')
                                        <form action="{{ route('work-orders.update-status', $wo->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 font-bold text-[11px] transition cursor-pointer">
                                                Mark Done
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Archived</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">No work orders recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create WO Modal -->
        <div id="newWoModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-4">
                    <h3 class="text-base font-black text-slate-900">Create Production Work Order</h3>
                    <button type="button" onclick="document.getElementById('newWoModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('work-orders.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Work Order Number</label>
                        <input type="text" name="wo_number" required value="WO-{{ date('Y') }}-{{ rand(1000, 9999) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-mono font-bold focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Target Product</label>
                            <select name="product_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->code }} - {{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Planned Quantity</label>
                            <input type="number" name="planned_qty" required value="100" min="1" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Work Center / Machine Line</label>
                            <input type="text" name="work_center" required value="CNC Milling Line A (Pune)" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Target Completion Date</label>
                            <input type="date" name="target_date" value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bill of Materials (Optional)</label>
                        <select name="bom_header_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="">-- None / Direct Entry --</option>
                            @foreach($boms as $b)
                                <option value="{{ $b->id }}">{{ $b->bom_number }} ({{ $b->version }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" onclick="document.getElementById('newWoModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                            Release Work Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
