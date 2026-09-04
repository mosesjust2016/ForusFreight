<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\CrmContactController;
use App\Http\Controllers\CrmSalesController;
use App\Http\Controllers\CrmMarketingController;
use App\Http\Controllers\CrmSupportController;
use App\Http\Controllers\CrmReportController;
use App\Http\Controllers\BulkCommunicationsController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\CmsAdminController;
use Livewire\Volt\Volt;

// Admin auth routes (must be before the admin prefix group)
Volt::route('admin/login', 'pages.admin.login')
    ->name('admin.login')->middleware('guest');

Volt::route('admin/forgot-password', 'pages.admin.forgot-password')
    ->name('admin.password.request')->middleware('guest');

Volt::route('admin/reset-password/{token}', 'pages.admin.reset-password')
    ->name('admin.password.reset')->middleware('guest');

// Public pages
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/services', fn() => view('services'))->name('services');

// Quote routes
Route::get('/quote', fn() => view('quote'))->name('quote');
Route::post('/quote/submit', [QuoteController::class, 'submit'])->name('quote.submit');

// Contact route
Route::get('/contact', fn() => view('contact'))->name('contact');

// Legal
Route::get('/terms', fn() => view('terms'))->name('terms');

// Tracking routes
Route::get('/track', [TrackingController::class, 'show'])->name('track');
Route::get('/tracking', [TrackingController::class, 'show'])->name('tracking');
Route::post('/track/check', [TrackingController::class, 'check'])->name('track.check');

// Public Knowledge Base
Route::get('/kb', [PublicContentController::class, 'knowledgeBaseHome'])->name('public.kb.index');
Route::get('/kb/{slug}', [PublicContentController::class, 'knowledgeBaseArticle'])->name('public.kb.article');

// Public Landing Pages
Route::get('/lp/{slug}', [PublicContentController::class, 'landingPage'])->name('public.lp.page');
Route::post('/lp/{slug}/submit', [PublicContentController::class, 'landingPageSubmit'])->name('public.lp.submit');

// Public exchange rate API
Route::get('/api/exchange-rate/current', [ExchangeRateController::class, 'currentRate'])->name('api.exchange-rate.current');

// Guest shipment creation (no auth required - creates temporary account)
Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create.guest');
Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store.guest');

// Protected routes (require authentication + full verification)
Route::middleware(['auth', 'fully_verified'])->group(function () {
    // Dashboard for logged in users
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Individual tracking dashboard
    Route::get('/tracking/{serial_no}', [TrackingController::class, 'showTracking'])
        ->name('tracking.show');
    
    // Client routes
    Route::get('/client/shipments', [ShipmentController::class, 'index'])->name('client.shipments');
    Route::get('/client/shipments/create', [ShipmentController::class, 'create'])->name('client.shipments.create');
    Route::post('/client/shipments', [ShipmentController::class, 'store'])->name('client.shipments.store');
    Route::get('/client/warehouse-cargo', [\App\Http\Controllers\ShipmentController::class, 'warehouseCargo'])->name('client.warehouse.cargo');
    Route::get('/client/invoices', [ShipmentController::class, 'invoices'])->name('client.invoices');
    Route::get('/client/tracking/auto', [\App\Http\Controllers\DashboardController::class, 'index'])->name('client.tracking.auto');
    
    Route::get('/client/settings', [ProfileController::class, 'settings'])->name('client.settings');
    Route::get('/client/security', [ProfileController::class, 'security'])->name('client.security');
    Route::put('/client/security/password', [ProfileController::class, 'updatePassword'])->name('client.password.update');
    Route::get('/client/help', [ProfileController::class, 'help'])->name('client.help');
    Route::get('/client/profile', [ProfileController::class, 'index'])->name('client.profile');
    Route::put('/client/profile', [ProfileController::class, 'update'])->name('client.profile.update');
});

