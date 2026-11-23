<x-app-layout>
    <x-slot:title>Bill of Materials (BOM) & Routings</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Bill of Materials (BOM) & Routings</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-200 font-mono">Phase 5 Live</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Multi-level BOM engineering, version control, standard cost rollups, and component scrap factors</p>
            </div>

            <button onclick="document.getElementById('newBomModal').classList.remove('hidden')" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create New BOM</span>
            </button>
        </div>

        <!-- BOM Cards List -->
        <div class="grid grid-cols-1 gap-6">
            @forelse($boms as $bom)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-amber-700 px-2 py-0.5 rounded bg-amber-50 border border-amber-200">{{ $bom->bom_number }}</span>
                                <span class="text-xs font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">{{ $bom->version }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">{{ $bom->status }}</span>
                            </div>
                            <h2 class="text-base font-bold text-slate-900 mt-2">{{ $bom->product_name }} (<span class="font-mono text-sm text-slate-500">{{ $bom->product_code }}</span>)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $bom->notes }}</p>
                        </div>

                        <div class="text-right sm:border-l sm:border-slate-200 sm:pl-6">
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Unit Standard Cost</div>
                            <div class="text-xl font-black text-slate-900 mt-0.5">₹{{ number_format($bom->standard_cost, 2) }}</div>
                            <div class="text-[11px] text-slate-500 font-medium">Output: 1 {{ $bom->uom }}</div>
                        </div>
                    </div>

                    <!-- Component Items Table -->
                    <div class="mt-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">BOM Component Explosion</div>
                        <div class="overflow-x-auto border border-slate-200 rounded-xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">Part Code</th>
                                        <th class="py-2.5 px-3">Component Description</th>
                                        <th class="py-2.5 px-3">Required Qty</th>
                                        <th class="py-2.5 px-3">Scrap %</th>
                                        <th class="py-2.5 px-3 text-right">Standard Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($bom->components as $c)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-3 font-mono font-bold text-amber-700">{{ $c->component_code }}</td>
                                            <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $c->component_name }}</td>
                                            <td class="py-2.5 px-3 font-bold text-slate-900">{{ $c->quantity }} {{ $c->uom }}</td>
                                            <td class="py-2.5 px-3 font-mono text-slate-500">{{ $c->scrap_percentage }}%</td>
                                            <td class="py-2.5 px-3 font-mono font-bold text-slate-900 text-right">₹{{ number_format($c->unit_cost, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-slate-400">No components defined in this BOM version.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white border border-slate-200 rounded-2xl text-slate-500 text-xs">
                    No Bill of Materials registered yet.
                </div>
            @endforelse
        </div>

        <!-- Create BOM Modal -->
        <div id="newBomModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-4">
                    <h3 class="text-base font-black text-slate-900">Register New Bill of Materials</h3>
                    <button type="button" onclick="document.getElementById('newBomModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('bom.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">BOM Number</label>
                        <input type="text" name="bom_number" required value="BOM-{{ date('Y') }}-{{ rand(100, 999) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-mono font-bold focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Parent Finished Good</label>
                            <select name="product_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->code }} - {{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Version / Revision</label>
                            <input type="text" name="version" required value="REV-01" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-mono focus:bg-white focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Standard Cost Rollup (₹)</label>
                        <input type="number" step="0.01" name="standard_cost" required value="4850.00" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Engineering Notes</label>
                        <textarea name="notes" rows="2" placeholder="Material specifications, drawing number, heat treatment specs..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" onclick="document.getElementById('newBomModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                            Save & Approve BOM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
