<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['branches', 'users'])->latest()->paginate(10);
        return view('companies.index', compact('companies'));
    }

    public function switchCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);
        session(['current_company_id' => $company->id]);

        // Automatically set first branch of that company
        $firstBranch = $company->branches()->first();
        session(['current_branch_id' => $firstBranch?->id]);

        ActivityLog::log('switch_company', 'core', "Switched workspace to company: {$company->name}");

        return back()->with('success', "Active company switched to {$company->name}");
    }

    public function switchBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $branch = Branch::findOrFail($request->branch_id);
        session(['current_branch_id' => $branch->id]);

        ActivityLog::log('switch_branch', 'core', "Switched workspace to plant/branch: {$branch->name}");

        return back()->with('success', "Active plant/branch switched to {$branch->name}");
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:15',
            'pan' => 'nullable|string|max:10',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $company->update($validated);
        ActivityLog::log('update', 'core', "Updated company profile: {$company->name}", $company);

        return redirect()->route('companies.index')->with('success', 'Company details updated successfully.');
    }
}
