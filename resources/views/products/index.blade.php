<x-app-layout>
    <x-slot:title>Products & Item Master</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Products & Item Master</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-200 font-mono">Catalog Master</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage finished goods, semi-finished assemblies, raw materials, tooling, and consumables</p>
            </div>

            <button onclick="document.getElementById('newProductModal').classList.remove('hidden')" class="px-3.5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Create Finished Product / Item</span>
            </button>
        </div>

        <!-- 4 Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Finished Goods (FG)</div>
                <div class="text-2xl font-black text-cyan-700 mt-1">{{ $stats['finished_goods'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Semi-Finished Blanks</div>
                <div class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['semi_finished'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Raw Materials (RM)</div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['raw_materials'] }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tools & Consumables</div>
                <div class="text-2xl font-black text-slate-700 mt-1">{{ $stats['consumables'] }}</div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto text-xs font-bold">
                <a href="{{ route('products.index', ['type' => 'all']) }}" class="px-3 py-1.5 rounded-xl border transition {{ $type === 'all' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : 'text-slate-600 hover:bg-slate-50 border-slate-200' }}">
                    All Items ({{ $stats['total'] }})
                </a>
                <a href="{{ route('products.index', ['type' => 'finished_good']) }}" class="px-3 py-1.5 rounded-xl border transition {{ $type === 'finished_good' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : 'text-slate-600 hover:bg-slate-50 border-slate-200' }}">
                    Finished Goods ({{ $stats['finished_goods'] }})
                </a>
                <a href="{{ route('products.index', ['type' => 'semi_finished']) }}" class="px-3 py-1.5 rounded-xl border transition {{ $type === 'semi_finished' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'text-slate-600 hover:bg-slate-50 border-slate-200' }}">
                    Semi-Finished ({{ $stats['semi_finished'] }})
                </a>
                <a href="{{ route('products.index', ['type' => 'raw_material']) }}" class="px-3 py-1.5 rounded-xl border transition {{ $type === 'raw_material' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'text-slate-600 hover:bg-slate-50 border-slate-200' }}">
                    Raw Materials ({{ $stats['raw_materials'] }})
                </a>
                <a href="{{ route('products.index', ['type' => 'consumable']) }}" class="px-3 py-1.5 rounded-xl border transition {{ $type === 'consumable' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-slate-600 hover:bg-slate-50 border-slate-200' }}">
                    Consumables ({{ $stats['consumables'] }})
                </a>
            </div>

            <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search SKU, name, category..." class="text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 w-48 sm:w-60">
                <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                    Search
                </button>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Item Code / SKU</th>
                            <th class="py-3 px-4">Product Name & Category</th>
                            <th class="py-3 px-4">Item Classification</th>
                            <th class="py-3 px-4">Unit (UOM)</th>
                            <th class="py-3 px-4 text-right">Standard Cost (₹)</th>
                            <th class="py-3 px-4 text-right">Current Stock</th>
                            <th class="py-3 px-4 text-right">Reorder Level</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($products as $prod)
                            @php
                                $typeBadge = match($prod->type) {
                                    'finished_good' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                    'semi_finished' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'raw_material' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'consumable' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $prod->code }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $prod->name }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $prod->category }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $typeBadge }} uppercase font-mono">
                                        {{ str_replace('_', ' ', $prod->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono font-semibold text-slate-600">{{ $prod->uom }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">₹{{ number_format($prod->standard_cost, 2) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold {{ (float)$prod->current_stock <= (float)$prod->reorder_level ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ (float)$prod->current_stock }} {{ $prod->uom }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-500">{{ (float)$prod->reorder_level }} {{ $prod->uom }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-400 text-xs">No products found matching the criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-3 border-t border-slate-200 bg-slate-50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Product Modal -->
    <div id="newProductModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                    <h3 class="text-sm font-black text-slate-900">Create New Product / Finished Good</h3>
                </div>
                <button onclick="document.getElementById('newProductModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Item Classification</label>
                        <select name="type" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-bold">
                            <option value="finished_good" selected>Finished Good (FG)</option>
                            <option value="semi_finished">Semi-Finished Assembly (SF)</option>
                            <option value="raw_material">Raw Material (RM)</option>
                            <option value="consumable">Consumable / Tooling</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Item Code / SKU</label>
                        <input type="text" name="code" placeholder="e.g. PART-VALVE-50" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono uppercase font-bold">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Product Name / Description</label>
                    <input type="text" name="name" placeholder="e.g. 50mm High Pressure Butterfly Valve" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Category</label>
                        <input type="text" name="category" placeholder="e.g. Valves & Actuators" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Unit of Measure (UOM)</label>
                        <select name="uom" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                            <option value="Pcs" selected>Pcs (Pieces)</option>
                            <option value="Kg">Kg (Kilograms)</option>
                            <option value="Meters">Meters</option>
                            <option value="Liters">Liters</option>
                            <option value="Sets">Sets</option>
                            <option value="Boxes">Boxes</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Standard Cost (₹)</label>
                        <input type="number" step="0.01" min="0" name="standard_cost" value="1250.00" required class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono font-bold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Initial Stock</label>
                        <input type="number" step="0.01" min="0" name="current_stock" value="0.00" class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Reorder Level</label>
                        <input type="number" step="0.01" min="0" name="reorder_level" value="20.00" class="w-full text-xs rounded-xl border-slate-200 focus:border-cyan-500 focus:ring-cyan-500 font-mono">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newProductModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition">Save Product Master</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
