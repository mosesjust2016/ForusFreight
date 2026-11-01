<?php

namespace Tests\Feature;

use Tests\TestCase;

class QuoteEstimateTest extends TestCase
{
    public function test_quote_estimate_returns_an_indicative_range(): void
    {
        $this->postJson(route('quote.estimate'), [
            'service_type' => 'road-freight',
            'pickup' => 'Johannesburg',
            'delivery' => 'Lusaka',
            'weight' => 100,
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['min', 'max', 'transit_time', 'disclaimer']);
    }

    public function test_quote_estimate_rejects_missing_inputs(): void
    {
        $this->postJson(route('quote.estimate'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['service_type', 'pickup', 'delivery', 'weight']);
    }
}
