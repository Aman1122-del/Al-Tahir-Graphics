<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->designerRole = Role::create(['name' => 'designer']);
        $this->supportRole = Role::create(['name' => 'support']);
        
        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole($this->adminRole);
    }

    public function test_admin_can_view_users_index()
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Users Management');
    }

    public function test_admin_can_create_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['designer'],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/users', $userData);

        $response->assertRedirect('/admin/users');
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue($user->hasRole('designer'));
    }

    public function test_admin_can_edit_user()
    {
        $user = User::factory()->create();
        $user->assignRole($this->designerRole);

        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_admin_can_update_user()
    {
        $user = User::factory()->create();
        $user->assignRole($this->designerRole);

        $updateData = [
            'name' => 'Updated User',
            'email' => $user->email,
            'roles' => ['support'],
        ];

        $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id}", $updateData);

        $response->assertRedirect('/admin/users');
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
        ]);

        $user->refresh();
        $this->assertTrue($user->hasRole('support'));
        $this->assertFalse($user->hasRole('designer'));
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/users/{$user->id}");

        $response->assertRedirect('/admin/users');
        
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_themselves()
    {
        $response = $this->actingAs($this->admin)->delete("/admin/users/{$this->admin->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot delete your own account.');
        
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_toggle_user_roles()
    {
        $user = User::factory()->create();
        $user->assignRole($this->designerRole);

        // Toggle to support role
        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => ['support'],
        ];

        $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id}", $updateData);

        $response->assertRedirect('/admin/users');
        
        $user->refresh();
        $this->assertTrue($user->hasRole('support'));
        $this->assertFalse($user->hasRole('designer'));
    }

    public function test_regular_user_cannot_manage_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/users', []);
        $response->assertStatus(403);
    }
}