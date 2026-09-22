<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
                <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                    </svg>
                </span>
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
