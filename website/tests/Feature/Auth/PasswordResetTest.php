<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Trigger a reset link send and capture the token from the email
     * BrevoMailService was asked to deliver.
     */
    private function requestResetLinkAndCaptureToken(User $user): string
    {
        $captured = null;

        $this->mock(BrevoMailService::class, function ($mock) use (&$captured) {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnUsing(function ($email, $name, $subject, $html) use (&$captured) {
                    $captured = $html;

                    return true;
                });
        });

        Volt::test('pages.auth.forgot-password')
            ->set('email', $user->email)
            ->call('sendPasswordResetLink');

        $this->assertNotNull($captured, 'BrevoMailService::send() was not called.');

        preg_match('#/reset-password/([^"?]+)#', $captured, $matches);

        $this->assertNotEmpty($matches, 'Could not find reset token in email content.');

        return $matches[1];
    }

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response
            ->assertSee('Reset Password')
            ->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        $user = User::factory()->create();

        $this->requestResetLinkAndCaptureToken($user);

        $this->assertDatabaseCount('password_reset_tokens', 1);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $token = $this->requestResetLinkAndCaptureToken($user);

        $response = $this->get('/reset-password/'.$token);

        $response
            ->assertSee('Reset Password')
            ->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();

        $token = $this->requestResetLinkAndCaptureToken($user);

        $component = Volt::test('pages.auth.reset-password', ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('resetPassword');

        $component
            ->assertRedirect('/login')
            ->assertHasNoErrors();
    }
}
