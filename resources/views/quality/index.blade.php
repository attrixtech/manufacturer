<x-app-layout>
    <x-slot:title>Quality & Inspections</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Quality Management & Inspections</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 font-mono">QA / QC Gate</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">IQC Inward, In-Process QC, Final QA Clearance, Non-Conformance Reports (NCR) & CAPA tracking</p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="document.getElementById('newNcrModal').classList.remove('hidden')" class="px-3.5 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Raise NCR</span>
                </button>
            </div>
        </div>

        <!-- 4 Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Inspections</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $inspections->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pending Clearance</div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $inspections->where('status', 'pending')->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Accepted Lots</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $inspections->where('status', 'accepted')->count() }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Open NCRs</div>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $ncrs->whereIn('status', ['open', 'under_review'])->count() }}</div>
            </div>
        </div>

        <!-- Inspection Queue Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    Inspection Queue & Quality Log
                </h3>
                <span class="text-[11px] font-medium text-slate-500">{{ $inspections->count() }} records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Inspection #</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Product / Item</th>
                            <th class="py-3 px-4">Batch Number</th>
                            <th class="py-3 px-4">Sample / Pass / Fail</th>
                            <th class="py-3 px-4">Reference Doc</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($inspections as $insp)
                            @php
                                $statusBadge = match($insp->status) {
                                    'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'quarantined' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                                $typeBadge = match($insp->type) {
                                    'iqc' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'ipqc' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'fqc' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $insp->inspection_number }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $typeBadge }} uppercase font-mono">
                                        {{ strtoupper($insp->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $insp->product_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $insp->product_code }}</div>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600">{{ $insp->batch_no ?? 'BATCH-STD' }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-mono text-slate-900 font-semibold">Total: {{ (int)$insp->sample_size }}</span>
                                    <span class="text-slate-400 mx-1">|</span>
                                    <span class="text-emerald-600 font-bold font-mono">P: {{ (int)$insp->accepted_qty }}</span>
                                    <span class="text-slate-400 mx-1">|</span>
                                    <span class="text-rose-600 font-bold font-mono">F: {{ (int)$insp->rejected_qty }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-mono">{{ $insp->reference_doc ?? 'REF-STD' }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }} capitalize font-mono">
                                        {{ $insp->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button onclick="openInspectionModal('{{ $insp->id }}', '{{ $insp->inspection_number }}', '{{ $insp->sample_size }}', '{{ $insp->status }}')" class="px-2.5 py-1 text-[11px] font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition cursor-pointer">
                                        Evaluate
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-400 text-xs">No inspections recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Non-Conformance Reports (NCR) -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Non-Conformance Reports (NCR) & CAPA
                </h3>
                <span class="text-[11px] font-medium text-slate-500">{{ $ncrs->count() }} active reports</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($ncrs as $ncr)
                    @php
                        $sevBadge = match($ncr->severity) {
                            'critical' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'major' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-blue-50 text-blue-700 border-blue-200',
                        };
                    @endphp
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono font-bold text-xs text-slate-900">{{ $ncr->ncr_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $sevBadge }} uppercase font-mono">
                                    {{ $ncr->severity }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase font-mono">
                                    {{ str_replace('_', ' ', $ncr->status) }}
                                </span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800">{{ $ncr->title }}</h4>
                            <p class="text-xs text-slate-500">{{ $ncr->description }}</p>
                            @if($ncr->disposition)
                                <div class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-[11px]">
                                    <span class="font-bold text-emerald-700">Disposition / CAPA:</span> {{ $ncr->disposition }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">No active Non-Conformance Reports.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Evaluate Inspection Modal -->
    <div id="evalModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Record Inspection Outcome</h3>
                <button onclick="document.getElementById('evalModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form id="evalForm" method="POST" action="" class="space-y-4">
                @csrf
                <div>
                    <div class="text-xs text-slate-500 mb-1">Inspection Number: <span id="modalInspNum" class="font-mono font-bold text-slate-900"></span></div>
                    <div class="text-xs text-slate-500">Sample Size: <span id="modalSampleSize" class="font-mono font-bold text-slate-900"></span></div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Quality Verdict</label>
                    <select name="status" id="evalStatus" class="w-full text-xs rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                        <option value="accepted">Accepted (Pass)</option>
                        <option value="rejected">Rejected (Fail)</option>
                        <option value="quarantined">Quarantined (Hold)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Accepted Qty</label>
                        <input type="number" step="1" min="0" name="accepted_qty" id="evalAcceptedQty" required class="w-full text-xs rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Rejected Qty</label>
                        <input type="number" step="1" min="0" name="rejected_qty" id="evalRejectedQty" required class="w-full text-xs rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Inspection Findings / Lab Notes</label>
                    <textarea name="findings" rows="3" placeholder="Dimensional variance, surface finish, hardness check..." class="w-full text-xs rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('evalModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition">Submit Verdict</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Raise NCR Modal -->
    <div id="newNcrModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Raise Non-Conformance Report (NCR)</h3>
                <button onclick="document.getElementById('newNcrModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('quality.ncr.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">NCR Number</label>
                        <input type="text" name="ncr_number" value="NCR-{{ date('Ymd') }}-{{ rand(100,999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Severity</label>
                        <select name="severity" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            <option value="minor">Minor</option>
                            <option value="major" selected>Major</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Title / Issue Summary</label>
                    <input type="text" name="title" placeholder="e.g. Surface crack detected during heat treatment batch #882" required class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Non-Conformance Description</label>
                    <textarea name="description" rows="3" required placeholder="Detailed root cause and description of defect..." class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Corrective / Preventive Action (CAPA)</label>
                    <textarea name="corrective_action" rows="2" placeholder="Action required to prevent reoccurrence..." class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newNcrModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition">Create NCR</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openInspectionModal(id, num, sampleSize, currentStatus) {
            document.getElementById('evalForm').action = '/quality/inspections/' + id + '/inspect';
            document.getElementById('modalInspNum').innerText = num;
            document.getElementById('modalSampleSize').innerText = sampleSize + ' pcs';
            document.getElementById('evalStatus').value = currentStatus === 'pending' ? 'accepted' : currentStatus;
            document.getElementById('evalAcceptedQty').value = sampleSize;
            document.getElementById('evalRejectedQty').value = 0;
            document.getElementById('evalModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
