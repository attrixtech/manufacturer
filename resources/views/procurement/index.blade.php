<x-app-layout>
    <x-slot:title>Procurement & Purchase Orders</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Procurement & Purchase Orders</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 font-mono">PUR Queue</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Supplier purchase contracts, PO dispatch, line item pricing, and 3-way matching</p>
            </div>

            <button onclick="document.getElementById('newPoModal').classList.remove('hidden')" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Purchase Order</span>
            </button>
        </div>

        <!-- 4 Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Orders</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $orders->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active Sent POs</div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $orders->where('status', 'sent')->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Received / Fulfilled</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $orders->where('status', 'received')->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Procurement Value</div>
                <div class="text-2xl font-black text-slate-900 mt-1 font-mono">₹{{ number_format($orders->sum('total_amount'), 2) }}</div>
            </div>
        </div>

        <!-- Purchase Orders Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Purchase Order Registry
                </h3>
                <span class="text-[11px] font-medium text-slate-500">{{ $orders->count() }} purchase orders</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($orders as $po)
                    @php
                        $statusBadge = match($po->status) {
                            'sent' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'received' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'partially_received' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <div class="p-5 hover:bg-slate-50/50 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono font-bold text-sm text-slate-900">{{ $po->po_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }} uppercase font-mono">
                                    {{ str_replace('_', ' ', $po->status) }}
                                </span>
                                <span class="text-xs font-medium text-slate-500">Ordered: {{ \Carbon\Carbon::parse($po->order_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-500 font-medium">Order Total:</span>
                                <span class="font-mono font-black text-sm text-slate-900">₹{{ number_format($po->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <div class="text-xs font-semibold text-slate-800 mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Supplier: <strong class="text-slate-900">{{ $po->supplier_name }}</strong></span>
                        </div>

                        <!-- PO Line Items Table -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-[11px]">
                                <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2 px-3">Item / SKU</th>
                                        <th class="py-2 px-3 text-right">Quantity</th>
                                        <th class="py-2 px-3 text-right">Unit Price</th>
                                        <th class="py-2 px-3 text-right">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($po->items as $item)
                                        <tr>
                                            <td class="py-2 px-3">
                                                <div class="font-bold text-slate-800">{{ $item->product_name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->product_code }}</div>
                                            </td>
                                            <td class="py-2 px-3 text-right font-mono font-semibold text-slate-700">
                                                {{ (float)$item->quantity }} {{ $item->uom }}
                                            </td>
                                            <td class="py-2 px-3 text-right font-mono text-slate-600">
                                                ₹{{ number_format($item->unit_price, 2) }}
                                            </td>
                                            <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                                                ₹{{ number_format($item->quantity * $item->unit_price, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">No purchase orders created yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- New PO Modal -->
    <div id="newPoModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Create New Purchase Order</h3>
                <button onclick="document.getElementById('newPoModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('procurement.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">PO Number</label>
                        <input type="text" name="po_number" value="PO-{{ date('Y') }}-{{ rand(1000,9999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Order Date</label>
                        <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Supplier Name / Vendor</label>
                    <input type="text" name="supplier_name" placeholder="e.g. Bharat Steel Suppliers Pvt. Ltd." required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Select Product / Material</label>
                    <select name="product_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }} ({{ $prod->uom }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Order Quantity</label>
                        <input type="number" step="0.01" min="1" name="quantity" value="100" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Unit Price (₹)</label>
                        <input type="number" step="0.01" min="0.01" name="unit_price" value="85.00" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-mono">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newPoModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition">Issue Purchase Order</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
