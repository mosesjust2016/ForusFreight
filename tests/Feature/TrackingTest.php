<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_track_by_tracking_number(): void
    {
        $user = User::factory()->create();
        Shipment::create([
            'user_id' => $user->id,
            'serial_no' => 'ZMFFL-TEST001',
            'tracking_number' => 'TRACK-TEST-001',
            'status' => 'CREATED',
            'origin' => 'Guangzhou',
            'destination' => 'Lusaka',
            'description' => 'Test cargo',
        ]);

        $response = $this->post(route('track.check'), ['tracking_number' => 'TRACK-TEST-001']);

        $response->assertOk()->assertSee('TRACK-TEST-001')->assertDontSee('Test cargo')->assertDontSee('Next action');
        $this->assertGuest();
    }

    public function test_guest_can_track_by_serial_number(): void
    {
        $user = User::factory()->create();
        Shipment::create([
            'user_id' => $user->id,
            'serial_no' => 'ZMFFL-TEST002',
            'status' => 'CREATED',
            'origin' => 'Durban',
            'destination' => 'Lusaka',
        ]);

        $this->post(route('track.check'), ['tracking_number' => 'ZMFFL-TEST002'])
            ->assertOk()
            ->assertSee('ZMFFL-TEST002');
    }

    public function test_unknown_tracking_number_returns_a_helpful_error(): void
    {
        $this->from(route('tracking'))
            ->post(route('track.check'), ['tracking_number' => 'DOES-NOT-EXIST'])
            ->assertRedirect(route('tracking'))
            ->assertSessionHas('error');
    }
}
