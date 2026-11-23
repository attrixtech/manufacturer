<x-app-layout>
    <x-slot:title>General Ledger & Accounting</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>General Ledger & GST Accounting</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">FIN Ledger</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Double-entry manufacturing accounting, Chart of Accounts, WIP cost absorption, and journal logs</p>
            </div>

            <button onclick="document.getElementById('newJournalModal').classList.remove('hidden')" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Post Journal Entry</span>
            </button>
        </div>

        <!-- 4 Financial Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Assets</div>
                <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">₹{{ number_format($assetsTotal, 2) }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Liabilities</div>
                <div class="text-2xl font-black text-rose-600 mt-1 font-mono">₹{{ number_format($liabilitiesTotal, 2) }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Operating Revenue</div>
                <div class="text-2xl font-black text-blue-600 mt-1 font-mono">₹{{ number_format($revenueTotal, 2) }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Operating Expenses</div>
                <div class="text-2xl font-black text-amber-600 mt-1 font-mono">₹{{ number_format($expenseTotal, 2) }}</div>
            </div>
        </div>

        <!-- Chart of Accounts & Journal Entries Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart of Accounts -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Chart of Accounts (COA)
                    </h3>
                    <span class="text-[11px] font-medium text-slate-500">{{ $accounts->count() }} accounts</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-4">Code</th>
                                <th class="py-2.5 px-4">Account Name</th>
                                <th class="py-2.5 px-4">Category</th>
                                <th class="py-2.5 px-4 text-right">Balance (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($accounts as $acc)
                                @php
                                    $typeBadge = match($acc->type) {
                                        'asset' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'liability' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'revenue' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'expense' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-2.5 px-4 font-mono font-bold text-slate-900">{{ $acc->code }}</td>
                                    <td class="py-2.5 px-4 font-medium text-slate-800">{{ $acc->name }}</td>
                                    <td class="py-2.5 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $typeBadge }} uppercase font-mono">
                                            {{ $acc->type }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">
                                        ₹{{ number_format($acc->balance, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-slate-400 text-xs">No accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Journal Entries Log -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Recent General Journal Entries
                    </h3>
                    <span class="text-[11px] font-medium text-slate-500">{{ $journalEntries->count() }} vouchers</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($journalEntries as $entry)
                        <div class="p-4 hover:bg-slate-50/50 transition">
                            <div class="flex items-center justify-between gap-3 mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-xs text-slate-900">{{ $entry->entry_number }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase font-mono">
                                        POSTED
                                    </span>
                                </div>
                                <span class="font-mono font-bold text-xs text-slate-900">₹{{ number_format($entry->total_amount, 2) }}</span>
                            </div>
                            <p class="text-xs text-slate-600 mb-1">{{ $entry->description }}</p>
                            <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono">
                                <span>Ref: {{ $entry->reference_doc }}</span>
                                <span>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">No journal entries logged yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Post Journal Modal -->
    <div id="newJournalModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-slate-200 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900">Record Manual Journal Voucher</h3>
                <button onclick="document.getElementById('newJournalModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('accounting.journal.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Entry Number</label>
                        <input type="text" name="entry_number" value="JV-{{ date('Y') }}-{{ rand(1000,9999) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-mono">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Voucher Date</label>
                        <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-mono">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Reference Document</label>
                    <input type="text" name="reference_doc" placeholder="e.g. INV-2026-4091 or BANK-REC" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-mono">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Description / Narration</label>
                    <textarea name="description" rows="2" required placeholder="Manufacturing cost allocation, WIP absorption adjustment..." class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Total Voucher Amount (₹)</label>
                    <input type="number" step="0.01" min="0.01" name="total_amount" value="15000.00" required class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-mono">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newJournalModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition">Post Journal</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
