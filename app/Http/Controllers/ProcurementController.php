<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $orders = DB::table('purchase_orders')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($orders as $order) {
            $order->items = DB::table('purchase_order_items')
                ->join('products', 'purchase_order_items.product_id', '=', 'products.id')
                ->where('purchase_order_items.purchase_order_id', $order->id)
                ->select('purchase_order_items.*', 'products.name as product_name', 'products.code as product_code', 'products.uom')
                ->get();
        }

        $products = DB::table('products')->where('company_id', $companyId)->get();

        return view('procurement.index', compact('orders', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_number' => 'required|string|unique:purchase_orders,po_number',
            'supplier_name' => 'required|string',
            'order_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;
        $branchId = session('current_branch_id') ?? auth()->user()->branch_id;
        $totalAmount = $request->quantity * $request->unit_price;

        $poId = DB::table('purchase_orders')->insertGetId([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'po_number' => $request->po_number,
            'supplier_name' => $request->supplier_name,
            'order_date' => $request->order_date,
            'expected_date' => now()->addDays(7),
            'total_amount' => $totalAmount,
            'status' => 'approved',
            'payment_status' => 'unpaid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('purchase_order_items')->insert([
            'purchase_order_id' => $poId,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'received_qty' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::log('create', 'procurement', "Created Purchase Order: {$request->po_number} for {$request->supplier_name}");

        return back()->with('success', "Purchase Order {$request->po_number} generated for ₹" . number_format($totalAmount, 2));
    }
}
