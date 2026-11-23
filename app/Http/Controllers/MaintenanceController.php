<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $machines = DB::table('machines')
            ->where('company_id', $companyId)
            ->orderBy('id')
            ->get();

        $breakdowns = DB::table('machine_breakdowns')
            ->join('machines', 'machine_breakdowns.machine_id', '=', 'machines.id')
            ->where('machine_breakdowns.company_id', $companyId)
            ->select('machine_breakdowns.*', 'machines.name as machine_name', 'machines.code as machine_code')
            ->orderBy('machine_breakdowns.id', 'desc')
            ->get();

        return view('maintenance.index', compact('machines', 'breakdowns'));
    }

    public function storeBreakdown(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'incident_number' => 'required|string|unique:machine_breakdowns,incident_number',
            'issue_description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::table('machine_breakdowns')->insert([
            'company_id' => $companyId,
            'machine_id' => $request->machine_id,
            'incident_number' => $request->incident_number,
            'reported_by' => auth()->user()->name,
            'issue_description' => $request->issue_description,
            'severity' => $request->severity,
            'downtime_minutes' => 0,
            'status' => 'reported',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mark machine status as breakdown
        DB::table('machines')->where('id', $request->machine_id)->update(['status' => 'breakdown']);

        ActivityLog::log('breakdown', 'maintenance', "Reported machine breakdown incident: {$request->incident_number}");

        return back()->with('success', "Breakdown Incident {$request->incident_number} registered. Maintenance team notified.");
    }
}
