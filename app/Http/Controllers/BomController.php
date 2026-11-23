<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BomController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $boms = DB::table('bom_headers')
            ->join('products', 'bom_headers.product_id', '=', 'products.id')
            ->where('bom_headers.company_id', $companyId)
            ->select('bom_headers.*', 'products.name as product_name', 'products.code as product_code', 'products.uom')
            ->get();

        foreach ($boms as $bom) {
            $bom->components = DB::table('bom_components')
                ->join('products', 'bom_components.product_id', '=', 'products.id')
                ->where('bom_components.bom_header_id', $bom->id)
                ->select('bom_components.*', 'products.name as component_name', 'products.code as component_code')
                ->get();
        }

        $products = DB::table('products')->where('company_id', $companyId)->get();

        return view('engineering.bom', compact('boms', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bom_number' => 'required|string|unique:bom_headers,bom_number',
            'product_id' => 'required|exists:products,id',
            'version' => 'required|string',
            'standard_cost' => 'required|numeric',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::table('bom_headers')->insert([
            'company_id' => $companyId,
            'bom_number' => $request->bom_number,
            'product_id' => $request->product_id,
            'version' => $request->version,
            'output_qty' => 1.00,
            'standard_cost' => $request->standard_cost,
            'status' => 'approved',
            'notes' => $request->notes ?? 'Engineering Approved Bill of Materials',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::log('create', 'engineering', "Created BOM: {$request->bom_number}");

        return back()->with('success', "Bill of Materials {$request->bom_number} registered successfully.");
    }
}
