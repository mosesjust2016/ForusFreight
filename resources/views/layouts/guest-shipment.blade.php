<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Forus Freight'))</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f4f7f6 0%, #e8f5e9 100%);
            min-height: 100vh;
            color: #1e293b;
        }
        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .header-logo img {
            height: 48px;
            width: auto;
        }
        .header-logo span {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -1px;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .header-link {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .header-link:hover {
            background: #f1f5f9;
            color: #007f7f;
        }
        .btn-login {
            background: #007f7f;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: #006666;
            transform: translateY(-1px);
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .footer {
            text-align: center;
            padding: 2rem;
            color: #94a3b8;
            font-size: 0.85rem;
        }
        .footer a {
            color: #007f7f;
            text-decoration: none;
        }

        @keyframes flashSlideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .flash-message {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.1rem 1.25rem;
            border-radius: 16px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            animation: flashSlideIn 0.3s ease;
        }
        .flash-message > i:first-child { font-size: 1.15rem; margin-top: 0.1rem; }
        .flash-message > span { flex: 1; }
        .flash-dismiss {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
            font-size: 0.85rem;
            padding: 0.15rem;
            flex-shrink: 0;
        }
        .flash-dismiss:hover { opacity: 1; }
        .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .flash-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
    </style>
    @yield('styles')
    @livewireStyles
</head>
<body>
    <header class="header">
        <a href="{{ route('home') }}" class="header-logo">
            <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight">
            <span>Forus Freight</span>
        </a>
        <div class="header-actions">
            <a href="{{ route('about') }}" class="header-link">About</a>
            <a href="{{ route('services') }}" class="header-link">Services</a>
            <a href="{{ route('contact') }}" class="header-link">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-login">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-login">Sign In</a>
            @endauth
        </div>
    </header>

    <main class="main-content">
        {{-- This layout previously had zero flash/validation feedback of its own,
             so a redirect back with session('success')/('error') (e.g. from
             ShipmentController::store()) had nowhere to render. --}}
        <div id="globalFlashMessages">
            @if(session('success'))
                <div class="flash-message flash-success" role="status">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="flash-dismiss" onclick="this.closest('.flash-message').remove()" aria-label="Dismiss"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-message flash-error" role="alert">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="flash-dismiss" onclick="this.closest('.flash-message').remove()" aria-label="Dismiss"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('info'))
                <div class="flash-message flash-info" role="status">
                    <i class="fas fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                    <button type="button" class="flash-dismiss" onclick="this.closest('.flash-message').remove()" aria-label="Dismiss"><i class="fas fa-times"></i></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; 2026 Forus Freight Limited. All rights reserved.</p>
        <p style="margin-top: 0.5rem;">
            <a href="{{ route('terms') }}">Terms & Conditions</a>
        </p>
    </footer>

    @yield('scripts')
    <script>
        // Same pattern as layouts/dashboard.blade.php: disable + spinner any
        // plain form submit button so a slow request doesn't look like a dead
        // click. Livewire forms manage their own wire:loading state.
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (e.defaultPrevented) return;
            if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) return;
            if (form.dataset.noLoader !== undefined) return;

            const label = form.dataset.loadingLabel || 'Processing...';

            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
                if (btn.disabled) return;
                if (btn.tagName === 'BUTTON') {
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> ' + label;
                } else {
                    btn.dataset.originalValue = btn.value;
                    btn.value = label;
                }
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
            });
        });
    </script>
    @livewireScripts
</body>
</html>
