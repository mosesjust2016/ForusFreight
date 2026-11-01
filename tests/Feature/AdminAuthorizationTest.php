<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_client_cannot_enter_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user, 'admin')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_client_cannot_mark_another_client_paid(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user, 'web')
            ->post(route('client.invoices.mark-paid', '999999'))
            ->assertForbidden();
    }
}
