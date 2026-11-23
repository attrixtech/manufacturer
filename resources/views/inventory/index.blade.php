<x-app-layout>
    <x-slot:title>Perpetual Inventory & Stock Ledger</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Perpetual Inventory & Stock Ledger</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">Phase 3 Live</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Real-time stock valuation (FIFO), bin tracking, immutable stock movements, and batch tracking</p>
            </div>

            <button onclick="document.getElementById('transferModal').classList.remove('hidden')" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span>Stock Transfer</span>
            </button>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Perpetual Stock Valuation</div>
                <div class="text-2xl font-black text-slate-900 mt-1">₹{{ number_format($totalValuation, 2) }}</div>
                <div class="text-[11px] text-emerald-700 font-semibold mt-1">FIFO Inventory Layer Active</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active Warehouses</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $warehouses->count() }} Stores</div>
                <div class="text-[11px] text-slate-500 font-medium mt-1">Raw, WIP, Finished & Quarantine</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Low Stock Warnings</div>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $lowStockCount }} Items</div>
                <div class="text-[11px] text-rose-700 font-semibold mt-1">Below safety reorder threshold</div>
            </div>
        </div>

        <!-- Stock Ledger Movements Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Perpetual Stock Ledger Moves</h2>
                    <p class="text-[11px] text-slate-500">Atomic, traceable inventory postings linked to source documents</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600">IMMUTABLE</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Date / Time</th>
                            <th class="py-3 px-4">Product / Item</th>
                            <th class="py-3 px-4">Warehouse</th>
                            <th class="py-3 px-4">Batch / Lot #</th>
                            <th class="py-3 px-4">Qty Change</th>
                            <th class="py-3 px-4">Balance After</th>
                            <th class="py-3 px-4">Voucher / Source</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($stockLedger as $move)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                    {{ \Carbon\Carbon::parse($move->created_at)->format('d M Y H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $move->product_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $move->product_code }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-700">
                                    {{ $move->warehouse_name }}
                                </td>
                                <td class="py-3 px-4 font-mono text-amber-700 font-semibold">
                                    {{ $move->batch_no ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 font-bold font-mono {{ $move->quantity_change > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $move->quantity_change > 0 ? '+' : '' }}{{ $move->quantity_change }} {{ $move->uom }}
                                </td>
                                <td class="py-3 px-4 font-bold font-mono text-slate-900">
                                    {{ $move->balance_after }} {{ $move->uom }}
                                </td>
                                <td class="py-3 px-4 font-mono text-[11px]">
                                    <span class="font-semibold text-slate-900">{{ $move->source_doc }}</span>
                                    <div class="text-[10px] text-slate-400 font-sans">{{ $move->voucher_type }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">No stock ledger entries recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transfer Modal -->
        <div id="transferModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-4">
                    <h3 class="text-base font-black text-slate-900">Execute Inter-Warehouse Stock Transfer</h3>
                    <button type="button" onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('inventory.transfer') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Select Item to Transfer</label>
                        <select name="product_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->code }} - {{ $prod->name }} (Stock: {{ $prod->current_stock }} {{ $prod->uom }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">From Warehouse</label>
                            <select name="from_warehouse_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">To Warehouse</label>
                            <select name="to_warehouse_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Quantity to Move</label>
                        <input type="number" name="quantity" required value="10" min="1" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" onclick="document.getElementById('transferModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                            Post Stock Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
