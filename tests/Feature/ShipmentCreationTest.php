<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_client_can_create_a_shipment_and_receives_an_initial_tracking_event(): void
    {
        $this->mock(BrevoMailService::class, function ($mock) {
            $mock->shouldReceive('sendShipmentCreated')->once()->andReturn(true);
        });

        $user = User::factory()->create(['phone' => '+260971234567']);

        $this->actingAs($user)->post(route('client.shipments.store'), [
            'client_name' => $user->name,
            'client_phone' => $user->phone,
            'origin_country' => 'China',
            'origin_city' => 'Guangzhou',
            'destination_country' => 'Zambia',
            'destination_city' => 'Lusaka',
            'service_type' => 'sea-freight',
            'shipping_method' => 'sea',
            'description' => 'Test cartons',
            'no_of_parcels' => 2,
        ])->assertRedirect(route('client.shipments'));

        $this->assertDatabaseHas('shipments', [
            'user_id' => $user->id,
            'description' => 'Test cartons',
            'status' => 'CREATED',
        ]);
        $this->assertDatabaseHas('tracking_events', [
            'description' => 'Shipment created and confirmed',
        ]);
    }

    public function test_shipment_creation_rejects_missing_required_cargo_details(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('client.shipments.store'), [])
            ->assertSessionHasErrors(['origin_city', 'destination_city', 'description', 'no_of_parcels']);
    }
}
