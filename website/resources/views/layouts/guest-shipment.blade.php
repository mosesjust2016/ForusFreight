<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Forus Freight'))</title>
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
        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; 2026 Forus Freight Limited. All rights reserved.</p>
        <p style="margin-top: 0.5rem;">
            <a href="{{ route('terms') }}">Terms & Conditions</a>
        </p>
    </footer>

    @yield('scripts')
    @livewireScripts
</body>
</html>
