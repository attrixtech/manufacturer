<x-app-layout>
    <x-slot:title>Companies & Manufacturing Plants</x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Companies & Manufacturing Plants</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage multi-company entities, manufacturing plants, factories, and warehouses</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @foreach($companies as $comp)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-bold text-lg shrink-0">
                                {{ substr($comp->code, 0, 4) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-base font-bold text-slate-900">{{ $comp->name }}</h2>
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">Active Entity</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">Legal Name: <span class="text-slate-700 font-medium">{{ $comp->legal_name }}</span></div>
                                <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-slate-600">
                                    <span>GSTIN: <strong class="text-slate-900 font-mono">{{ $comp->gstin }}</strong></span>
                                    <span>PAN: <strong class="text-slate-900 font-mono">{{ $comp->pan }}</strong></span>
                                    <span>Currency: <strong class="text-amber-700 font-mono font-bold">{{ $comp->currency }} ({{ $comp->currency_symbol }})</strong></span>
                                    <span>Plants/Branches: <strong class="text-slate-900">{{ $comp->branches_count }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if(session('current_company_id') == $comp->id)
                                <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Currently Active
                                </span>
                            @else
                                <form action="{{ route('companies.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="company_id" value="{{ $comp->id }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-slate-800 transition shadow-2xs">
                                        Switch to this Company
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Branches Table -->
                    <div class="mt-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Manufacturing Units & Branches</div>
                        <div class="overflow-x-auto border border-slate-200 rounded-xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">Unit Name</th>
                                        <th class="py-2.5 px-3">Code</th>
                                        <th class="py-2.5 px-3">Type</th>
                                        <th class="py-2.5 px-3">GSTIN</th>
                                        <th class="py-2.5 px-3">Location</th>
                                        <th class="py-2.5 px-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @foreach($comp->branches as $branch)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-3 font-bold text-slate-900 flex items-center gap-2">
                                                <span>{{ $branch->name }}</span>
                                                @if($branch->is_head_office)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] bg-blue-50 text-blue-700 border border-blue-200 font-mono font-bold">HQ</span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-slate-500 font-semibold">{{ $branch->code }}</td>
                                            <td class="py-2.5 px-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $branch->type === 'factory' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700' }}">
                                                    {{ strtoupper($branch->type) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-600">{{ $branch->gstin ?? 'N/A' }}</td>
                                            <td class="py-2.5 px-3 text-slate-600">{{ $branch->city }}, {{ $branch->state }}</td>
                                            <td class="py-2.5 px-3 text-right">
                                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-bold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
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
