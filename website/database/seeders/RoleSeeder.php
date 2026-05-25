<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => 'admin_staff',
                'display_name' => 'Admin Staff',
                'description'  => 'Full operational access — shipments, clients, CRM, CMS, exchange rates, reports. Cannot manage user roles.',
                'permissions'  => [
                    'admin.dashboard.view',
                    'admin.shipments.view',
                    'admin.shipments.manage',
                    'admin.clients.view',
                    'admin.clients.manage',
                    'admin.reports.view',
                    'admin.exchange_rates.manage',
                    'admin.cms.manage',
                    'admin.users.view',
                    'crm.contacts.view',
                    'crm.contacts.manage',
                    'crm.companies.view',
                    'crm.companies.manage',
                    'crm.pipeline.view',
                    'crm.deals.manage',
                    'crm.tasks.manage',
                    'crm.documents.manage',
                    'crm.forecast.view',
                    'crm.leads.manage',
                    'crm.stages.manage',
                    'crm.campaigns.manage',
                    'crm.landing_pages.manage',
                    'crm.communications.manage',
                    'crm.tickets.manage',
                    'crm.knowledge_base.manage',
                    'crm.reports.view',
                ],
            ],
            [
                'name'         => 'sales',
                'display_name' => 'Sales',
                'description'  => 'CRM-focused access — pipeline, deals, contacts, tasks, documents, tickets, KB, and reports. No access to shipment operations, marketing, or communications.',
                'permissions'  => [
                    'admin.dashboard.view',
                    'crm.contacts.view',
                    'crm.contacts.manage',
                    'crm.companies.view',
                    'crm.companies.manage',
                    'crm.pipeline.view',
                    'crm.deals.manage',
                    'crm.tasks.manage',
                    'crm.documents.manage',
                    'crm.forecast.view',
                    'crm.leads.manage',
                    'crm.tickets.manage',
                    'crm.knowledge_base.manage',
                    'crm.reports.view',
                ],
            ],
        ];

        foreach ($roles as $data) {
            Role::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}
