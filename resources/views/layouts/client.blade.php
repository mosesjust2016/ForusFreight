{{-- Client portal shell. Rendered by layouts/dashboard.blade.php for customer
     (non-staff) accounts, so every client page gets this simpler navigation
     while the staff admin panel keeps its own layout. --}}
@php
    $portalUser = Auth::user();
    $firstName = explode(' ', trim($portalUser->name))[0] ?: 'there';
    $initials = collect(preg_split('/\s+/', trim($portalUser->name)))->filter()->take(2)
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: 'FF';

    $nav = [
        ['label' => 'Home', 'icon' => 'fa-house', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard', 'client.tracking.auto')],
        ['label' => 'Shipments', 'icon' => 'fa-box', 'route' => 'client.shipments', 'active' => request()->routeIs('client.shipments', 'tracking.show', 'client.warehouse.cargo')],
        // Invoices live under Account (and anything due shows on Home) to keep the menu short
        ['label' => 'Account', 'icon' => 'fa-user', 'route' => 'client.profile', 'active' => request()->routeIs('client.profile*', 'client.security', 'client.settings', 'client.invoices*', 'client.payments*', 'client.help')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', config('app.name', 'Forus Freight'))</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Archivo:wdth,wght@100..125,500..800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @include('partials.ui-base-styles')
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body.portal {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 3px solid var(--orange);
            outline-offset: 2px;
        }

        .portal-shell {
            display: grid;
            grid-template-columns: 248px minmax(0, 1fr);
            min-height: 100vh;
        }

        /* ---------- Sidebar ---------- */
        .p-side {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 1.5rem 1rem 1.25rem;
            background: var(--ink);
            color: #c4d1d5;
        }

        .p-logo {
            display: inline-flex;
            align-self: flex-start;
            background: #fff;
            border-radius: 12px;
            padding: 0.45rem 0.6rem;
            margin-left: 0.4rem;
        }

        .p-logo img {
            height: 46px;
            width: auto;
            display: block;
        }

        .p-new {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 46px;
            border-radius: 10px;
            background: var(--orange);
            color: var(--ink);
            font-weight: 800;
            font-size: 0.95rem;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }

        .p-new:hover { background: var(--orange-hover); }

        .p-new.is-active { box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.45); }

        .p-nav {
            list-style: none;
            display: grid;
            gap: 0.2rem;
        }

        .p-nav a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-height: 44px;
            padding: 0 0.85rem;
            border-radius: 10px;
            color: #c4d1d5;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .p-nav a i {
            width: 1.1rem;
            text-align: center;
            color: #7f979e;
        }

        .p-nav a:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .p-nav a.is-active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .p-nav a.is-active i { color: var(--orange); }

        .p-side-foot {
            margin-top: auto;
            display: grid;
            gap: 0.15rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .p-side-foot a,
        .p-side-foot button {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            min-height: 40px;
            padding: 0 0.85rem;
            border: none;
            border-radius: 8px;
            background: none;
            color: #a9bcc2;
            font: inherit;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            text-align: left;
        }

        .p-side-foot a:hover,
        .p-side-foot button:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .p-side-foot i { width: 1.1rem; text-align: center; }

        .p-user {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.75rem 0.85rem 0.25rem;
        }

        .p-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #1d4d55;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
        }

        .p-user strong {
            display: block;
            color: #fff;
            font-size: 0.88rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .p-user small {
            display: block;
            font-size: 0.75rem;
            color: #8fa6ad;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        /* ---------- Main ---------- */
        .p-main {
            min-width: 0;
            padding: 2.25rem 2.5rem 4rem;
        }

        .p-content {
            max-width: 1120px;
            margin: 0 auto;
        }

        /* Mobile top bar + bottom tabs */
        .p-topbar,
        .p-tabs {
            display: none;
        }

        /* Flash messages */
        .p-flash {
            display: grid;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
        }

        .p-flash-msg {
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-size: 0.92rem;
            font-weight: 500;
        }

        .p-flash-msg ul { margin: 0.3rem 0 0 1.1rem; }
        .p-flash-msg span { flex: 1; }
        .p-flash-msg i { margin-top: 0.15rem; }

        .p-flash-ok { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .p-flash-err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .p-flash-info { background: #ecfeff; border: 1px solid #a5f3fc; color: #155e75; }

        .p-flash-close {
            border: none;
            background: none;
            color: inherit;
            opacity: 0.6;
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 6px;
        }

        .p-flash-close:hover { opacity: 1; }

        /* Classes older portal pages still use */
        .welcome-section { margin-bottom: 2.5rem; }

        .btn-create {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-height: 46px;
            padding: 0 1.25rem;
            border: none;
            border-radius: 10px;
            background: var(--orange);
            color: var(--ink);
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-create:hover { background: var(--orange-hover); }

        @media (max-width: 1023px) {
            .portal-shell { grid-template-columns: 1fr; }

            .p-side { display: none; }

            .p-topbar {
                position: sticky;
                top: 0;
                z-index: 40;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.6rem 1rem;
                background: rgba(245, 244, 239, 0.94);
                backdrop-filter: blur(8px);
                border-bottom: 1px solid var(--line);
            }

            .p-topbar img { height: 40px; width: auto; display: block; }

            .p-topbar-actions { display: flex; align-items: center; gap: 0.5rem; }

            .p-icon-btn {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--line-strong);
                background: #fff;
                color: var(--ink);
                text-decoration: none;
            }

            .p-main { padding: 1.5rem 1.1rem calc(88px + env(safe-area-inset-bottom)); }

            .p-tabs {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 50;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                padding: 0.4rem 0.5rem calc(0.4rem + env(safe-area-inset-bottom));
                background: #fff;
                border-top: 1px solid var(--line);
                box-shadow: 0 -6px 20px rgba(13, 27, 32, 0.06);
            }

            .p-tabs a {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.2rem;
                min-height: 52px;
                color: var(--muted);
                font-size: 0.7rem;
                font-weight: 600;
                text-decoration: none;
                border-radius: 10px;
            }

            .p-tabs a i { font-size: 1.05rem; }

            .p-tabs a.is-active { color: var(--ink); }
            .p-tabs a.is-active i { color: var(--orange); }

            .p-tabs .p-tab-new i {
                width: 42px;
                height: 42px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: var(--orange);
                color: var(--ink);
                font-size: 1rem;
                margin-top: -1.1rem;
                box-shadow: 0 6px 16px rgba(255, 98, 0, 0.35);
            }
        }
    </style>
    @yield('styles')
    @livewireStyles
</head>
<body class="portal ui-page">
    <div class="portal-shell">
        <aside class="p-side" aria-label="Portal navigation">
            <a href="{{ route('dashboard') }}" class="p-logo" aria-label="Forus Freight portal home">
                <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight">
            </a>

            <a href="{{ route('client.shipments.create') }}" class="p-new {{ request()->routeIs('client.shipments.create') ? 'is-active' : '' }}"><i class="fas fa-plus" aria-hidden="true"></i> New shipment</a>

            <nav>
                <ul class="p-nav">
                    @foreach ($nav as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="{{ $item['active'] ? 'is-active' : '' }}" @if($item['active']) aria-current="page" @endif>
                                <i class="fas {{ $item['icon'] }}" aria-hidden="true"></i>{{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="p-side-foot">
                <a href="{{ route('client.getting-started') }}"><i class="fas fa-circle-question" aria-hidden="true"></i> How it works</a>
                <a href="https://wa.me/260572788685?text={{ urlencode('Hi Forus Freight, I need help with my account') }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp support</a>
                <button type="button" onclick="document.getElementById('logout-form').submit()"><i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i> Sign out</button>
                <div class="p-user">
                    <span class="p-avatar" aria-hidden="true">{{ $initials }}</span>
                    <span style="min-width: 0;">
                        <strong>{{ $portalUser->name }}</strong>
                        <small>{{ $portalUser->email }}</small>
                    </span>
                </div>
            </div>
        </aside>

        <div>
            <header class="p-topbar">
                <a href="{{ route('dashboard') }}" aria-label="Forus Freight portal home"><img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight"></a>
                <div class="p-topbar-actions">
                    <a href="https://wa.me/260572788685?text={{ urlencode('Hi Forus Freight, I need help with my account') }}" target="_blank" rel="noopener" class="p-icon-btn" aria-label="WhatsApp support"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                    <a href="{{ route('client.profile') }}" class="p-avatar" style="width: 40px; height: 40px;" aria-label="Your account">{{ $initials }}</a>
                </div>
            </header>

            <main class="p-main" id="main-content">
                <div class="p-content">
                    {{-- Centralised so every client page shows success/error feedback --}}
                    @if(session('success') || session('error') || session('info') || $errors->any())
                        <div class="p-flash">
                            @if(session('success'))
                                <div class="p-flash-msg p-flash-ok" role="status"><i class="fas fa-check-circle" aria-hidden="true"></i><span>{{ session('success') }}</span><button type="button" class="p-flash-close" onclick="this.parentElement.remove()" aria-label="Dismiss"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                            @endif
                            @if(session('error'))
                                <div class="p-flash-msg p-flash-err" role="alert"><i class="fas fa-circle-exclamation" aria-hidden="true"></i><span>{{ session('error') }}</span><button type="button" class="p-flash-close" onclick="this.parentElement.remove()" aria-label="Dismiss"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                            @endif
                            @if(session('info'))
                                <div class="p-flash-msg p-flash-info" role="status"><i class="fas fa-circle-info" aria-hidden="true"></i><span>{{ session('info') }}</span><button type="button" class="p-flash-close" onclick="this.parentElement.remove()" aria-label="Dismiss"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                            @endif
                            @if($errors->any())
                                <div class="p-flash-msg p-flash-err" role="alert"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                                    <span>
                                        @if($errors->count() === 1)
                                            {{ $errors->first() }}
                                        @else
                                            <strong>Please fix the following:</strong>
                                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                                        @endif
                                    </span>
                                    <button type="button" class="p-flash-close" onclick="this.parentElement.remove()" aria-label="Dismiss"><i class="fas fa-times" aria-hidden="true"></i></button>
                                </div>
                            @endif
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Bottom tabs on phones --}}
    <nav class="p-tabs" aria-label="Portal navigation">
        <a href="{{ route('dashboard') }}" class="{{ $nav[0]['active'] ? 'is-active' : '' }}"><i class="fas fa-house" aria-hidden="true"></i>Home</a>
        <a href="{{ route('client.shipments') }}" class="{{ $nav[1]['active'] ? 'is-active' : '' }}"><i class="fas fa-box" aria-hidden="true"></i>Shipments</a>
        <a href="{{ route('client.shipments.create') }}" class="p-tab-new {{ request()->routeIs('client.shipments.create') ? 'is-active' : '' }}"><i class="fas fa-plus" aria-hidden="true"></i>New</a>
        <a href="{{ route('client.profile') }}" class="{{ $nav[2]['active'] ? 'is-active' : '' }}"><i class="fas fa-user" aria-hidden="true"></i>Account</a>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
        {{-- Clients log in on the "web" guard; name it so signing out here
             doesn't also end a staff session held in the same browser. --}}
        <input type="hidden" name="guard" value="web">
    </form>

    <script>
        // Spinner + disable on plain form submits so a slow request doesn't
        // look like a dead click (Livewire forms handle their own state).
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement) || e.defaultPrevented) return;
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
