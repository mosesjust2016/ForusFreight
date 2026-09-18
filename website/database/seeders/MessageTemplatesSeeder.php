<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MessageTemplate;

class MessageTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Lead Re-engagement (Question)',
                'channel' => 'whatsapp',
                'category' => 'follow_up',
                'body' => "Hi {name}, good afternoon! Do you want to receive our latest freight rates and shipping updates from Forus Freight?\n\nReply STOP to unsubscribe.",
            ],
            [
                'name' => 'Short & Personal Check-in',
                'channel' => 'whatsapp',
                'category' => 'follow_up',
                'body' => "Hi {name}! Forus Freight here. Quick question — are you still shipping between Zambia and South Africa? We'd love to help.\n\nReply STOP to unsubscribe.",
            ],
            [
                'name' => 'Monthly Promo Offer',
                'channel' => 'whatsapp',
                'category' => 'promo',
                'body' => "Hi {name}! Forus Freight is offering discounted cross-border shipping this month. Want to know more?\n\nReply YES for details or STOP to unsubscribe.",
            ],
            [
                'name' => 'Invoice Reminder',
                'channel' => 'both',
                'category' => 'invoice',
                'body' => "Hi {name}, this is a friendly reminder that your invoice with Forus Freight is due soon. Please let us know if you need any assistance with payment.\n\nReply STOP to unsubscribe.",
            ],
            [
                'name' => 'Customs Clearance Update',
                'channel' => 'whatsapp',
                'category' => 'custom_clearance',
                'body' => "Hi {name}, your shipment has reached customs. We're handling the clearance process. You'll receive an update within 24 hours.\n\nReply STOP to unsubscribe.",
            ],
            [
                'name' => 'Onboarding Welcome',
                'channel' => 'whatsapp',
                'category' => 'onboarding',
                'body' => "Welcome to Forus Freight, {name}! 🚛 We're excited to handle your logistics. Your dedicated agent will contact you shortly.\n\nReply STOP to unsubscribe.",
            ],
            [
                'name' => 'Event / Webinar Invite',
                'channel' => 'whatsapp',
                'category' => 'event',
                'body' => "Hi {name}! Forus Freight is hosting a free webinar on cross-border shipping best practices. Interested in joining?\n\nReply YES for the link or STOP to unsubscribe.",
            ],
            [
                'name' => 'Abandoned Cart / Quote Follow-up',
                'channel' => 'whatsapp',
                'category' => 'follow_up',
                'body' => "Hi {name}, you requested a freight quote from us recently. Is there anything else you need to finalize your shipment? We're here to help.\n\nReply STOP to unsubscribe.",
            ],
            [
                'name' => 'Feedback Request',
                'channel' => 'whatsapp',
                'category' => 'general',
                'body' => "Hi {name}, thanks for shipping with Forus Freight! How was your experience? Your feedback helps us improve.\n\nReply STOP to unsubscribe.",
            ],
            [
                'name' => 'SMS Quick Promo',
                'channel' => 'sms',
                'category' => 'promo',
                'body' => "Forus Freight: Special rates on Zambia-SA routes this week. Call us or reply for details. Reply STOP to opt out.",
            ],
        ];

        foreach ($templates as $t) {
            MessageTemplate::firstOrCreate(
                ['name' => $t['name'], 'channel' => $t['channel']],
                array_merge($t, ['is_active' => true, 'created_by' => null])
            );
        }
    }
}
