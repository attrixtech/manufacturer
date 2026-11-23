<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BomController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        // Fetch all BOM headers with products and optional routing
        $boms = DB::table('bom_headers')
            ->join('products', 'bom_headers.product_id', '=', 'products.id')
            ->leftJoin('routings', 'bom_headers.routing_id', '=', 'routings.id')
            ->where('bom_headers.company_id', $companyId)
            ->select(
                'bom_headers.*',
                'products.name as product_name',
                'products.code as product_code',
                'products.uom',
                'routings.routing_code',
                'routings.name as routing_name',
                'routings.total_cycle_time_mins'
            )
            ->orderBy('bom_headers.id', 'desc')
            ->get();

        foreach ($boms as $bom) {
            $bom->components = DB::table('bom_components')
                ->join('products', 'bom_components.product_id', '=', 'products.id')
                ->where('bom_components.bom_header_id', $bom->id)
                ->select(
                    'bom_components.*',
                    'products.name as component_name',
                    'products.code as component_code',
                    'products.uom as component_uom',
                    'products.current_stock'
                )
                ->orderBy('bom_components.operation_seq')
                ->get();
        }

        // Fetch all Routings with their operations
        $routings = DB::table('routings')
            ->leftJoin('products', 'routings.product_id', '=', 'products.id')
            ->where('routings.company_id', $companyId)
            ->select('routings.*', 'products.name as product_name', 'products.code as product_code')
            ->orderBy('routings.id', 'desc')
            ->get();

        foreach ($routings as $routing) {
            $routing->operations = DB::table('routing_operations')
                ->where('routing_id', $routing->id)
                ->orderBy('sequence_no')
                ->get();
        }

        $products = DB::table('products')->where('company_id', $companyId)->get();
        $warehouses = DB::table('warehouses')->where('company_id', $companyId)->get();

        $stats = [
            'total_boms' => $boms->count(),
            'approved_boms' => $boms->where('approval_status', 'approved')->count(),
            'total_routings' => $routings->count(),
            'avg_cost' => $boms->count() > 0 ? $boms->avg('standard_cost') : 0,
        ];

        return view('engineering.bom', compact('boms', 'routings', 'products', 'warehouses', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bom_number' => 'required|string|unique:bom_headers,bom_number',
            'product_id' => 'required|exists:products,id',
            'version' => 'required|string',
            'output_qty' => 'required|numeric|min:0.01',
            'routing_id' => 'nullable|exists:routings,id',
            'components' => 'nullable|array',
            'components.*.product_id' => 'required|exists:products,id',
            'components.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::beginTransaction();
        try {
            $materialCost = 0;
            $componentsData = [];

            if ($request->has('components') && is_array($request->components)) {
                foreach ($request->components as $item) {
                    $prod = DB::table('products')->where('id', $item['product_id'])->first();
                    $unitCost = (float)($item['unit_cost'] ?? $prod->standard_cost ?? 0);
                    $qty = (float)$item['quantity'];
                    $subtotal = $qty * $unitCost;
                    $materialCost += $subtotal;

                    $componentsData[] = [
                        'product_id' => $item['product_id'],
                        'component_type' => $item['component_type'] ?? 'raw_material',
                        'quantity' => $qty,
                        'uom' => $prod->uom ?? 'Pcs',
                        'unit_cost' => $unitCost,
                        'subtotal_cost' => $subtotal,
                        'scrap_percentage' => (float)($item['scrap_percentage'] ?? 0),
                        'operation_seq' => (int)($item['operation_seq'] ?? 10),
                        'issue_warehouse' => $item['issue_warehouse'] ?? null,
                        'remarks' => $item['remarks'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Calculate labour and overhead cost if routing assigned
            $labourCost = 0;
            $overheadCost = 0;
            if ($request->routing_id) {
                $ops = DB::table('routing_operations')->where('routing_id', $request->routing_id)->get();
                foreach ($ops as $op) {
                    $hours = ($op->run_time_mins + $op->setup_time_mins) / 60;
                    $labourCost += $hours * (float)$op->hourly_cost;
                }
                $overheadCost = $labourCost * 0.35; // Standard 35% factory overhead allocation
            }

            $totalStandardCost = $request->standard_cost ?: ($materialCost + $labourCost + $overheadCost);

            $bomId = DB::table('bom_headers')->insertGetId([
                'company_id' => $companyId,
                'bom_number' => $request->bom_number,
                'product_id' => $request->product_id,
                'routing_id' => $request->routing_id ?: null,
                'version' => $request->version,
                'output_qty' => $request->output_qty,
                'standard_cost' => $totalStandardCost,
                'material_cost' => $materialCost,
                'labour_cost' => $labourCost,
                'overhead_cost' => $overheadCost,
                'status' => 'approved',
                'approval_status' => 'approved',
                'effective_date' => $request->effective_date ?? now()->toDateString(),
                'expiry_date' => $request->expiry_date ?? null,
                'change_reason' => $request->change_reason ?? 'Initial engineering release',
                'notes' => $request->notes ?? 'Engineering Approved Bill of Materials',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($componentsData as &$comp) {
                $comp['bom_header_id'] = $bomId;
                DB::table('bom_components')->insert($comp);
            }

            DB::commit();
            ActivityLog::log('create', 'engineering', "Created comprehensive BOM {$request->bom_number} with " . count($componentsData) . " components.");

            return back()->with('success', "Standard Bill of Materials {$request->bom_number} created with dynamic costing.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create BOM: ' . $e->getMessage()]);
        }
    }

    public function storeRouting(Request $request)
    {
        $request->validate([
            'routing_code' => 'required|string|unique:routings,routing_code',
            'name' => 'required|string',
            'version' => 'required|string',
            'product_id' => 'nullable|exists:products,id',
            'operations' => 'nullable|array',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::beginTransaction();
        try {
            $totalCycleMins = 0;
            if ($request->has('operations') && is_array($request->operations)) {
                foreach ($request->operations as $op) {
                    $totalCycleMins += (int)($op['run_time_mins'] ?? 0) + (int)($op['setup_time_mins'] ?? 0);
                }
            }

            $routingId = DB::table('routings')->insertGetId([
                'company_id' => $companyId,
                'product_id' => $request->product_id ?: null,
                'routing_code' => $request->routing_code,
                'name' => $request->name,
                'version' => $request->version,
                'status' => 'active',
                'total_cycle_time_mins' => $totalCycleMins,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->has('operations') && is_array($request->operations)) {
                foreach ($request->operations as $op) {
                    if (empty($op['operation_name'])) continue;
                    DB::table('routing_operations')->insert([
                        'routing_id' => $routingId,
                        'sequence_no' => $op['sequence_no'] ?? 10,
                        'operation_name' => $op['operation_name'],
                        'work_center' => $op['work_center'] ?? 'General Line',
                        'setup_time_mins' => $op['setup_time_mins'] ?? 15,
                        'run_time_mins' => $op['run_time_mins'] ?? 30,
                        'hourly_cost' => $op['hourly_cost'] ?? 400.00,
                        'requires_qc' => !empty($op['requires_qc']),
                        'instructions' => $op['instructions'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            ActivityLog::log('create', 'engineering', "Created Manufacturing Routing: {$request->routing_code}");

            return back()->with('success', "Manufacturing Routing {$request->routing_code} registered successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create Routing: ' . $e->getMessage()]);
        }
    }

    public function approve($id)
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::table('bom_headers')->where('id', $id)->where('company_id', $companyId)->update([
            'approval_status' => 'approved',
            'status' => 'approved',
            'updated_at' => now(),
        ]);

        ActivityLog::log('approve', 'engineering', "Approved BOM Version #{$id}");

        return back()->with('success', "BOM #{$id} has been formally approved for shop floor execution.");
    }
}
