<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Forus Freight') }} - @yield('title', 'Authentication')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary: rgb(0, 127, 127);
                --secondary: rgb(255, 98, 0);
                --tertiary: rgb(204, 204, 204);
                --dark: #0f172a;
                --light: #ffffff;
            }

            body {
                font-family: 'Inter', sans-serif;
            }

            .auth-hero {
                background: linear-gradient(135deg, var(--primary) 0%, rgba(0, 150, 150, 0.9) 100%);
                position: relative;
                overflow: hidden;
            }

            .auth-pattern {
                position: absolute;
                inset: 0;
                opacity: 0.1;
                background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            }

            .logo-text {
                font-size: 2.5rem;
                font-weight: 800;
                color: white;
                text-decoration: none;
                margin-bottom: 1rem;
                display: block;
            }

            .btn-auth {
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--primary);
                color: white;
                padding: 0.875rem 2rem;
                border-radius: 50px;
                font-weight: 700;
                font-size: 0.9375rem;
                transition: all 0.3s ease;
                border: 2px solid var(--primary);
                box-shadow: 0 4px 15px rgba(0, 127, 127, 0.3);
                width: 100%;
                cursor: pointer;
                text-decoration: none;
            }

            .btn-auth:hover {
                background: white;
                color: var(--primary);
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 127, 127, 0.4);
            }

            /* Override @tailwindcss/forms resets for .form-control inputs */
            .input-wrapper {
                position: relative;
                display: block;
                width: 100%;
            }

            .input-icon {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                display: flex;
                align-items: center;
                padding-left: 1rem;
                pointer-events: none;
                color: #94a3b8;
                z-index: 1;
            }

            .form-control {
                display: block;
                width: 100%;
                border-radius: 12px;
                border: 2px solid var(--tertiary) !important;
                padding: 0.75rem 1rem !important;
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
                background-color: #ffffff;
                font-size: 0.9375rem;
                line-height: 1.5;
                color: #1e293b;
                box-sizing: border-box;
            }

            .form-control.has-icon {
                padding-left: 2.75rem !important;
            }

            .form-control:focus {
                border-color: var(--primary) !important;
                outline: none;
                box-shadow: 0 0 0 4px rgba(0, 127, 127, 0.1) !important;
            }

            .form-control::placeholder {
                color: #94a3b8;
            }
        </style>
    </head>
    <body class="antialiased h-full">
        <div class="min-h-screen flex flex-col lg:grid lg:grid-cols-2">
            <!-- Left Side - Branding & Design -->
            <div class="hidden lg:flex auth-hero items-center justify-center p-12 text-white relative">
                <div class="auth-pattern"></div>
                
                <div class="relative z-10 text-center max-w-md">
                    <a href="/" class="mb-8 inline-block transform hover:scale-105 transition-transform duration-300">
                        <div class="bg-white rounded-2xl px-6 py-4 inline-block shadow-lg">
                            <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight" class="h-20 w-auto mx-auto">
                        </div>
                    </a>
                    <h1 class="text-4xl font-bold mb-6">Your Global Logistics Partner</h1>
                    <p class="text-lg opacity-90 leading-relaxed font-medium">
                        Seamlessly manage your freight, track shipments in real-time, and grow your business with Forus Freight.
                    </p>

                    <div class="mt-12 grid grid-cols-2 gap-6 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-location-crosshairs"></i>
                            </div>
                            <span class="text-sm font-semibold">Real-time Tracking</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <span class="text-sm font-semibold">Secure Logistics</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-globe"></i>
                            </div>
                            <span class="text-sm font-semibold">Global Network</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-headset"></i>
                            </div>
                            <span class="text-sm font-semibold">24/7 Support</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form Container -->
            <div class="flex flex-col justify-center px-6 py-12 bg-slate-50 relative">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-8">
                    <a href="/">
                        <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight" class="h-14 w-auto">
                    </a>
                </div>
                
                <div class="w-full max-w-md mx-auto">
                    {{ $slot }}
                </div>

                <div class="mt-8 text-center text-slate-400 text-sm">
                    &copy; {{ date('Y') }} Forus Freight Ltd. All rights reserved.
                </div>
            </div>
        </div>
    </body>
</html>
