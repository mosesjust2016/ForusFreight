<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Pages\Auth\ActivateAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Route::get('activate-account', ActivateAccount::class)
        ->name('activation.phone');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::post('logout', function (Request $request) {
    // The dashboard layout's logout form says which guard's session its
    // Logout link belongs to (admin vs client), since a browser can hold
    // both at once and logging out of one shouldn't end the other. Fall
    // back to logging out of both only if that hint is ever missing.
    $guard = $request->input('guard');

    if ($guard === 'admin' || $guard === 'web') {
        Auth::guard($guard)->logout();

        // session()->invalidate() clears the ENTIRE session store, not just
        // this guard's key within it — calling it unconditionally would wipe
        // out the other guard's still-active login too. Only do the full
        // invalidate once nothing is left logged in; otherwise just rotate
        // the CSRF token.
        $otherGuard = $guard === 'admin' ? 'web' : 'admin';
        if (! Auth::guard($otherGuard)->check()) {
            $request->session()->invalidate();
        }
        $request->session()->regenerateToken();
    } else {
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect('/');
})->middleware('auth:web,admin')->name('logout');

Route::middleware('auth:web,admin')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('verify-phone', 'pages.auth.verify-phone')
        ->name('verification.phone');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
