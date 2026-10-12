<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        $authorId = DB::table('users')->where('is_admin', true)->value('id');
        if (!$authorId) return;

        $articles = [
            [
                'title' => 'How to ship goods to Zambia: a practical freight guide',
                'slug' => 'how-to-ship-goods-to-zambia',
                'category' => 'Shipping guides',
                'content' => '<h2>Start with the route and the cargo</h2><p>The right freight option depends on where the goods are collected, where they are delivered, the cargo type, its weight and dimensions, and how quickly it is needed. Share these details with Forus Freight so the team can recommend a practical route.</p><h2>Choose the right freight method</h2><ul><li><strong>Road freight:</strong> useful for cross-border cargo between Zambia, South Africa, and the wider SADC region.</li><li><strong>Air freight:</strong> suitable for urgent, small, high-value, or time-sensitive goods.</li><li><strong>Sea freight:</strong> useful for larger volumes from China or the UAE when cost matters more than speed.</li></ul><h2>Prepare the important details</h2><p>Have the pickup and delivery locations, cargo description, number of packages, weight, dimensions, value, and any deadline ready. Clear information helps the team prepare a more accurate quote.</p><p><a href="/quote">Request a freight quote</a> or <a href="/contact">contact the Forus Freight team</a> if you are unsure which service fits.</p>',
            ],
            [
                'title' => 'Road, air, or sea freight: which option should you choose?',
                'slug' => 'road-air-or-sea-freight',
                'category' => 'Shipping guides',
                'content' => '<h2>Compare the trade-offs</h2><p>Freight decisions usually balance speed, cost, cargo size, and handling requirements. There is no single best method for every shipment.</p><h3>Road freight</h3><p>Road transport is a practical choice for regional movements where flexible collection and delivery matter. It can suit full loads and shared space.</p><h3>Air freight</h3><p>Air freight is designed for urgency. It can work well for replacement parts, samples, small consignments, and high-value goods.</p><h3>Sea freight</h3><p>Sea freight is often considered for larger or heavier imports, especially from China and the UAE. Transit planning should include port and inland delivery steps.</p><h2>What to send for a recommendation</h2><p>Send the origin, destination, cargo description, weight, dimensions, value, and required delivery date. Forus Freight can then compare the available route and service options.</p><p><a href="/quote">Get a route-specific quote</a>.</p>',
            ],
            [
                'title' => 'Customs clearance in Zambia: what importers should prepare',
                'slug' => 'customs-clearance-zambia-importers-guide',
                'category' => 'Customs',
                'content' => '<h2>Why preparation matters</h2><p>Customs clearance depends on accurate shipment information and supporting documents. Missing or inconsistent details can create questions, delays, and additional handling.</p><h2>Useful information to prepare</h2><ul><li>Commercial invoice and packing information</li><li>Clear description of the goods and their intended use</li><li>Quantity, weight, value, and country of origin</li><li>Importer and consignee details</li><li>Any permits or certificates required for the cargo</li></ul><h2>Ask before the shipment moves</h2><p>Tell the freight team about the cargo before dispatch. This gives the team an opportunity to identify documentation requirements and explain the next steps.</p><p><a href="/quote?service=customs">Ask about customs clearance</a> for your shipment.</p>',
            ],
            [
                'title' => 'How to track a Forus Freight shipment',
                'slug' => 'how-to-track-forus-freight-shipment',
                'category' => 'Tracking',
                'content' => '<h2>Use your tracking number</h2><p>Open the Forus Freight tracking page and enter the tracking number provided for your shipment. The shipment timeline shows its current stage and the latest recorded update.</p><h2>Understand the shipment stages</h2><p>Shipments move through a defined sequence from creation and receipt to departure, arrival, customs, collection or delivery. The timeline helps you see what has already happened and what comes next.</p><h2>Need help with an update?</h2><p>If the information does not answer your question, contact the Forus Freight team with your tracking number. This lets the team find the correct shipment quickly.</p><p><a href="/tracking">Track a shipment</a> or <a href="/contact">contact support</a>.</p>',
            ],
            [
                'title' => 'How the Forus Freight China shopping address works',
                'slug' => 'forus-freight-china-shopping-address',
                'category' => 'China shopping',
                'content' => '<h2>Use your Forus Freight address at checkout</h2><p>Customers shopping from Chinese stores can use their assigned Forus Freight warehouse address when placing an order. The store sends the parcel to the warehouse for receiving and consolidation.</p><h2>What happens after delivery</h2><ol><li>The warehouse receives and records your parcel.</li><li>Your parcels can be combined where the service allows.</li><li>The cargo is prepared for shipment to Zambia.</li><li>You receive updates while the shipment moves toward Lusaka.</li></ol><p>Keep your order details and parcel references available so the warehouse team can match the delivery correctly.</p><p><a href="/register">Create an account</a> or <a href="/contact">ask the team how to get started</a>.</p>',
            ],
        ];

        foreach ($articles as $article) {
            DB::table('knowledge_base_articles')->updateOrInsert(
                ['slug' => $article['slug']],
                array_merge($article, ['status' => 'published', 'is_internal' => false, 'author_id' => $authorId, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }

    public function down(): void
    {
        DB::table('knowledge_base_articles')->whereIn('slug', [
            'how-to-ship-goods-to-zambia',
            'road-air-or-sea-freight',
            'customs-clearance-zambia-importers-guide',
            'how-to-track-forus-freight-shipment',
            'forus-freight-china-shopping-address',
        ])->delete();
    }
};
