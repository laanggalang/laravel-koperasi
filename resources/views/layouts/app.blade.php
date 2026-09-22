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
    <body class="font-sans antialiased bg-gray-100">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">

            @include('layouts.navigation')

            <!-- Content Area -->
            <div class="lg:ps-64 flex flex-col min-h-screen transition-[padding] duration-200">
                <!-- Topbar -->
                <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
                    <div class="flex items-center gap-4 px-4 sm:px-6 lg:px-8 h-16">
                        <!-- Hamburger (mobile) -->
                        <button @click="sidebarOpen = true" class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                            <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Page Title -->
                        <div class="flex-1 min-w-0">
                            @isset($header)
                                <h2 class="text-base font-bold text-gray-800 truncate">{{ $header }}</h2>
                            @else
                                <h2 class="text-base font-bold text-gray-800 truncate">KABAPIN Koperasi</h2>
                            @endisset
                        </div>

                        <!-- User dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot:trigger>
                                <button class="inline-flex items-center gap-2 p-1.5 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white text-xs font-bold uppercase">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                    <span class="hidden md:block text-sm font-semibold text-gray-700 max-w-[140px] truncate">{{ auth()->user()->name }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </x-slot:trigger>
                            <x-slot:content>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                                    <span class="mt-1 inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-700 uppercase border border-indigo-200">
                                        {{ auth()->user()->role }}
                                    </span>
                                </div>
                                <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-start flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                        Keluar
                                    </button>
                                </form>
                            </x-slot:content>
                        </x-dropdown>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
                    @if(isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </main>

                <!-- Footer -->
                <footer class="px-8 py-4 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} KABAPIN &mdash; Sistem Informasi Koperasi
                </footer>
            </div>

            @stack('scripts')
        </div>
    </body>
</html>
