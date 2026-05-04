<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TrackingController;

// Public pages
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/services', fn() => view('services'))->name('services');

// Quote routes
Route::get('/quote', fn() => view('quote'))->name('quote');
Route::post('/quote/submit', [QuoteController::class, 'submit'])->name('quote.submit');

// Contact route
Route::get('/contact', fn() => view('contact'))->name('contact');

// Tracking routes
Route::get('/track', [TrackingController::class, 'show'])->name('track');
Route::get('/tracking', [TrackingController::class, 'show'])->name('tracking'); // Add this line
Route::post('/track/check', [TrackingController::class, 'check'])->name('track.check');

// Protected routes (require authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard for logged in users
    Route::get('/dashboard', [TrackingController::class, 'dashboard'])->name('dashboard');
    
    // Individual tracking dashboard
    Route::get('/tracking/{tracking_number}', [TrackingController::class, 'showTracking'])
        ->name('tracking.show');
});

// Auth routes
require __DIR__.'/auth.php';