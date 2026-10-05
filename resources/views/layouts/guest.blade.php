<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-gray-100 to-indigo-100">
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-6">
                <x-app-logo class="w-14 h-14 object-contain shrink-0" />
                <div>
                    <p class="font-black text-gray-800 text-xl leading-none tracking-wide">KABAPIN</p>
                    <p class="text-[11px] text-gray-500 font-medium uppercase tracking-widest mt-1">Koperasi Digital</p>
                </div>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl shadow-gray-200/60 overflow-hidden rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} KABAPIN &mdash; Sistem Informasi Koperasi</p>
        </div>
    </body>
</html>
