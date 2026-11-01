<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Volt;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_enable_email_two_factor_authentication(): void
    {
        $user = User::factory()->create(['two_factor_enabled' => false]);

        $this->actingAs($user)
            ->put(route('client.2fa.update'), ['two_factor_enabled' => '1'])
            ->assertSessionHas('success');

        $this->assertTrue((bool) $user->fresh()->two_factor_enabled);
    }

    public function test_two_factor_challenge_authenticates_only_with_a_valid_code(): void
    {
        Mail::fake();
        $user = User::factory()->create(['two_factor_enabled' => true]);
        $code = $user->generateEmailOtp();
        session(['two_factor_user_id' => $user->id, 'two_factor_guard' => 'web']);

        Volt::test('pages.auth.two-factor')
            ->set('otp', '000000')
            ->call('verify')
            ->assertHasErrors('otp');

        Volt::test('pages.auth.two-factor')
            ->set('otp', $code)
            ->call('verify')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated('web');
    }

    public function test_two_factor_challenge_can_restore_an_admin_guard_session(): void
    {
        $user = User::factory()->create(['is_admin' => true, 'two_factor_enabled' => true]);
        $code = $user->generateEmailOtp();
        session(['two_factor_user_id' => $user->id, 'two_factor_guard' => 'admin']);

        Volt::test('pages.auth.two-factor')
            ->set('otp', $code)
            ->call('verify')
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticated('admin');
    }
}
