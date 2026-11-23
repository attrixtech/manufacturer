<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ManufacturingOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@apexerp.com')->first();
    }

    public function test_all_permitted_operation_views_render_successfully(): void
    {
        $routes = [
            'work-orders.index',
            'bom.index',
            'inventory.index',
            'quality.index',
            'procurement.index',
            'accounting.index',
            'maintenance.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get(route($route));
            $response->assertStatus(200);
        }
    }

    public function test_create_work_order(): void
    {
        $product = DB::table('products')->first();
        $response = $this->actingAs($this->admin)->post(route('work-orders.store'), [
            'wo_number' => 'WO-TEST-999',
            'product_id' => $product->id,
            'planned_qty' => 50,
            'work_center' => 'Assembly Bay 1',
            'target_date' => now()->addDays(5)->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('work_orders', ['wo_number' => 'WO-TEST-999']);
    }

    public function test_record_quality_inspection(): void
    {
        $inspection = DB::table('quality_inspections')->first();
        $response = $this->actingAs($this->admin)->post(route('quality.inspect', $inspection->id), [
            'status' => 'accepted',
            'accepted_qty' => 10,
            'rejected_qty' => 0,
            'findings' => 'Passed hardness & CMM testing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quality_inspections', [
            'id' => $inspection->id,
            'status' => 'accepted',
        ]);
    }

    public function test_create_purchase_order(): void
    {
        $product = DB::table('products')->first();
        $response = $this->actingAs($this->admin)->post(route('procurement.store'), [
            'po_number' => 'PO-TEST-888',
            'supplier_name' => 'Tata Steel Distribution',
            'order_date' => now()->toDateString(),
            'product_id' => $product->id,
            'quantity' => 120,
            'unit_price' => 50.00,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', ['po_number' => 'PO-TEST-888']);
    }

    public function test_log_machine_breakdown(): void
    {
        $machine = DB::table('machines')->first();
        $response = $this->actingAs($this->admin)->post(route('maintenance.breakdown.store'), [
            'machine_id' => $machine->id,
            'incident_number' => 'BD-TEST-777',
            'issue_description' => 'Spindle vibration threshold exceeded',
            'severity' => 'high',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('machine_breakdowns', ['incident_number' => 'BD-TEST-777']);
    }
}
