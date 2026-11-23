<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $workOrders = DB::table('work_orders')
            ->join('products', 'work_orders.product_id', '=', 'products.id')
            ->leftJoin('bom_headers', 'work_orders.bom_header_id', '=', 'bom_headers.id')
            ->leftJoin('routings', 'work_orders.routing_id', '=', 'routings.id')
            ->where('work_orders.company_id', $companyId)
            ->select(
                'work_orders.*',
                'products.name as product_name',
                'products.code as product_code',
                'products.uom',
                'products.current_stock as fg_stock',
                'bom_headers.bom_number',
                'bom_headers.version as bom_version',
                'routings.routing_code',
                'routings.name as routing_name'
            )
            ->orderBy('work_orders.id', 'desc')
            ->get();

        foreach ($workOrders as $wo) {
            // Material availability analysis
            $wo->components = [];
            $wo->has_shortage = false;
            if ($wo->bom_header_id) {
                $components = DB::table('bom_components')
                    ->join('products', 'bom_components.product_id', '=', 'products.id')
                    ->where('bom_components.bom_header_id', $wo->bom_header_id)
                    ->select(
                        'bom_components.*',
                        'products.name as item_name',
                        'products.code as item_code',
                        'products.current_stock',
                        'products.uom as item_uom'
                    )
                    ->get();

                foreach ($components as $c) {
                    $c->required_qty = (float)$c->quantity * (float)$wo->planned_qty;
                    $c->shortage_qty = max(0, $c->required_qty - (float)$c->current_stock);
                    if ($c->shortage_qty > 0) {
                        $wo->has_shortage = true;
                    }
                }
                $wo->components = $components;
            }

            // Operations routing steps
            $wo->operations = [];
            if ($wo->routing_id) {
                $wo->operations = DB::table('routing_operations')
                    ->where('routing_id', $wo->routing_id)
                    ->orderBy('sequence_no')
                    ->get();
            }

            // Progress percentage
            $wo->progress_pct = $wo->planned_qty > 0 ? min(100, round(($wo->completed_qty / $wo->planned_qty) * 100, 1)) : 0;
        }

        $products = DB::table('products')->where('company_id', $companyId)->get();
        $boms = DB::table('bom_headers')->where('company_id', $companyId)->get();
        $routings = DB::table('routings')->where('company_id', $companyId)->get();
        $machines = DB::table('machines')->where('company_id', $companyId)->get();

        $stats = [
            'total' => $workOrders->count(),
            'in_progress' => $workOrders->where('status', 'in_progress')->count(),
            'released' => $workOrders->where('status', 'released')->count(),
            'completed' => $workOrders->where('status', 'completed')->count(),
            'urgent_count' => $workOrders->whereIn('priority', ['urgent', 'high'])->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        return view('production.work_orders', compact('workOrders', 'products', 'boms', 'routings', 'machines', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wo_number' => 'required|string|unique:work_orders,wo_number',
            'product_id' => 'required|exists:products,id',
            'planned_qty' => 'required|numeric|min:1',
            'work_center' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'production_line' => 'nullable|string',
            'supervisor' => 'nullable|string',
            'sales_order_ref' => 'nullable|string',
            'bom_header_id' => 'nullable|exists:bom_headers,id',
            'routing_id' => 'nullable|exists:routings,id',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;
        $branchId = session('current_branch_id') ?? auth()->user()->branch_id;

        // Determine initial material status based on BOM
        $materialStatus = 'available';
        if ($request->bom_header_id) {
            $comps = DB::table('bom_components')
                ->join('products', 'bom_components.product_id', '=', 'products.id')
                ->where('bom_components.bom_header_id', $request->bom_header_id)
                ->select('bom_components.quantity', 'products.current_stock')
                ->get();

            foreach ($comps as $c) {
                if (((float)$c->quantity * (float)$request->planned_qty) > (float)$c->current_stock) {
                    $materialStatus = 'shortage';
                    break;
                }
            }
        }

        DB::table('work_orders')->insert([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'wo_number' => $request->wo_number,
            'product_id' => $request->product_id,
            'bom_header_id' => $request->bom_header_id ?: null,
            'routing_id' => $request->routing_id ?: null,
            'planned_qty' => $request->planned_qty,
            'completed_qty' => 0,
            'scrap_qty' => 0,
            'status' => 'released',
            'priority' => $request->priority,
            'sales_order_ref' => $request->sales_order_ref ?? 'MTS-INTERNAL',
            'supervisor' => $request->supervisor ?? auth()->user()->name,
            'production_line' => $request->production_line ?? 'Line 1',
            'work_center' => $request->work_center,
            'material_status' => $materialStatus,
            'start_date' => $request->start_date ?? now()->toDateString(),
            'target_date' => $request->target_date ?? now()->addDays(3)->toDateString(),
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::log('dispatch', 'production', "Dispatched Work Order #{$request->wo_number} for {$request->planned_qty} units.");

        return back()->with('success', "Work Order {$request->wo_number} dispatched to shop floor ({$materialStatus} material status).");
    }

    public function recordOutput(Request $request, $id)
    {
        $request->validate([
            'completed_delta' => 'required|numeric|min:0',
            'scrap_delta' => 'nullable|numeric|min:0',
            'operator_notes' => 'nullable|string',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;
        $wo = DB::table('work_orders')->where('id', $id)->where('company_id', $companyId)->first();

        if (!$wo) {
            return back()->withErrors(['error' => 'Work order not found.']);
        }

        $newCompleted = (float)$wo->completed_qty + (float)$request->completed_delta;
        $newScrap = (float)$wo->scrap_qty + (float)($request->scrap_delta ?? 0);

        $newStatus = $wo->status;
        if ($newCompleted >= (float)$wo->planned_qty) {
            $newStatus = 'completed';
        } elseif ($newCompleted > 0 && $wo->status === 'released') {
            $newStatus = 'in_progress';
        }

        DB::table('work_orders')->where('id', $id)->update([
            'completed_qty' => $newCompleted,
            'scrap_qty' => $newScrap,
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        ActivityLog::log('produce', 'mes', "Logged output for {$wo->wo_number}: +{$request->completed_delta} good, +{$request->scrap_delta} scrap.");

        return back()->with('success', "Production recorded for {$wo->wo_number}: {$newCompleted}/{$wo->planned_qty} completed.");
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,released,in_progress,completed,cancelled,on_hold',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::table('work_orders')->where('id', $id)->where('company_id', $companyId)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        ActivityLog::log('status_change', 'production', "Updated Work Order #{$id} status to {$request->status}");

        return back()->with('success', "Work Order status updated to {$request->status}.");
    }
}
