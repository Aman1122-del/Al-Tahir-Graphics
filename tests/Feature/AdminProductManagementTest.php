<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role
        $this->adminRole = Role::create(['name' => 'admin']);
        
        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole($this->adminRole);
    }

    public function test_admin_can_view_services_index()
    {
        $response = $this->actingAs($this->admin)->get('/admin/services');

        $response->assertStatus(200);
        $response->assertSee('Products & Services');
    }

    public function test_admin_can_create_service()
    {
        $serviceData = [
            'title' => 'Test Service',
            'description' => 'Test Description',
            'price' => 100.00,
            'category' => 'Test Category',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/services', $serviceData);

        $response->assertRedirect('/admin/services');
        
        $this->assertDatabaseHas('services', [
            'title' => 'Test Service',
            'price' => 100.00,
        ]);
    }

    public function test_admin_can_edit_service()
    {
        $service = Service::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/services/{$service->id}/edit");

        $response->assertStatus(200);
        $response->assertSee($service->title);
    }

    public function test_admin_can_update_service()
    {
        $service = Service::factory()->create();

        $updateData = [
            'title' => 'Updated Service',
            'description' => 'Updated Description',
            'price' => 150.00,
            'category' => 'Updated Category',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->put("/admin/services/{$service->id}", $updateData);

        $response->assertRedirect('/admin/services');
        
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'title' => 'Updated Service',
            'price' => 150.00,
        ]);
    }

    public function test_admin_can_delete_service()
    {
        $service = Service::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/services/{$service->id}");

        $response->assertRedirect('/admin/services');
        
        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }

    public function test_regular_user_cannot_manage_services()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services');
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/services', []);
        $response->assertStatus(403);
    }
}