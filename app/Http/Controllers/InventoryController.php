<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $stockLedger = DB::table('stock_ledger')
            ->join('products', 'stock_ledger.product_id', '=', 'products.id')
            ->join('warehouses', 'stock_ledger.warehouse_id', '=', 'warehouses.id')
            ->where('stock_ledger.company_id', $companyId)
            ->select('stock_ledger.*', 'products.name as product_name', 'products.code as product_code', 'products.uom', 'warehouses.name as warehouse_name')
            ->orderBy('stock_ledger.id', 'desc')
            ->get();

        $warehouses = DB::table('warehouses')->where('company_id', $companyId)->get();
        $products = DB::table('products')->where('company_id', $companyId)->get();

        $totalValuation = DB::table('products')->where('company_id', $companyId)->sum(DB::raw('current_stock * standard_cost'));
        $lowStockCount = DB::table('products')->where('company_id', $companyId)->whereColumn('current_stock', '<=', 'reorder_level')->count();

        return view('inventory.index', compact('stockLedger', 'warehouses', 'products', 'totalValuation', 'lowStockCount'));
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;
        $branchId = session('current_branch_id') ?? auth()->user()->branch_id;
        $product = DB::table('products')->where('id', $request->product_id)->first();

        // 1. Deduct from source warehouse
        DB::table('stock_ledger')->insert([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'warehouse_id' => $request->from_warehouse_id,
            'product_id' => $product->id,
            'batch_no' => 'TR-' . date('YmdHis'),
            'quantity_change' => -$request->quantity,
            'balance_after' => max(0, $product->current_stock - $request->quantity),
            'unit_cost' => $product->standard_cost,
            'total_value' => $request->quantity * $product->standard_cost,
            'voucher_type' => 'Stock Transfer Out',
            'source_doc' => 'TR-MANUAL-' . rand(100, 999),
            'remarks' => "Transfer to target warehouse",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Add to destination warehouse
        DB::table('stock_ledger')->insert([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'warehouse_id' => $request->to_warehouse_id,
            'product_id' => $product->id,
            'batch_no' => 'TR-' . date('YmdHis'),
            'quantity_change' => $request->quantity,
            'balance_after' => $product->current_stock,
            'unit_cost' => $product->standard_cost,
            'total_value' => $request->quantity * $product->standard_cost,
            'voucher_type' => 'Stock Transfer In',
            'source_doc' => 'TR-MANUAL-' . rand(100, 999),
            'remarks' => "Received from source warehouse",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::log('transfer', 'inventory', "Stock transfer: {$request->quantity} {$product->uom} of {$product->name}");

        return back()->with('success', "Stock transfer of {$request->quantity} {$product->uom} recorded successfully.");
    }
}
