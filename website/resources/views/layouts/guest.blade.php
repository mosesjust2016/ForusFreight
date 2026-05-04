<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased h-full" style="font-family: 'Outfit', sans-serif;">
        <div class="flex min-h-screen">
            <!-- Left Side - Branding & Design -->
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-indigo-900 via-indigo-800 to-emerald-800 justify-center items-center">
                <!-- Background decoration -->
                <div class="absolute inset-0 bg-black/20 z-0"></div>
                <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] rounded-full bg-indigo-500/30 blur-3xl filter mix-blend-screen animate-pulse z-0"></div>
                <div class="absolute -bottom-[20%] -right-[10%] w-[70%] h-[70%] rounded-full bg-emerald-500/30 blur-3xl filter mix-blend-screen animate-pulse z-0" style="animation-delay: 2s;"></div>
                
                <!-- Content -->
                <div class="relative z-10 p-12 text-center text-white flex flex-col items-center">
                    <a href="/" wire:navigate class="mb-8 block transform transition duration-500 hover:scale-105">
                        <x-application-logo class="w-24 h-24 fill-current text-white drop-shadow-lg" />
                    </a>
                    <h1 class="text-5xl font-bold mb-6 tracking-tight drop-shadow-md">Welcome to ForusFreight</h1>
                    <p class="text-lg text-indigo-100 max-w-md mx-auto leading-relaxed font-medium">
                        The ultimate platform for seamless logistics and freight management.
                    </p>
                </div>
            </div>

            <!-- Right Side - Form Container -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24 bg-gray-50 dark:bg-gray-900 relative">
                <!-- Mobile Logo -->
                <div class="absolute top-8 left-0 w-full lg:hidden flex justify-center">
                    <a href="/" wire:navigate>
                        <x-application-logo class="w-16 h-16 fill-current text-indigo-600 dark:text-indigo-400" />
                    </a>
                </div>
                
                <div class="w-full max-w-md mx-auto mt-16 lg:mt-0">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
