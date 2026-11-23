<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationAndAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSeeText('thrumble.manufacturer');
        $response->assertSeeText('Attrix Technologies');
        $response->assertSeeText('Infusionn Pvt. Ltd.');
    }

    public function test_super_admin_sees_executive_command_center_and_admin_nav(): void
    {
        $admin = User::where('email', 'admin@thrumble.manufacturer')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Executive & General Plant Command Center');
        $response->assertSeeText('Enterprise Administration');
        $response->assertSeeText('Companies & Plants');
        $response->assertSeeText('Users, Roles & RBAC');
        $response->assertSeeText('Attrix Technologies');
        $response->assertSeeText('Infusionn Pvt. Ltd.');
    }

    public function test_quality_inspector_sees_quality_dashboard_and_no_admin_nav(): void
    {
        $qualityUser = User::where('email', 'quality@thrumble.manufacturer')->first();
        $this->assertNotNull($qualityUser);

        $response = $this->actingAs($qualityUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('QA / QC Quality Command Center');
        $response->assertSeeText('Pending IQC Inspections');
        $response->assertSeeText('Inspection Clearance Queue');
        $response->assertDontSeeText('Companies & Plants');
        $response->assertDontSeeText('Enterprise Administration');
    }

    public function test_inventory_manager_sees_inventory_dashboard_and_stock_alerts(): void
    {
        $invUser = User::where('email', 'inventory@thrumble.manufacturer')->first();
        $this->assertNotNull($invUser);

        $response = $this->actingAs($invUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Warehouse & Inventory Command Center');
        $response->assertSeeText('Critical Low Stock & Reorder Triggers');
        $response->assertSeeText('Total Stock Valuation');
        $response->assertDontSeeText('Companies & Plants');
        $response->assertDontSeeText('Enterprise Administration');
    }

    public function test_operator_sees_machine_terminal_dashboard(): void
    {
        $operator = User::where('email', 'operator@thrumble.manufacturer')->first();
        $this->assertNotNull($operator);

        $response = $this->actingAs($operator)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('Shop Floor Machine Terminal');
        $response->assertSeeText('Active Shop Floor Job Card');
        $response->assertSeeText('Spindle Load');
        $response->assertDontSeeText('Companies & Plants');
        $response->assertDontSeeText('Enterprise Administration');
    }

    public function test_multi_company_and_branch_context_switching(): void
    {
        $user = User::where('email', 'admin@thrumble.manufacturer')->first();
        $branch = Branch::where('code', 'BLR-PLT')->first();
        $this->assertNotNull($branch);

        $response = $this->actingAs($user)->post('/switch-branch', [
            'branch_id' => $branch->id,
        ]);

        $response->assertSessionHas('current_branch_id', $branch->id);
    }
}
