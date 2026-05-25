<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'home',
                'title' => 'Forus Freight',
                'meta_description' => 'Cross-border logistics and freight services between Zambia and South Africa.',
                'meta_keywords' => 'freight, logistics, zambia, south africa, shipping, cargo',
                'status' => 'published',
                'sections' => [
                    'hero_title' => 'Forus Freight',
                    'hero_subtitle' => 'Cross-border logistics made simple. Reliable freight between Zambia and South Africa.',
                    'hero_cta_text' => 'Get a Quote',
                    'hero_cta_link' => '/quote',
                    'hero_image' => '',
                    'features' => [
                        ['icon' => 'fa-truck', 'title' => 'Fast Shipping', 'text' => 'Reliable cross-border transport with real-time tracking'],
                        ['icon' => 'fa-shield-halved', 'title' => 'Secure Cargo', 'text' => 'Full insurance coverage for all shipments'],
                        ['icon' => 'fa-globe', 'title' => 'Zambia & SA', 'text' => 'Expert regional logistics and customs clearance'],
                    ],
                    'stats' => [
                        ['number' => '500+', 'label' => 'Monthly Shipments'],
                        ['number' => '98%', 'label' => 'On-Time Delivery'],
                        ['number' => '24/7', 'label' => 'Support Available'],
                    ],
                    'about_title' => 'About Forus Freight',
                    'about_text' => 'We specialize in cross-border freight between Zambia and South Africa. With years of experience, we provide reliable, efficient, and cost-effective logistics solutions for businesses of all sizes.',
                    'about_image' => '',
                ],
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
                'meta_description' => 'Learn about Forus Freight, your trusted cross-border logistics partner.',
                'meta_keywords' => 'about forus freight, logistics company, zambia, south africa',
                'status' => 'published',
                'sections' => [
                    'title' => 'About Us',
                    'subtitle' => 'Your trusted logistics partner since 2015',
                    'content' => '<p>Forus Freight was founded with a mission to simplify cross-border logistics between Zambia and South Africa. Over the years, we have grown into a trusted partner for hundreds of businesses.</p><p>Our team of experienced professionals handles everything from documentation to delivery, ensuring your cargo reaches its destination safely and on time.</p>',
                    'mission' => 'To deliver reliable, efficient, and cost-effective logistics solutions that empower businesses to grow across borders.',
                    'vision' => 'To become the leading cross-border logistics provider in Southern Africa, recognized for excellence and innovation.',
                    'team_title' => 'Our Leadership Team',
                    'team' => [
                        ['name' => 'CEO', 'role' => 'Chief Executive Officer', 'image' => ''],
                        ['name' => 'COO', 'role' => 'Chief Operations Officer', 'image' => ''],
                    ],
                ],
            ],
            [
                'slug' => 'services',
                'title' => 'Our Services',
                'meta_description' => 'Comprehensive logistics services including road freight, warehousing, and customs clearance.',
                'meta_keywords' => 'freight services, warehousing, customs clearance, logistics',
                'status' => 'published',
                'sections' => [
                    'title' => 'Our Services',
                    'subtitle' => 'Comprehensive logistics solutions for your business',
                    'services' => [
                        ['icon' => 'fa-truck', 'title' => 'Road Freight', 'description' => 'Full and part load road transport between Zambia and South Africa with GPS tracking.'],
                        ['icon' => 'fa-warehouse', 'title' => 'Warehousing', 'description' => 'Secure storage facilities with inventory management and distribution.'],
                        ['icon' => 'fa-file-shield', 'title' => 'Customs Clearance', 'description' => 'Expert handling of all customs documentation and border procedures.'],
                        ['icon' => 'fa-boxes-packing', 'title' => 'Packaging', 'description' => 'Professional packing and crating services for fragile and oversized cargo.'],
                        ['icon' => 'fa-headset', 'title' => '24/7 Support', 'description' => 'Round-the-clock customer service and shipment tracking assistance.'],
                        ['icon' => 'fa-chart-line', 'title' => 'Supply Chain Consulting', 'description' => 'Optimize your logistics operations with our expert consultants.'],
                    ],
                ],
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'meta_description' => 'Get in touch with Forus Freight for quotes, support, and inquiries.',
                'meta_keywords' => 'contact forus freight, logistics support, get quote',
                'status' => 'published',
                'sections' => [
                    'title' => 'Contact Us',
                    'subtitle' => 'We would love to hear from you',
                    'address' => '123 Main Street, Lusaka, Zambia',
                    'phone' => '+260 97 123 4567',
                    'email' => 'info@forusfl.co.zm',
                    'hours' => 'Mon-Fri: 8:00 AM - 5:00 PM',
                    'map_embed' => '',
                    'form_title' => 'Send us a Message',
                ],
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'meta_description' => 'Forus Freight terms of service and shipping conditions.',
                'meta_keywords' => 'terms of service, shipping conditions, logistics terms',
                'status' => 'published',
                'sections' => [
                    'title' => 'Terms of Service',
                    'subtitle' => 'Last updated: January 2025',
                    'content' => '<h2>1. Acceptance of Terms</h2><p>By accessing and using Forus Freight services, you agree to be bound by these Terms of Service.</p><h2>2. Services Description</h2><p>Forus Freight provides cross-border logistics and freight services between Zambia and South Africa.</p><h2>3. Shipping and Delivery</h2><p>We strive to deliver all shipments within the estimated timeframes. Delivery times may vary based on customs clearance and other factors.</p><h2>4. Liability and Insurance</h2><p>We maintain full insurance coverage for all shipments. Claims must be filed within 7 days of delivery.</p><h2>5. Payment Terms</h2><p>Payment is due upon shipment unless credit terms have been arranged. We accept bank transfer and mobile money.</p><h2>6. Cancellation</h2><p>Cancellations must be made at least 24 hours before scheduled pickup to avoid cancellation fees.</p>',
                ],
            ],
        ];

        foreach ($pages as $page) {
            CmsPage::firstOrCreate(
                ['slug' => $page['slug']],
                array_merge($page, ['last_edited_by' => null])
            );
        }
    }
}
