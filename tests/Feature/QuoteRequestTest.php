<?php

namespace Tests\Feature;

use App\Services\BrevoMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_submission_is_saved_for_admin_follow_up(): void
    {
        $this->mock(BrevoMailService::class, function ($mock) {
            $mock->shouldReceive('send')->once()->andReturn(true);
        });

        $this->postJson(route('quote.submit'), [
            'service_type' => 'road-freight',
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '+260971234567',
            'pickup' => 'Johannesburg',
            'delivery' => 'Lusaka',
            'weight' => 100,
            'consent' => '1',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('quote_requests', [
            'email' => 'customer@example.com',
            'status' => 'new',
            'pickup' => 'Johannesburg',
        ]);
    }
}
