<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationsDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        $punePlant = Branch::where('code', 'PUNE-PLT')->first();
        $branchId = $punePlant?->id;

        // 1. Products
        $products = [
            ['code' => 'PART-FLG-400-F', 'name' => 'Heavy Forged Flange 400mm Class 150', 'type' => 'finished_good', 'category' => 'Flanges & Valves', 'uom' => 'Pcs', 'standard_cost' => 4850.00, 'current_stock' => 140.00, 'reorder_level' => 30.00],
            ['code' => 'SF-FLG-BLANK', 'name' => 'Flange Forged Blank 400mm Rough Turn', 'type' => 'semi_finished', 'category' => 'Forgings', 'uom' => 'Pcs', 'standard_cost' => 3200.00, 'current_stock' => 85.00, 'reorder_level' => 20.00],
            ['code' => 'RM-STEEL-4140', 'name' => 'Alloy Steel Bar 4140 Dia 60mm', 'type' => 'raw_material', 'category' => 'Raw Steel', 'uom' => 'Kg', 'standard_cost' => 145.00, 'current_stock' => 240.00, 'reorder_level' => 1000.00],
            ['code' => 'RM-STEEL-316L', 'name' => 'Stainless Steel Round Bar 316L 50mm', 'type' => 'raw_material', 'category' => 'Raw Steel', 'uom' => 'Kg', 'standard_cost' => 380.00, 'current_stock' => 1500.00, 'reorder_level' => 500.00],
            ['code' => 'CUT-CNMG-1204', 'name' => 'Carbide Cutting Inserts CNMG 120408', 'type' => 'consumable', 'category' => 'Tooling & Inserts', 'uom' => 'Pcs', 'standard_cost' => 280.00, 'current_stock' => 12.00, 'reorder_level' => 50.00],
            ['code' => 'OIL-HYD-VG68', 'name' => 'Hydraulic Oil VG 68 Grade Industrial', 'type' => 'consumable', 'category' => 'Lubricants', 'uom' => 'Liters', 'standard_cost' => 210.00, 'current_stock' => 80.00, 'reorder_level' => 200.00],
        ];

        $productMap = [];
        foreach ($products as $p) {
            $id = DB::table('products')->insertGetId(array_merge($p, [
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $productMap[$p['code']] = $id;
        }

        // 2. Warehouses
        $warehouses = [
            ['code' => 'WH-RAW-01', 'name' => 'Raw Material Central Store', 'type' => 'raw_store', 'location' => 'Bay A-1'],
            ['code' => 'WH-WIP-01', 'name' => 'Shop Floor WIP Staging', 'type' => 'wip_floor', 'location' => 'Floor Line 1'],
            ['code' => 'WH-FG-01', 'name' => 'Finished Goods Warehouse', 'type' => 'finished_store', 'location' => 'Bay D-3'],
            ['code' => 'WH-QRN-01', 'name' => 'QA Quarantine Inspection Bay', 'type' => 'quarantine', 'location' => 'Inward Dock 2'],
        ];

        $warehouseMap = [];
        foreach ($warehouses as $wh) {
            $id = DB::table('warehouses')->insertGetId(array_merge($wh, [
                'company_id' => $company->id,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $warehouseMap[$wh['code']] = $id;
        }

        // 3. BOM
        $bomId = DB::table('bom_headers')->insertGetId([
            'company_id' => $company->id,
            'bom_number' => 'BOM-FLG-400-REV1',
            'product_id' => $productMap['PART-FLG-400-F'],
            'version' => 'REV-01',
            'output_qty' => 1.00,
            'standard_cost' => 4850.00,
            'status' => 'approved',
            'notes' => 'Class 150 ANSI Standard Heavy Forged Flange Production Bill of Materials',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('bom_components')->insert([
            [
                'bom_header_id' => $bomId,
                'product_id' => $productMap['SF-FLG-BLANK'],
                'quantity' => 1.0000,
                'uom' => 'Pcs',
                'unit_cost' => 3200.00,
                'scrap_percentage' => 1.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bom_header_id' => $bomId,
                'product_id' => $productMap['CUT-CNMG-1204'],
                'quantity' => 0.1000,
                'uom' => 'Pcs',
                'unit_cost' => 280.00,
                'scrap_percentage' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Work Orders
        $workOrders = [
            ['wo_number' => 'WO-2026-0891', 'product_id' => $productMap['PART-FLG-400-F'], 'bom_header_id' => $bomId, 'planned_qty' => 100, 'completed_qty' => 78, 'scrap_qty' => 1, 'status' => 'in_progress', 'work_center' => 'CNC Milling Line A (Pune)', 'start_date' => now()->subDays(2), 'target_date' => now()->addDays(2)],
            ['wo_number' => 'WO-2026-0894', 'product_id' => $productMap['PART-FLG-400-F'], 'bom_header_id' => $bomId, 'planned_qty' => 50, 'completed_qty' => 22, 'scrap_qty' => 0, 'status' => 'in_progress', 'work_center' => 'Robotic Arc Welding Cell 2', 'start_date' => now()->subDay(), 'target_date' => now()->addDays(3)],
            ['wo_number' => 'WO-2026-0902', 'product_id' => $productMap['SF-FLG-BLANK'], 'bom_header_id' => null, 'planned_qty' => 200, 'completed_qty' => 184, 'scrap_qty' => 2, 'status' => 'in_progress', 'work_center' => 'VMC Precision Line 4', 'start_date' => now()->subDays(3), 'target_date' => now()->addDay()],
            ['wo_number' => 'WO-2026-0887', 'product_id' => $productMap['PART-FLG-400-F'], 'bom_header_id' => $bomId, 'planned_qty' => 120, 'completed_qty' => 120, 'scrap_qty' => 1, 'status' => 'completed', 'work_center' => 'Heat Treatment Furnace 1', 'start_date' => now()->subDays(5), 'target_date' => now()->subDay()],
        ];

        foreach ($workOrders as $wo) {
            DB::table('work_orders')->insert(array_merge($wo, [
                'company_id' => $company->id,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 5. Stock Ledger
        $ledgerEntries = [
            ['warehouse_id' => $warehouseMap['WH-RAW-01'], 'product_id' => $productMap['RM-STEEL-316L'], 'batch_no' => 'LOT-SS316-092', 'quantity_change' => 1500.00, 'balance_after' => 1500.00, 'unit_cost' => 380.00, 'total_value' => 570000.00, 'voucher_type' => 'GRN', 'source_doc' => 'GRN-2026-0118', 'remarks' => 'Inward receipt from Jindal Stainless Ltd.'],
            ['warehouse_id' => $warehouseMap['WH-RAW-01'], 'product_id' => $productMap['RM-STEEL-4140'], 'batch_no' => 'LOT-4140-B3', 'quantity_change' => -200.00, 'balance_after' => 240.00, 'unit_cost' => 145.00, 'total_value' => 29000.00, 'voucher_type' => 'Production Issue', 'source_doc' => 'WO-2026-0891', 'remarks' => 'Issued to shop floor line A'],
            ['warehouse_id' => $warehouseMap['WH-FG-01'], 'product_id' => $productMap['PART-FLG-400-F'], 'batch_no' => 'LOT-FLG-0887', 'quantity_change' => 120.00, 'balance_after' => 140.00, 'unit_cost' => 4850.00, 'total_value' => 582000.00, 'voucher_type' => 'Production Receipt', 'source_doc' => 'WO-2026-0887', 'remarks' => 'Completed batch transfer from Heat Treatment'],
        ];

        foreach ($ledgerEntries as $entry) {
            DB::table('stock_ledger')->insert(array_merge($entry, [
                'company_id' => $company->id,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 6. Purchase Orders
        $pos = [
            ['po_number' => 'PO-2026-0142', 'supplier_name' => 'Tata Steel Limited', 'order_date' => now()->subDays(4), 'expected_date' => now()->addDays(5), 'total_amount' => 2450000.00, 'status' => 'approved', 'payment_status' => 'unpaid'],
            ['po_number' => 'PO-2026-0145', 'supplier_name' => 'Bharat Forge Limited', 'order_date' => now()->subDays(6), 'expected_date' => now()->addDays(2), 'total_amount' => 1280000.00, 'status' => 'received_partial', 'payment_status' => 'partial'],
            ['po_number' => 'PO-2026-0148', 'supplier_name' => 'Sandvik Coromant India', 'order_date' => now()->subDays(2), 'expected_date' => now()->addDays(8), 'total_amount' => 340000.00, 'status' => 'approved', 'payment_status' => 'unpaid'],
        ];

        foreach ($pos as $po) {
            $poId = DB::table('purchase_orders')->insertGetId(array_merge($po, [
                'company_id' => $company->id,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::table('purchase_order_items')->insert([
                'purchase_order_id' => $poId,
                'product_id' => $productMap['RM-STEEL-4140'],
                'quantity' => 1000.00,
                'unit_price' => 145.00,
                'received_qty' => $po['status'] === 'received_partial' ? 400.00 : 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Quality Inspections & NCRs
        $inspections = [
            ['inspection_number' => 'QC-2026-0312', 'type' => 'incoming_iqc', 'product_id' => $productMap['RM-STEEL-316L'], 'batch_no' => 'LOT-SS316-092', 'reference_doc' => 'GRN-2026-0118', 'sample_size' => 15.00, 'accepted_qty' => 0.00, 'rejected_qty' => 0.00, 'status' => 'pending', 'findings' => 'Chemical spectrometer lab assay and dimensional check pending'],
            ['inspection_number' => 'QC-2026-0315', 'type' => 'in_process_ipqc', 'product_id' => $productMap['PART-FLG-400-F'], 'batch_no' => 'WO-2026-0891-OP2', 'reference_doc' => 'WO-2026-0891', 'sample_size' => 10.00, 'accepted_qty' => 10.00, 'rejected_qty' => 0.00, 'status' => 'accepted', 'findings' => 'Facing tolerance within +/- 0.02mm, surface finish Ra 1.6 certified'],
            ['inspection_number' => 'QC-2026-0318', 'type' => 'final_fqc', 'product_id' => $productMap['PART-FLG-400-F'], 'batch_no' => 'LOT-FLG-0887', 'reference_doc' => 'WO-2026-0887', 'sample_size' => 20.00, 'accepted_qty' => 20.00, 'rejected_qty' => 0.00, 'status' => 'accepted', 'findings' => 'Hardness Rockwell C 60 HRC verified. Ready for dispatch.'],
        ];

        foreach ($inspections as $insp) {
            DB::table('quality_inspections')->insert(array_merge($insp, [
                'company_id' => $company->id,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        DB::table('non_conformance_reports')->insert([
            [
                'company_id' => $company->id,
                'ncr_number' => 'NCR-2026-014',
                'title' => 'Heat treatment hardness deviation on sample batch',
                'description' => 'Hardness tested at 52 HRC, which falls below mandatory spec of 58-62 HRC.',
                'severity' => 'major',
                'status' => 'open',
                'disposition' => 'Under metallurgical review for re-hardening cycle',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'ncr_number' => 'NCR-2026-015',
                'title' => 'Internal thread pitch burrs detected on flange holes',
                'description' => 'Minor burrs detected on tapped holes due to worn tap tool.',
                'severity' => 'minor',
                'status' => 'investigating',
                'disposition' => 'Rework scheduled for deburring and tool replacement',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 8. Machines & Breakdowns
        $machines = [
            ['code' => 'MC-VMC-01', 'name' => 'CNC Milling Center VMC-01', 'work_center' => 'CNC Milling Line A (Pune)', 'status' => 'running', 'spindle_load' => 72, 'coolant_pressure' => 4.80, 'tool_life' => 84],
            ['code' => 'MC-ROBOT-02', 'name' => 'Robotic Arc Welding Cell 2', 'work_center' => 'Welding Bay 2', 'status' => 'running', 'spindle_load' => 65, 'coolant_pressure' => 5.20, 'tool_life' => 91],
            ['code' => 'MC-VMC-04', 'name' => 'VMC Precision Line 4 (Bengaluru)', 'work_center' => 'Bengaluru CNC Hub', 'status' => 'running', 'spindle_load' => 78, 'coolant_pressure' => 4.90, 'tool_life' => 76],
            ['code' => 'MC-FURNACE-01', 'name' => 'Heat Treatment Furnace 1', 'work_center' => 'Heat Treatment Facility', 'status' => 'maintenance', 'spindle_load' => 20, 'coolant_pressure' => 3.50, 'tool_life' => 100],
        ];

        $machineMap = [];
        foreach ($machines as $mc) {
            $id = DB::table('machines')->insertGetId(array_merge($mc, [
                'company_id' => $company->id,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $machineMap[$mc['code']] = $id;
        }

        DB::table('machine_breakdowns')->insert([
            'company_id' => $company->id,
            'machine_id' => $machineMap['MC-FURNACE-01'],
            'incident_number' => 'INC-2026-008',
            'reported_by' => 'Gopal Shinde',
            'issue_description' => 'Furnace thermocouple sensor calibration drift detected during cooling ramp down.',
            'severity' => 'medium',
            'downtime_minutes' => 45,
            'status' => 'under_repair',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 9. Chart of Accounts & Journal Entries
        $accounts = [
            ['code' => '1001', 'name' => 'Raw Materials Inventory', 'type' => 'asset', 'balance' => 24500000.00],
            ['code' => '1002', 'name' => 'Work-In-Progress (WIP) Inventory', 'type' => 'asset', 'balance' => 13500000.00],
            ['code' => '1003', 'name' => 'Finished Goods Inventory', 'type' => 'asset', 'balance' => 10250000.00],
            ['code' => '1010', 'name' => 'State Bank of India (Operations)', 'type' => 'asset', 'balance' => 8420000.00],
            ['code' => '2001', 'name' => 'Accounts Payable (Trade Vendors)', 'type' => 'liability', 'balance' => 9420000.00],
            ['code' => '2002', 'name' => 'GRN Suspense / Clearing Account', 'type' => 'liability', 'balance' => 2450000.00],
            ['code' => '2010', 'name' => 'GST Output Tax Liability (IGST/CGST/SGST)', 'type' => 'liability', 'balance' => 1840000.00],
            ['code' => '4001', 'name' => 'Domestic Manufacturing Sales Revenue', 'type' => 'revenue', 'balance' => 64500000.00],
            ['code' => '5001', 'name' => 'Direct Shop Floor Wages Absorbed', 'type' => 'expense', 'balance' => 1840000.00],
            ['code' => '5002', 'name' => 'Factory Machine Power & Overheads', 'type' => 'expense', 'balance' => 1480000.00],
        ];

        foreach ($accounts as $acc) {
            DB::table('chart_of_accounts')->insert(array_merge($acc, [
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        DB::table('journal_entries')->insert([
            [
                'company_id' => $company->id,
                'entry_number' => 'JRN-2026-089',
                'entry_date' => now()->subDay(),
                'reference_doc' => 'GRN-2026-0118',
                'description' => 'Automated inventory posting for Raw Material GRN receipt from Jindal Stainless Ltd.',
                'total_amount' => 570000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'entry_number' => 'JRN-2026-090',
                'entry_date' => now(),
                'reference_doc' => 'WO-2026-0891',
                'description' => 'Material issue posting from Raw Stores to WIP Floor Line 1',
                'total_amount' => 29000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
