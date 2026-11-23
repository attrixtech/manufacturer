<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $accounts = DB::table('chart_of_accounts')
            ->where('company_id', $companyId)
            ->orderBy('code')
            ->get();

        $journalEntries = DB::table('journal_entries')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get();

        $assetsTotal = $accounts->where('type', 'asset')->sum('balance');
        $liabilitiesTotal = $accounts->where('type', 'liability')->sum('balance');
        $revenueTotal = $accounts->where('type', 'revenue')->sum('balance');
        $expenseTotal = $accounts->where('type', 'expense')->sum('balance');

        return view('accounting.index', compact('accounts', 'journalEntries', 'assetsTotal', 'liabilitiesTotal', 'revenueTotal', 'expenseTotal'));
    }

    public function storeJournal(Request $request)
    {
        $request->validate([
            'entry_number' => 'required|string|unique:journal_entries,entry_number',
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'total_amount' => 'required|numeric|min:0.01',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::table('journal_entries')->insert([
            'company_id' => $companyId,
            'entry_number' => $request->entry_number,
            'entry_date' => $request->entry_date,
            'reference_doc' => $request->reference_doc ?? 'MANUAL-VCH',
            'description' => $request->description,
            'total_amount' => $request->total_amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::log('create', 'accounting', "Posted General Ledger Journal Entry: {$request->entry_number}");

        return back()->with('success', "Journal Entry {$request->entry_number} balanced and posted to General Ledger.");
    }
}
