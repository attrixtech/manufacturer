<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['branches', 'departments'])->withCount(['branches', 'users'])->latest()->paginate(10);
        $allBranches = Branch::with('company')->latest()->get();
        $allDepartments = Department::with(['company', 'branch'])->latest()->get();

        return view('companies.index', compact('companies', 'allBranches', 'allDepartments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'code' => 'required|string|max:20|unique:companies,code',
            'gstin' => 'nullable|string|max:15',
            'pan' => 'nullable|string|max:10',
            'cin' => 'nullable|string|max:21',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:3',
            'currency_symbol' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $company = Company::create(array_merge($validated, [
            'currency' => $validated['currency'] ?? 'INR',
            'currency_symbol' => $validated['currency_symbol'] ?? '₹',
            'country' => $validated['country'] ?? 'India',
            'is_active' => true,
        ]));

        ActivityLog::log('create', 'core', "Created company entity: {$company->name} ({$company->code})");

        return back()->with('success', "Company entity {$company->name} successfully registered.");
    }

    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'type' => 'required|in:factory,warehouse,head_office,branch,subcontractor_unit',
            'gstin' => 'nullable|string|max:15',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

        $branch = Branch::create(array_merge($validated, [
            'country' => 'India',
            'is_active' => true,
        ]));

        ActivityLog::log('create', 'core', "Added manufacturing plant/branch: {$branch->name} ({$branch->code})");

        return back()->with('success', "Manufacturing Plant {$branch->name} added successfully.");
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        $dept = Department::create(array_merge($validated, [
            'is_active' => true,
        ]));

        ActivityLog::log('create', 'core', "Added department: {$dept->name} ({$dept->code})");

        return back()->with('success', "Department {$dept->name} added successfully.");
    }

    public function switchCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);
        session(['current_company_id' => $company->id]);

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
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        $company->update($validated);

        ActivityLog::log('update', 'core', "Updated company details for {$company->name}");

        return redirect()->route('companies.index')->with('success', "Company {$company->name} updated successfully.");
    }
}
