<?php

namespace Tests\Feature\Auth;

use App\Models\PhoneCountry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        PhoneCountry::create(['name' => 'Zambia', 'dial_code' => '260', 'is_active' => true]);

        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSee('Create Account');
    }

    public function test_new_users_can_register(): void
    {
        PhoneCountry::create(['name' => 'Zambia', 'dial_code' => '260', 'is_active' => true]);

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('phone_country', '260')
            ->set('phone', '961234567')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('agree_terms', true);

        $component->call('register');

        $component->assertRedirect(route('verification.notice', absolute: false));

        $this->assertAuthenticated();
    }
}
