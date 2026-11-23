<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualityController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $inspections = DB::table('quality_inspections')
            ->join('products', 'quality_inspections.product_id', '=', 'products.id')
            ->where('quality_inspections.company_id', $companyId)
            ->select('quality_inspections.*', 'products.name as product_name', 'products.code as product_code')
            ->orderBy('quality_inspections.id', 'desc')
            ->get();

        $ncrs = DB::table('non_conformance_reports')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get();

        $products = DB::table('products')->where('company_id', $companyId)->get();

        return view('quality.index', compact('inspections', 'ncrs', 'products'));
    }

    public function inspect(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected,quarantined',
            'accepted_qty' => 'required|numeric',
            'rejected_qty' => 'required|numeric',
            'findings' => 'nullable|string',
        ]);

        DB::table('quality_inspections')->where('id', $id)->update([
            'status' => $request->status,
            'accepted_qty' => $request->accepted_qty,
            'rejected_qty' => $request->rejected_qty,
            'findings' => $request->findings,
            'updated_at' => now(),
        ]);

        ActivityLog::log('inspect', 'quality', "Completed Inspection #{$id} with outcome: {$request->status}");

        return back()->with('success', "Quality inspection record certified as {$request->status}.");
    }

    public function storeNcr(Request $request)
    {
        $request->validate([
            'ncr_number' => 'required|string|unique:non_conformance_reports,ncr_number',
            'title' => 'required|string',
            'description' => 'required|string',
            'severity' => 'required|in:minor,major,critical',
            'disposition' => 'nullable|string',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::table('non_conformance_reports')->insert([
            'company_id' => $companyId,
            'ncr_number' => $request->ncr_number,
            'title' => $request->title,
            'description' => $request->description,
            'severity' => $request->severity,
            'status' => 'open',
            'disposition' => $request->disposition ?? 'Under Investigation',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::log('create', 'quality', "Logged Non-Conformance Report: {$request->ncr_number}");

        return back()->with('success', "Non-Conformance Report {$request->ncr_number} logged.");
    }
}
