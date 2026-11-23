<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $workOrders = DB::table('work_orders')
            ->join('products', 'work_orders.product_id', '=', 'products.id')
            ->leftJoin('bom_headers', 'work_orders.bom_header_id', '=', 'bom_headers.id')
            ->where('work_orders.company_id', $companyId)
            ->select('work_orders.*', 'products.name as product_name', 'products.code as product_code', 'products.uom', 'bom_headers.bom_number')
            ->orderBy('work_orders.id', 'desc')
            ->get();

        $products = DB::table('products')->where('company_id', $companyId)->get();
        $boms = DB::table('bom_headers')->where('company_id', $companyId)->get();

        $stats = [
            'total' => $workOrders->count(),
            'in_progress' => $workOrders->where('status', 'in_progress')->count(),
            'released' => $workOrders->where('status', 'released')->count(),
            'completed' => $workOrders->where('status', 'completed')->count(),
        ];

        return view('production.work_orders', compact('workOrders', 'products', 'boms', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wo_number' => 'required|string|unique:work_orders,wo_number',
            'product_id' => 'required|exists:products,id',
            'planned_qty' => 'required|numeric|min:1',
            'work_center' => 'required|string',
            'target_date' => 'nullable|date',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;
        $branchId = session('current_branch_id') ?? auth()->user()->branch_id;

        DB::table('work_orders')->insert([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'wo_number' => $request->wo_number,
            'product_id' => $request->product_id,
            'bom_header_id' => $request->bom_header_id ?: null,
            'planned_qty' => $request->planned_qty,
            'completed_qty' => 0,
            'scrap_qty' => 0,
            'status' => 'released',
            'work_center' => $request->work_center,
            'start_date' => now(),
            'target_date' => $request->target_date ?: now()->addDays(3),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::log('create', 'production', "Created Work Order: {$request->wo_number}");

        return back()->with('success', "Work Order {$request->wo_number} created and released to floor.");
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:draft,released,in_progress,completed,cancelled']);

        DB::table('work_orders')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        ActivityLog::log('status_change', 'production', "Work Order #{$id} status updated to {$request->status}");

        return back()->with('success', "Work order status updated to {$request->status}.");
    }
}
