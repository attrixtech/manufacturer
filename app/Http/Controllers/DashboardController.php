<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currentCompanyId = session('current_company_id') ?? auth()->user()->company_id;
        $company = Company::find($currentCompanyId) ?? Company::first();

        $stats = [
            'total_plants' => Branch::where('type', 'factory')->count(),
            'total_warehouses' => Branch::where('type', 'warehouse')->count(),
            'total_departments' => Department::count(),
            'total_active_staff' => User::where('status', 'active')->count(),
            
            // Production & Shop Floor Metrics (Simulated real-time operations dashboard)
            'active_work_orders' => 14,
            'work_orders_delayed' => 2,
            'oee_percentage' => 87.4,
            'quality_pass_rate' => 99.2,
            'inventory_valuation' => 48250000.00, // ₹4.82 Cr
            'mtd_production_tons' => 1240.5,
        ];

        // Production line statuses
        $productionLines = [
            [
                'name' => 'CNC Milling Line A (Pune)',
                'current_wo' => 'WO-2026-0891 (Heavy Flange)',
                'operator' => 'Dinesh Pawar',
                'progress' => 78,
                'status' => 'running',
                'speed_rpm' => '98% efficiency',
            ],
            [
                'name' => 'Robotic Arc Welding Cell 2',
                'current_wo' => 'WO-2026-0894 (Chassis Frame)',
                'operator' => 'Sanjay Verma',
                'progress' => 45,
                'status' => 'running',
                'speed_rpm' => '94% efficiency',
            ],
            [
                'name' => 'VMC Precision Line 4 (Bengaluru)',
                'current_wo' => 'WO-2026-0902 (Shaft Pinion)',
                'operator' => 'Karthik Rao',
                'progress' => 92,
                'status' => 'running',
                'speed_rpm' => '96% efficiency',
            ],
            [
                'name' => 'Heat Treatment Furnace 1',
                'current_wo' => 'WO-2026-0887 (Hardening Batch #4)',
                'operator' => 'Gopal Shinde',
                'progress' => 100,
                'status' => 'maintenance',
                'speed_rpm' => 'Cooling Cycle',
            ],
        ];

        // Recent audit/activity trail
        $recentActivities = ActivityLog::with(['user', 'branch'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.index', compact('company', 'stats', 'productionLines', 'recentActivities'));
    }
}
