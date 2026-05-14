<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Shipment;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'client@forusfl.co.zm')->first();
        if (!$user) return;

        $shipments = Shipment::where('user_id', $user->id)->get();

        foreach ($shipments as $index => $shipment) {
            Invoice::create([
                'user_id' => $user->id,
                'shipment_id' => $shipment->id,
                'invoice_number' => '2023' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'amount' => $shipment->cost > 0 ? $shipment->cost : rand(5000, 15000),
                'currency' => 'ZMW',
                'status' => $index % 2 === 0 ? 'paid' : 'pending',
                'due_date' => now()->addDays(rand(5, 20)),
                'paid_at' => $index % 2 === 0 ? now()->subDays(rand(1, 5)) : null,
            ]);
        }
    }
}
