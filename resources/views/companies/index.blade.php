<x-app-layout>
    <x-slot:title>Companies & Manufacturing Plants</x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-800">
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight">Companies & Manufacturing Plants</h1>
                <p class="text-xs text-slate-400 mt-0.5">Manage multi-company entities, manufacturing plants, factories, and warehouses</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @foreach($companies as $comp)
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-800">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-lg shrink-0">
                                {{ substr($comp->code, 0, 4) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-base font-bold text-white">{{ $comp->name }}</h2>
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium">Active Entity</span>
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">Legal Name: <span class="text-slate-300">{{ $comp->legal_name }}</span></div>
                                <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-slate-400">
                                    <span>GSTIN: <strong class="text-slate-200 font-mono">{{ $comp->gstin }}</strong></span>
                                    <span>PAN: <strong class="text-slate-200 font-mono">{{ $comp->pan }}</strong></span>
                                    <span>Currency: <strong class="text-amber-400 font-mono">{{ $comp->currency }} ({{ $comp->currency_symbol }})</strong></span>
                                    <span>Plants/Branches: <strong class="text-slate-200">{{ $comp->branches_count }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if(session('current_company_id') == $comp->id)
                                <span class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Currently Active
                                </span>
                            @else
                                <form action="{{ route('companies.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="company_id" value="{{ $comp->id }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white transition">
                                        Switch to this Company
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Branches Table -->
                    <div class="mt-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Manufacturing Units & Branches</div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] tracking-wider border-y border-slate-800">
                                    <tr>
                                        <th class="py-2.5 px-3">Unit Name</th>
                                        <th class="py-2.5 px-3">Code</th>
                                        <th class="py-2.5 px-3">Type</th>
                                        <th class="py-2.5 px-3">GSTIN</th>
                                        <th class="py-2.5 px-3">Location</th>
                                        <th class="py-2.5 px-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                    @foreach($comp->branches as $branch)
                                        <tr class="hover:bg-slate-800/30 transition">
                                            <td class="py-2.5 px-3 font-semibold text-white flex items-center gap-2">
                                                <span>{{ $branch->name }}</span>
                                                @if($branch->is_head_office)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] bg-blue-500/20 text-blue-400 font-mono font-medium">HQ</span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-slate-400">{{ $branch->code }}</td>
                                            <td class="py-2.5 px-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $branch->type === 'factory' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-800 text-slate-300' }}">
                                                    {{ strtoupper($branch->type) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">{{ $branch->gstin ?? 'N/A' }}</td>
                                            <td class="py-2.5 px-3 text-slate-400">{{ $branch->city }}, {{ $branch->state }}</td>
                                            <td class="py-2.5 px-3 text-right">
                                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