// Admin routes (require authentication, full verification, and at least a staff role)
Route::middleware(['auth', 'fully_verified', 'role:admin_staff,sales'])->prefix('admin')->group(function () {
    // Dashboard — accessible to all staff roles
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Shipments — admin_staff and super-admin only
    Route::middleware('permission:admin.shipments.manage')->group(function () {
        Route::get('/shipments/create', [AdminController::class, 'createShipment'])->name('admin.shipments.create');
        Route::post('/shipments', [AdminController::class, 'storeShipment'])->name('admin.shipments.store');
    });
    Route::middleware('permission:admin.shipments.view')->group(function () {
        Route::get('/shipments', [AdminController::class, 'shipments'])->name('admin.shipments');
        Route::get('/shipments/{shipment}/edit', [AdminController::class, 'editShipment'])->name('admin.shipments.edit');
        Route::put('/shipments/{shipment}', [AdminController::class, 'updateShipment'])->name('admin.shipments.update');
        Route::post('/shipments/{shipment}/images/remove', [AdminController::class, 'removeShipmentImage'])->name('admin.shipments.images.remove');
        Route::post('/shipments/{shipment}/events', [AdminController::class, 'editShipment'])->name('admin.shipments.events.store');
        Route::delete('/shipments/{shipment}/events/{event}', fn() => back())->name('admin.shipments.events.destroy');
    });

    // Bulk Shipment Import
    Route::middleware('permission:admin.shipments.manage')->group(function () {
        Route::get('/shipments/bulk/import', [\App\Http\Controllers\BulkShipmentImportController::class, 'index'])->name('admin.shipments.bulk.index');
        Route::post('/shipments/bulk/import', [\App\Http\Controllers\BulkShipmentImportController::class, 'import'])->name('admin.shipments.bulk.import');
        Route::get('/shipments/bulk/template', [\App\Http\Controllers\BulkShipmentImportController::class, 'downloadTemplate'])->name('admin.shipments.bulk.template');
    });

    // Warehouse Cargo Import (integrated with shipments)
    Route::middleware('permission:admin.shipments.manage')->group(function () {
        Route::get('/shipments/warehouse/import', [\App\Http\Controllers\WarehouseCargoImportController::class, 'index'])->name('admin.warehouse.cargo.index');
        Route::post('/shipments/warehouse/import', [\App\Http\Controllers\WarehouseCargoImportController::class, 'import'])->name('admin.warehouse.cargo.import');
        Route::get('/shipments/warehouse/template', [\App\Http\Controllers\WarehouseCargoImportController::class, 'downloadTemplate'])->name('admin.warehouse.cargo.template');
    });

    // Clients — admin_staff and super-admin only
    // Note: specific routes (/create, /send-message) come before wildcards (/{user}) to avoid capture
    Route::middleware('permission:admin.clients.manage')->group(function () {
        Route::get('/clients/create', [AdminController::class, 'createClient'])->name('admin.clients.create');
        Route::post('/clients', [AdminController::class, 'clients'])->name('admin.clients.store');
    });
    Route::middleware('permission:admin.clients.view')->group(function () {
        Route::get('/clients', [AdminController::class, 'clients'])->name('admin.clients');
        Route::post('/clients/send-message', [AdminController::class, 'clients'])->name('admin.clients.send-message');
        Route::get('/clients/{user}', [AdminController::class, 'showClient'])->name('admin.clients.show');
    });
    Route::middleware('permission:admin.clients.manage')->group(function () {
        Route::get('/clients/{user}/edit', [AdminController::class, 'editClient'])->name('admin.clients.edit');
        Route::put('/clients/{user}', [AdminController::class, 'editClient'])->name('admin.clients.update');
    });

    // System Reports — admin_staff and super-admin only
    Route::middleware('permission:admin.reports.view')->group(function () {
        Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        Route::get('/reports/export', [AdminController::class, 'reports'])->name('admin.reports.export');
    });

    // Exchange Rates — admin_staff and super-admin only
    Route::middleware('permission:admin.exchange_rates.manage')->group(function () {
        Route::get('/exchange-rates', [ExchangeRateController::class, 'index'])->name('admin.exchange-rates');
        Route::post('/exchange-rates/sync', [ExchangeRateController::class, 'sync'])->name('admin.exchange-rates.sync');
        Route::get('/exchange-rates/hedge/create', [ExchangeRateController::class, 'createHedge'])->name('admin.exchange-rates.hedge.create');
        Route::post('/exchange-rates/hedge', [ExchangeRateController::class, 'storeHedge'])->name('admin.exchange-rates.hedge.store');
        Route::post('/exchange-rates/hedge/{hedge}/cancel', [ExchangeRateController::class, 'cancelHedge'])->name('admin.exchange-rates.hedge.cancel');
    });

    // User & Role Management — super-admin only
    Route::middleware('permission:admin.users.manage')->group(function () {
        Route::get('/staff', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('admin.staff.index');
        Route::post('/staff/{user}/roles', [\App\Http\Controllers\UserManagementController::class, 'assignRole'])->name('admin.staff.roles.assign');
        Route::delete('/staff/{user}/roles/{role}', [\App\Http\Controllers\UserManagementController::class, 'removeRole'])->name('admin.staff.roles.remove');

        // Phone country whitelist
        Route::get('/settings/phone-countries', [\App\Http\Controllers\PhoneCountryController::class, 'index'])->name('admin.settings.phone-countries');
        Route::post('/settings/phone-countries', [\App\Http\Controllers\PhoneCountryController::class, 'store'])->name('admin.settings.phone-countries.store');
        Route::post('/settings/phone-countries/{phoneCountry}/toggle', [\App\Http\Controllers\PhoneCountryController::class, 'toggle'])->name('admin.settings.phone-countries.toggle');
        Route::delete('/settings/phone-countries/{phoneCountry}', [\App\Http\Controllers\PhoneCountryController::class, 'destroy'])->name('admin.settings.phone-countries.destroy');
    });

    // ─────────── CRM ───────────
    // Contact Management
    Route::middleware('permission:crm.companies.view,crm.contacts.view')->group(function () {
        Route::get('/crm/companies', [CrmContactController::class, 'companies'])->name('admin.crm.companies');
        Route::get('/crm/contacts/{user}/360', [CrmContactController::class, 'contact360'])->name('admin.crm.contacts.360');
    });
    Route::middleware('permission:crm.companies.manage')->group(function () {
        Route::get('/crm/companies/create', [CrmContactController::class, 'createCompany'])->name('admin.crm.companies.create');
        Route::post('/crm/companies', [CrmContactController::class, 'storeCompany'])->name('admin.crm.companies.store');
        Route::get('/crm/companies/{company}/edit', [CrmContactController::class, 'editCompany'])->name('admin.crm.companies.edit');
        Route::put('/crm/companies/{company}', [CrmContactController::class, 'updateCompany'])->name('admin.crm.companies.update');
        Route::post('/crm/companies/{company}/link-contact', [CrmContactController::class, 'linkContact'])->name('admin.crm.companies.link-contact');
        Route::delete('/crm/companies/{company}/unlink-contact/{user}', [CrmContactController::class, 'unlinkContact'])->name('admin.crm.companies.unlink-contact');
    });
    Route::middleware('permission:crm.companies.view,crm.contacts.view')->group(function () {
        Route::get('/crm/companies/{company}', [CrmContactController::class, 'showCompany'])->name('admin.crm.companies.show');
    });
    Route::middleware('permission:crm.contacts.manage')->group(function () {
        Route::post('/crm/contacts/{user}/notes', [CrmContactController::class, 'storeNote'])->name('admin.crm.contacts.notes.store');
    });

    // Sales Automation & Pipeline
    Route::middleware('permission:crm.pipeline.view')->group(function () {
        Route::get('/crm/pipeline', [CrmSalesController::class, 'pipeline'])->name('admin.crm.pipeline');
        Route::get('/crm/stages', [CrmSalesController::class, 'stages'])->name('admin.crm.stages');
    });
    Route::middleware('permission:crm.deals.manage')->group(function () {
        Route::get('/crm/deals/create', [CrmSalesController::class, 'createDeal'])->name('admin.crm.deals.create');
        Route::post('/crm/deals', [CrmSalesController::class, 'storeDeal'])->name('admin.crm.deals.store');
        Route::put('/crm/deals/{deal}/stage', [CrmSalesController::class, 'updateDealStage'])->name('admin.crm.deals.stage');
    });
    Route::middleware('permission:crm.pipeline.view')->group(function () {
        Route::get('/crm/deals/{deal}', [CrmSalesController::class, 'showDeal'])->name('admin.crm.deals.show');
    });
    Route::middleware('permission:crm.tasks.manage')->group(function () {
        Route::get('/crm/tasks', [CrmSalesController::class, 'tasks'])->name('admin.crm.tasks');
        Route::post('/crm/tasks', [CrmSalesController::class, 'storeTask'])->name('admin.crm.tasks.store');
        Route::put('/crm/tasks/{task}/complete', [CrmSalesController::class, 'completeTask'])->name('admin.crm.tasks.complete');
    });
    Route::middleware('permission:crm.documents.manage')->group(function () {
        Route::get('/crm/documents', [CrmSalesController::class, 'documents'])->name('admin.crm.documents');
        Route::post('/crm/documents', [CrmSalesController::class, 'storeDocument'])->name('admin.crm.documents.store');
        Route::put('/crm/documents/{document}/send', [CrmSalesController::class, 'sendDocument'])->name('admin.crm.documents.send');
    });
    Route::middleware('permission:crm.forecast.view')->group(function () {
        Route::get('/crm/forecast', [CrmSalesController::class, 'forecast'])->name('admin.crm.forecast');
    });
    Route::middleware('permission:crm.leads.manage')->group(function () {
        Route::get('/crm/leads', [CrmSalesController::class, 'leads'])->name('admin.crm.leads');
        Route::put('/crm/leads/{user}/assign', [CrmSalesController::class, 'assignLead'])->name('admin.crm.leads.assign');
    });
    Route::middleware('permission:crm.stages.manage')->group(function () {
        Route::post('/crm/stages', [CrmSalesController::class, 'storeStage'])->name('admin.crm.stages.store');
    });

    // Marketing Automation
    Route::middleware('permission:crm.campaigns.manage')->group(function () {
        Route::get('/crm/campaigns', [CrmMarketingController::class, 'campaigns'])->name('admin.crm.campaigns');
        Route::post('/crm/campaigns', [CrmMarketingController::class, 'storeCampaign'])->name('admin.crm.campaigns.store');
        Route::put('/crm/campaigns/{campaign}', [CrmMarketingController::class, 'updateCampaign'])->name('admin.crm.campaigns.update');
    });
    Route::middleware('permission:crm.landing_pages.manage')->group(function () {
        Route::get('/crm/landing-pages', [CrmMarketingController::class, 'landingPages'])->name('admin.crm.landing-pages');
        Route::post('/crm/landing-pages', [CrmMarketingController::class, 'storeLandingPage'])->name('admin.crm.landing-pages.store');
        Route::put('/crm/landing-pages/{page}/status', [CrmMarketingController::class, 'updateLandingPageStatus'])->name('admin.crm.landing-pages.status');
    });

    // Bulk Communications (SMS & WhatsApp)
    Route::middleware('permission:crm.communications.manage')->group(function () {
        Route::get('/crm/communications/sms', [BulkCommunicationsController::class, 'sms'])->name('admin.crm.communications.sms');
        Route::post('/crm/communications/sms/send', [BulkCommunicationsController::class, 'sendSms'])->name('admin.crm.communications.sms.send');
        Route::get('/crm/communications/download-sample-csv', [BulkCommunicationsController::class, 'downloadSampleCsv'])->name('admin.crm.communications.download-sample-csv');
        Route::get('/crm/communications/export-contacts-csv', [BulkCommunicationsController::class, 'exportContactsCsv'])->name('admin.crm.communications.export-contacts-csv');

        // WhatsApp
        Route::get('/crm/communications/whatsapp', [BulkCommunicationsController::class, 'whatsapp'])->name('admin.crm.communications.whatsapp');
        Route::post('/crm/communications/whatsapp/send', [BulkCommunicationsController::class, 'sendWhatsapp'])->name('admin.crm.communications.whatsapp.send');
        Route::post('/crm/communications/whatsapp/upload-recipients', [BulkCommunicationsController::class, 'uploadWhatsappRecipients'])->name('admin.crm.communications.whatsapp.upload-recipients');
        Route::get('/crm/communications/whatsapp/qr', [BulkCommunicationsController::class, 'whatsappQr'])->name('admin.crm.communications.whatsapp.qr');
        Route::get('/crm/communications/whatsapp/status', [BulkCommunicationsController::class, 'whatsappStatus'])->name('admin.crm.communications.whatsapp.status');
        Route::get('/crm/communications/whatsapp/receive', [BulkCommunicationsController::class, 'whatsappReceive'])->name('admin.crm.communications.whatsapp.receive');
        Route::post('/crm/communications/whatsapp/clear-queue', [BulkCommunicationsController::class, 'whatsappClearQueue'])->name('admin.crm.communications.whatsapp.clear-queue');

        // Campaign management
        Route::get('/crm/communications/whatsapp/campaigns/{campaign}', [BulkCommunicationsController::class, 'showCampaign'])->name('admin.crm.communications.whatsapp.campaigns.show');
        Route::get('/crm/communications/whatsapp/compare', [BulkCommunicationsController::class, 'compareCampaigns'])->name('admin.crm.communications.whatsapp.compare');
        Route::post('/crm/communications/whatsapp/campaigns/{campaign}/pause', [BulkCommunicationsController::class, 'pauseCampaign'])->name('admin.crm.communications.whatsapp.campaigns.pause');
        Route::post('/crm/communications/whatsapp/campaigns/{campaign}/resume', [BulkCommunicationsController::class, 'resumeCampaign'])->name('admin.crm.communications.whatsapp.campaigns.resume');
        Route::post('/crm/communications/whatsapp/campaigns/{campaign}/cancel', [BulkCommunicationsController::class, 'cancelCampaign'])->name('admin.crm.communications.whatsapp.campaigns.cancel');

        // Templates
        Route::post('/crm/communications/templates', [BulkCommunicationsController::class, 'storeTemplate'])->name('admin.crm.communications.templates.store');
        Route::put('/crm/communications/templates/{template}', [BulkCommunicationsController::class, 'updateTemplate'])->name('admin.crm.communications.templates.update');
        Route::delete('/crm/communications/templates/{template}', [BulkCommunicationsController::class, 'destroyTemplate'])->name('admin.crm.communications.templates.destroy');
    });

    // Customer Service & Support
    Route::middleware('permission:crm.tickets.manage')->group(function () {
        Route::get('/crm/tickets', [CrmSupportController::class, 'tickets'])->name('admin.crm.tickets');
        Route::post('/crm/tickets', [CrmSupportController::class, 'storeTicket'])->name('admin.crm.tickets.store');
        Route::get('/crm/tickets/{ticket}', [CrmSupportController::class, 'showTicket'])->name('admin.crm.tickets.show');
        Route::post('/crm/tickets/{ticket}/reply', [CrmSupportController::class, 'replyTicket'])->name('admin.crm.tickets.reply');
        Route::put('/crm/tickets/{ticket}/status', [CrmSupportController::class, 'updateTicketStatus'])->name('admin.crm.tickets.status');
        Route::put('/crm/tickets/{ticket}/assign', [CrmSupportController::class, 'assignTicket'])->name('admin.crm.tickets.assign');
    });
    Route::middleware('permission:crm.knowledge_base.manage')->group(function () {
        Route::get('/crm/knowledge-base', [CrmSupportController::class, 'knowledgeBase'])->name('admin.crm.knowledge-base');
        Route::post('/crm/knowledge-base', [CrmSupportController::class, 'storeArticle'])->name('admin.crm.knowledge-base.store');
        Route::put('/crm/knowledge-base/{article}', [CrmSupportController::class, 'updateArticle'])->name('admin.crm.knowledge-base.update');
    });

    // Reporting & Analytics
    Route::middleware('permission:crm.reports.view')->group(function () {
        Route::get('/crm/reports', [CrmReportController::class, 'dashboard'])->name('admin.crm.reports');
        Route::get('/crm/analytics', [CrmReportController::class, 'analytics'])->name('admin.crm.analytics');
    });

    // Website CMS — admin_staff and super-admin only
    Route::middleware('permission:admin.cms.manage')->group(function () {
        Route::get('/cms/pages', [CmsAdminController::class, 'index'])->name('admin.cms.pages.index');
        Route::get('/cms/pages/create', [CmsAdminController::class, 'create'])->name('admin.cms.pages.create');
        Route::post('/cms/pages', [CmsAdminController::class, 'store'])->name('admin.cms.pages.store');
        Route::post('/cms/upload', [CmsAdminController::class, 'uploadImage'])->name('admin.cms.upload');
        Route::get('/cms/pages/{page}/edit', [CmsAdminController::class, 'edit'])->name('admin.cms.pages.edit');
        Route::put('/cms/pages/{page}', [CmsAdminController::class, 'update'])->name('admin.cms.pages.update');
        Route::delete('/cms/pages/{page}', [CmsAdminController::class, 'destroy'])->name('admin.cms.pages.destroy');
    });

    // Account & Settings — accessible to all staff
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::get('/security', [ProfileController::class, 'security'])->name('admin.security');
    Route::put('/security/password', [ProfileController::class, 'updatePassword'])->name('admin.password.update');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('admin.settings');
    Route::get('/help', [ProfileController::class, 'help'])->name('admin.help');
});

// Auth routes
require __DIR__.'/auth.php';