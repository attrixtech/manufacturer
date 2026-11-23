<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('current_company_id') ?? auth()->user()->company_id;
        $type = $request->get('type', 'all');
        $search = $request->get('search');

        $query = DB::table('products')->where('company_id', $companyId);

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $allProducts = DB::table('products')->where('company_id', $companyId)->get();

        $stats = [
            'total' => $allProducts->count(),
            'finished_goods' => $allProducts->where('type', 'finished_good')->count(),
            'semi_finished' => $allProducts->where('type', 'semi_finished')->count(),
            'raw_materials' => $allProducts->where('type', 'raw_material')->count(),
            'consumables' => $allProducts->where('type', 'consumable')->count(),
        ];

        return view('products.index', compact('products', 'stats', 'type', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:products,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:finished_good,semi_finished,raw_material,consumable',
            'category' => 'required|string|max:100',
            'uom' => 'required|string|max:20',
            'standard_cost' => 'required|numeric|min:0',
            'current_stock' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        DB::table('products')->insert([
            'company_id' => $companyId,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'type' => $request->type,
            'category' => $request->category,
            'uom' => $request->uom,
            'standard_cost' => $request->standard_cost,
            'current_stock' => $request->current_stock ?? 0.00,
            'reorder_level' => $request->reorder_level ?? 10.00,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $typeName = str_replace('_', ' ', $request->type);
        ActivityLog::log('create', 'master_data', "Created product {$request->code} ({$typeName})");

        return back()->with('success', "Product '{$request->name}' ({$request->code}) successfully registered as a {$typeName}.");
    }
}
