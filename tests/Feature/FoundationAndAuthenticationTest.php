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
        $response->assertSee('Apex Manufacturing ERP');
    }

    public function test_user_can_authenticate_and_access_dashboard(): void
    {
        $user = User::where('email', 'admin@apexerp.com')->first();
        $this->assertNotNull($user);

        $response = $this->post('/login', [
            'email' => 'admin@apexerp.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $dashboardResponse = $this->actingAs($user)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Manufacturing Command Center');
    }

    public function test_multi_company_and_branch_context_switching(): void
    {
        $user = User::where('email', 'admin@apexerp.com')->first();
        $branch = Branch::where('code', 'BLR-PLT')->first();
        $this->assertNotNull($branch);

        $response = $this->actingAs($user)->post('/switch-branch', [
            'branch_id' => $branch->id,
        ]);

        $response->assertSessionHas('current_branch_id', $branch->id);
    }
}
