<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard()
    {
        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);
        
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Login as admin
        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_regular_user_cannot_access_admin_dashboard()
    {
        // Create regular user
        $user = User::factory()->create();

        // Try to access admin dashboard
        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        // Try to access admin dashboard without login
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_support_user_can_access_admin_dashboard()
    {
        // Create support role
        $supportRole = Role::create(['name' => 'support']);
        
        // Create support user
        $support = User::factory()->create();
        $support->assignRole($supportRole);

        // Login as support
        $response = $this->actingAs($support)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }
}