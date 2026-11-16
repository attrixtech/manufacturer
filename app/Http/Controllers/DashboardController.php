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
        $user = auth()->user();
        $currentCompanyId = session('current_company_id') ?? $user->company_id;
        $company = Company::find($currentCompanyId) ?? Company::first();

        // Determine user's primary role
        $roleName = $user->roles->first()?->name ?? 'Plant Manager';

        // Base organizational counters
        $baseStats = [
            'total_plants' => Branch::where('type', 'factory')->count(),
            'total_warehouses' => Branch::where('type', 'warehouse')->count(),
            'total_departments' => Department::count(),
            'total_active_staff' => User::where('status', 'active')->count(),
        ];

        // Role-tailored data package
        $roleData = $this->getRoleSpecificData($roleName);

        // Recent audit/activity trail filtered by company
        $recentActivities = ActivityLog::with(['user', 'branch'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact('company', 'user', 'roleName', 'baseStats', 'roleData', 'recentActivities'));
    }

    private function getRoleSpecificData(string $role): array
    {
        switch ($role) {
            case 'Quality Inspector':
                return [
                    'view_type' => 'quality',
                    'title' => 'QA / QC Quality Command Center',
                    'subtitle' => 'Incoming, In-Process, and Finished Goods Inspection Control',
                    'cards' => [
                        ['label' => 'Pending IQC Inspections', 'value' => '6 Batches', 'sub' => 'Materials in Quarantine', 'color' => 'amber'],
                        ['label' => 'In-Process QC Checks', 'value' => '8 Active', 'sub' => 'Shop floor line checks', 'color' => 'blue'],
                        ['label' => 'FQC Final Clearances', 'value' => '3 Lots', 'sub' => 'Awaiting dispatch approval', 'color' => 'purple'],
                        ['label' => 'Pass Rate (Monthly)', 'value' => '99.2%', 'sub' => '0.8% Non-conformance', 'color' => 'emerald'],
                    ],
                    'pending_inspections' => [
                        [
                            'lot_no' => 'LOT-SS316-092',
                            'item' => 'Stainless Steel Rods 50mm',
                            'type' => 'Incoming (IQC)',
                            'supplier' => 'Jindal Stainless Ltd.',
                            'warehouse' => 'Quarantine Bay 02',
                            'qty' => '1,500 Kg',
                            'status' => 'Pending Lab Assay',
                        ],
                        [
                            'lot_no' => 'LOT-FLG-400-A',
                            'item' => 'Forged Flange Blanks',
                            'type' => 'Incoming (IQC)',
                            'supplier' => 'Bharat Forge Ltd.',
                            'warehouse' => 'Raw Material Inward',
                            'qty' => '250 Pcs',
                            'status' => 'Dimensional Check Due',
                        ],
                        [
                            'lot_no' => 'WO-2026-0891-OP2',
                            'item' => 'Heavy Flange Machining',
                            'type' => 'In-Process (IPQC)',
                            'supplier' => 'CNC Milling Line A',
                            'warehouse' => 'WIP Line 1',
                            'qty' => '80 Pcs',
                            'status' => 'Tolerance & Surface Finish',
                        ],
                        [
                            'lot_no' => 'FG-PINION-0887',
                            'item' => 'Hardened Shaft Pinions',
                            'type' => 'Final QC (FQC)',
                            'supplier' => 'Heat Treatment Cell',
                            'warehouse' => 'Pre-Dispatch Staging',
                            'qty' => '120 Pcs',
                            'status' => 'Hardness Rockwell C Test',
                        ],
                    ],
                    'open_ncrs' => [
                        ['code' => 'NCR-2026-014', 'issue' => 'Hardness 52 HRC below specification 58-62 HRC', 'disposition' => 'Under Metallurgical Review', 'severity' => 'Major'],
                        ['code' => 'NCR-2026-015', 'issue' => 'Visual burrs on internal thread pitch', 'disposition' => 'Sent for De-burring Rework', 'severity' => 'Minor'],
                    ]
                ];

            case 'Warehouse & Inventory Manager':
                return [
                    'view_type' => 'inventory',
                    'title' => 'Warehouse & Inventory Command Center',
                    'subtitle' => 'Perpetual Stock Ledger, Bin Tracking & Material Movement',
                    'cards' => [
                        ['label' => 'Total Stock Valuation', 'value' => '₹4.82 Cr', 'sub' => 'Perpetual FIFO Ledger', 'color' => 'amber'],
                        ['label' => 'Low Stock Reorder Alerts', 'value' => '5 Items', 'sub' => 'Below safety buffer', 'color' => 'rose'],
                        ['label' => 'Pending Inward GRNs', 'value' => '4 Shipments', 'sub' => 'Unloading at dock', 'color' => 'blue'],
                        ['label' => 'Pending Dispatch Challans', 'value' => '7 Orders', 'sub' => 'Ready for truck loading', 'color' => 'emerald'],
                    ],
                    'reorder_alerts' => [
                        ['code' => 'RM-STEEL-4140', 'name' => 'Alloy Steel Bar 4140 Dia 60mm', 'warehouse' => 'Pune Raw Store', 'current_stock' => '240 Kg', 'min_reorder' => '1,000 Kg', 'urgency' => 'CRITICAL'],
                        ['code' => 'CUT-CNMG-1204', 'name' => 'Carbide Cutting Inserts CNMG', 'warehouse' => 'Tool Crib Bay 4', 'current_stock' => '12 Pcs', 'min_reorder' => '50 Pcs', 'urgency' => 'HIGH'],
                        ['code' => 'OIL-HYD-VG68', 'name' => 'Hydraulic Oil VG 68 Grade', 'warehouse' => 'Consumables Bay', 'current_stock' => '80 Liters', 'min_reorder' => '200 Liters', 'urgency' => 'MEDIUM'],
                    ],
                    'recent_movements' => [
                        ['doc' => 'GRN-2026-0118', 'type' => 'Inward Purchase', 'item' => 'MS Plates 20mm (12,000 Kg)', 'source' => 'Tata Steel Ltd.', 'dest' => 'Raw Warehouse Bay 1', 'time' => '32 mins ago'],
                        ['doc' => 'TR-2026-0045', 'type' => 'Inter-Branch Transfer', 'item' => 'Forged Blanks (400 Pcs)', 'source' => 'Pune Fabrication', 'dest' => 'Bengaluru CNC Unit', 'time' => '2 hours ago'],
                        ['doc' => 'ISS-WO-0891', 'type' => 'Production Issue', 'item' => 'Round Bars (850 Kg)', 'source' => 'Raw Store', 'dest' => 'Shop Floor WIP', 'time' => '4 hours ago'],
                    ]
                ];

            case 'Production Head':
            case 'Production Engineer':
                return [
                    'view_type' => 'production',
                    'title' => 'Shop Floor & Production Execution Center',
                    'subtitle' => 'Work Orders, Machine Utilization, Routing Operations & WIP Tracking',
                    'cards' => [
                        ['label' => 'Active Work Orders', 'value' => '14 Orders', 'sub' => '12 on-time, 2 delayed', 'color' => 'blue'],
                        ['label' => 'Overall Plant OEE', 'value' => '87.4%', 'sub' => '+2.4% vs shift target', 'color' => 'emerald'],
                        ['label' => 'MTD Output (Tons)', 'value' => '1,240.5 T', 'sub' => '94% of monthly target', 'color' => 'purple'],
                        ['label' => 'Shop Floor Scrap Rate', 'value' => '0.8%', 'sub' => 'Benchmark: < 1.5%', 'color' => 'amber'],
                    ],
                    'lines' => [
                        ['name' => 'CNC Milling Line A (Pune)', 'current_wo' => 'WO-2026-0891 (Heavy Flange)', 'operator' => 'Dinesh Pawar', 'progress' => 78, 'status' => 'running', 'speed' => '98% efficiency'],
                        ['name' => 'Robotic Arc Welding Cell 2', 'current_wo' => 'WO-2026-0894 (Chassis Frame)', 'operator' => 'Sanjay Verma', 'progress' => 45, 'status' => 'running', 'speed' => '94% efficiency'],
                        ['name' => 'VMC Precision Line 4 (Bengaluru)', 'current_wo' => 'WO-2026-0902 (Shaft Pinion)', 'operator' => 'Karthik Rao', 'progress' => 92, 'status' => 'running', 'speed' => '96% efficiency'],
                        ['name' => 'Heat Treatment Furnace 1', 'current_wo' => 'WO-2026-0887 (Hardening Batch #4)', 'operator' => 'Gopal Shinde', 'progress' => 100, 'status' => 'cooling', 'speed' => 'Cooling Cycle'],
                    ],
                    'bottlenecks' => [
                        ['center' => 'CNC Horizontal Boring Center', 'load' => '104% Capacity', 'status' => 'Overallocated', 'action' => 'Split batch to Subcontractor'],
                        ['center' => 'Deburring & Finishing Station', 'load' => '92% Capacity', 'status' => 'High Load', 'action' => 'Add overtime shift'],
                    ]
                ];

            case 'Accounts & Finance Manager':
                return [
                    'view_type' => 'finance',
                    'title' => 'Financial & Cost Accounting Center',
                    'subtitle' => 'General Ledger, AP/AR, Manufacturing Costing & GST Reporting',
                    'cards' => [
                        ['label' => 'Accounts Receivable (AR)', 'value' => '₹1.85 Cr', 'sub' => 'Due within 30 days', 'color' => 'emerald'],
                        ['label' => 'Accounts Payable (AP)', 'value' => '₹94.20 L', 'sub' => 'Vendor liabilities pending', 'color' => 'amber'],
                        ['label' => 'Est. GST Net Liability', 'value' => '₹18.40 L', 'sub' => 'Output Tax - Eligible ITC', 'color' => 'blue'],
                        ['label' => 'Production Cost Variance', 'value' => '-1.4% Fave', 'sub' => 'Actual vs Standard Cost', 'color' => 'purple'],
                    ],
                    'cost_breakdown' => [
                        ['head' => 'Direct Raw Material Consumption', 'amount' => '₹84,20,000', 'share' => '62%'],
                        ['head' => 'Direct Shop Floor Labor Absorbed', 'amount' => '₹18,40,000', 'share' => '14%'],
                        ['head' => 'Machine Overheads Absorbed', 'amount' => '₹14,80,000', 'share' => '11%'],
                        ['head' => 'Factory Power & Consumables', 'amount' => '₹11,10,000', 'share' => '8%'],
                        ['head' => 'Plant Depreciation Allocation', 'amount' => '₹6,75,000', 'share' => '5%'],
                    ],
                    'pending_vouchers' => [
                        ['ref' => 'VCH-PO-2026-042', 'entity' => 'Tata Steel Ltd. (Raw Mat)', 'type' => 'Vendor Bill Match (3-Way)', 'amount' => '₹24,50,000', 'due' => 'In 5 days'],
                        ['ref' => 'VCH-INV-2026-089', 'entity' => 'Mahindra Auto Ltd. (Finished)', 'type' => 'Sales Invoice Collection', 'amount' => '₹38,20,000', 'due' => 'Overdue by 2 days'],
                        ['ref' => 'JRN-STK-ADJ-09', 'entity' => 'Quarterly Physical Count', 'type' => 'Stock Variance Adjustment', 'amount' => '₹42,150', 'due' => 'Approval Needed'],
                    ]
                ];

            case 'Machine Operator':
                return [
                    'view_type' => 'operator',
                    'title' => 'Shop Floor Machine Terminal',
                    'subtitle' => 'Live Job Card Tracking, Shift Production & Downtime Logging',
                    'cards' => [
                        ['label' => 'Current Assigned Machine', 'value' => 'CNC VMC-01', 'sub' => 'Pune Fabrication Line A', 'color' => 'blue'],
                        ['label' => 'Active Job Card', 'value' => 'JC-0891-OP1', 'sub' => 'Rough Face Milling', 'color' => 'amber'],
                        ['label' => 'Shift Target vs Output', 'value' => '86 / 120 Pcs', 'sub' => '71.6% achieved', 'color' => 'emerald'],
                        ['label' => 'Shift Machine Uptime', 'value' => '6.4 / 7.0 Hrs', 'sub' => '36 mins total idle', 'color' => 'purple'],
                    ],
                    'job_card' => [
                        'code' => 'JC-2026-0891-01',
                        'work_order' => 'WO-2026-0891 (Heavy Flange 400mm)',
                        'part_no' => 'PART-FLG-400-F',
                        'operation' => 'OP-10: Facing & Center Boring',
                        'drawing_ref' => 'DWG-FLG-2026-REV3',
                        'target_cycle_time' => '4 mins 30 secs',
                        'planned_qty' => 120,
                        'completed_qty' => 86,
                        'rejected_qty' => 1,
                    ]
                ];

            case 'Super Administrator':
            case 'Plant Manager':
            default:
                return [
                    'view_type' => 'executive',
                    'title' => 'Executive & General Plant Command Center',
                    'subtitle' => 'Multi-Plant Operations, Capacity Utilization, Financials & Security',
                    'cards' => [
                        ['label' => 'Active Work Orders', 'value' => '14 Orders', 'sub' => '12 on-track, 2 delayed', 'color' => 'blue'],
                        ['label' => 'Plant OEE & Availability', 'value' => '87.4%', 'sub' => '+2.4% vs benchmark', 'color' => 'emerald'],
                        ['label' => 'Total Inventory Valuation', 'value' => '₹4.82 Cr', 'sub' => 'Perpetual FIFO Ledger', 'color' => 'amber'],
                        ['label' => 'QA / QC First-Pass Yield', 'value' => '99.2%', 'sub' => 'Zero fatal defects', 'color' => 'purple'],
                    ],
                    'plants_summary' => [
                        ['name' => 'Pune Heavy Fabrication & Assembly Plant', 'head' => 'Vikram Deshmukh', 'active_wos' => 9, 'oee' => '88.2%', 'health' => 'Optimal'],
                        ['name' => 'Bengaluru CNC Machining Unit', 'head' => 'Karthik Rao', 'active_wos' => 5, 'oee' => '86.1%', 'health' => 'Optimal'],
                        ['name' => 'Mumbai Corporate Headquarters', 'head' => 'Meera Sundaram', 'active_wos' => 0, 'oee' => 'N/A', 'health' => 'Administrative'],
                    ],
                    'production_telemetry' => [
                        ['name' => 'CNC Milling Line A (Pune)', 'current_wo' => 'WO-2026-0891 (Heavy Flange)', 'operator' => 'Dinesh Pawar', 'progress' => 78, 'status' => 'running', 'speed' => '98% efficiency'],
                        ['name' => 'Robotic Arc Welding Cell 2', 'current_wo' => 'WO-2026-0894 (Chassis Frame)', 'operator' => 'Sanjay Verma', 'progress' => 45, 'status' => 'running', 'speed' => '94% efficiency'],
                        ['name' => 'VMC Precision Line 4 (Bengaluru)', 'current_wo' => 'WO-2026-0902 (Shaft Pinion)', 'operator' => 'Karthik Rao', 'progress' => 92, 'status' => 'running', 'speed' => '96% efficiency'],
                        ['name' => 'Heat Treatment Furnace 1', 'current_wo' => 'WO-2026-0887 (Hardening Batch #4)', 'operator' => 'Gopal Shinde', 'progress' => 100, 'status' => 'cooling', 'speed' => 'Cooling Cycle'],
                    ]
                ];
        }
    }
}
