<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExchangeRateController;
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
Route::get('/tracking', [TrackingController::class, 'show'])->name('tracking'); // Add this line
Route::post('/track/check', [TrackingController::class, 'check'])->name('track.check');

// Public exchange rate API
Route::get('/api/exchange-rate/current', [ExchangeRateController::class, 'currentRate'])->name('api.exchange-rate.current');

// Protected routes (require authentication + full verification)
Route::middleware(['auth', 'fully_verified'])->group(function () {
    // Dashboard for logged in users
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Individual tracking dashboard
    Route::get('/tracking/{tracking_number}', [TrackingController::class, 'showTracking'])
        ->name('tracking.show');
    
    // Client routes
    Route::get('/client/shipments', [ShipmentController::class, 'index'])->name('client.shipments');
    Route::get('/client/shipments/create', [ShipmentController::class, 'create'])->name('client.shipments.create');
    Route::post('/client/shipments', [ShipmentController::class, 'store'])->name('client.shipments.store');
    Route::get('/client/invoices', [ShipmentController::class, 'invoices'])->name('client.invoices');
    Route::get('/client/tracking/auto', [\App\Http\Controllers\DashboardController::class, 'index'])->name('client.tracking.auto');
    
    Route::get('/client/settings', [ProfileController::class, 'settings'])->name('client.settings');
    Route::get('/client/security', [ProfileController::class, 'security'])->name('client.security');
    Route::put('/client/security/password', [ProfileController::class, 'updatePassword'])->name('client.password.update');
    Route::get('/client/help', [ProfileController::class, 'help'])->name('client.help');
    Route::get('/client/profile', [ProfileController::class, 'index'])->name('client.profile');
    Route::put('/client/profile', [ProfileController::class, 'update'])->name('client.profile.update');
});

// Admin routes (require authentication, full verification, and admin role)
Route::middleware(['auth', 'fully_verified'])->prefix('admin')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Shipments management
    Route::get('/shipments', [AdminController::class, 'shipments'])->name('admin.shipments');
    Route::get('/shipments/create', [AdminController::class, 'createShipment'])->name('admin.shipments.create');
    Route::post('/shipments', [AdminController::class, 'storeShipment'])->name('admin.shipments.store');
    Route::get('/shipments/{shipment}/edit', [AdminController::class, 'editShipment'])->name('admin.shipments.edit');
    Route::put('/shipments/{shipment}', [AdminController::class, 'updateShipment'])->name('admin.shipments.update');
    
    // Clients management
    Route::get('/clients', [AdminController::class, 'clients'])->name('admin.clients');
    Route::get('/clients/create', [AdminController::class, 'createClient'])->name('admin.clients.create');
    Route::post('/clients', [AdminController::class, 'clients'])->name('admin.clients.store');
    Route::get('/clients/{user}', [AdminController::class, 'showClient'])->name('admin.clients.show');
    Route::get('/clients/{user}/edit', [AdminController::class, 'editClient'])->name('admin.clients.edit');
    Route::put('/clients/{user}', [AdminController::class, 'editClient'])->name('admin.clients.update');
    Route::post('/clients/send-message', [AdminController::class, 'clients'])->name('admin.clients.send-message');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export', [AdminController::class, 'reports'])->name('admin.reports.export');

    // Exchange Rates & Hedging
    Route::get('/exchange-rates', [ExchangeRateController::class, 'index'])->name('admin.exchange-rates');
    Route::post('/exchange-rates/sync', [ExchangeRateController::class, 'sync'])->name('admin.exchange-rates.sync');
    Route::get('/exchange-rates/hedge/create', [ExchangeRateController::class, 'createHedge'])->name('admin.exchange-rates.hedge.create');
    Route::post('/exchange-rates/hedge', [ExchangeRateController::class, 'storeHedge'])->name('admin.exchange-rates.hedge.store');
    Route::post('/exchange-rates/hedge/{hedge}/cancel', [ExchangeRateController::class, 'cancelHedge'])->name('admin.exchange-rates.hedge.cancel');

    // Shipment tracking events
    Route::post('/shipments/{shipment}/events', [AdminController::class, 'editShipment'])->name('admin.shipments.events.store');
    Route::delete('/shipments/{shipment}/events/{event}', function () {
        return back();
    })->name('admin.shipments.events.destroy');

    // Account & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::get('/security', [ProfileController::class, 'security'])->name('admin.security');
    Route::put('/security/password', [ProfileController::class, 'updatePassword'])->name('admin.password.update');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('admin.settings');
    Route::get('/help', [ProfileController::class, 'help'])->name('admin.help');
});

// Auth routes
require __DIR__.'/auth.php';