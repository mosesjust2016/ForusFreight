<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_open_another_clients_shipment_portal_page(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        Shipment::create([
            'user_id' => $owner->id,
            'serial_no' => 'ZMFFL-PRIVATE001',
            'status' => 'CREATED',
            'origin' => 'Guangzhou',
            'destination' => 'Lusaka',
        ]);

        $this->actingAs($visitor)
            ->get(route('tracking.show', 'ZMFFL-PRIVATE001'))
            ->assertRedirect(route('client.shipments'))
            ->assertSessionHas('error');
    }

    public function test_client_cannot_view_another_clients_invoice(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $owner->id,
            'invoice_number' => 'INV-PRIVATE001',
            'amount' => 100,
            'currency' => 'ZMW',
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);

        $this->actingAs($visitor)
            ->get(route('client.invoices.show', $invoice))
            ->assertNotFound();
    }
}
