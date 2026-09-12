<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo Aplikasi -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-black text-indigo-600 text-lg tracking-wider">
                        🏢 KABAPIN
                    </a>
                </div>

                <!-- Link Menu Navigasi Berdasarkan Role Pilihan -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <!-- 1. Semua user yang login bisa melihat Dashboard -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- 2. Menu DATA ANGGOTA: Hanya untuk Admin -->
                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                            {{ __('Data Anggota') }}
                        </x-nav-link>
                    @endif

                    <!-- 3. Menu KAS SIMPANAN: Untuk Admin, Bendahara, Staff -->
                    @if(in_array(auth()->user()->role, ['admin', 'bendahara', 'staff']))
                        <x-nav-link :href="route('savings.index')" :active="request()->routeIs('savings.*')">
                            {{ __('Kas Simpanan') }}
                        </x-nav-link>
                    @endif

                    <!-- 4. Menu PINJAMAN KREDIT: Untuk Admin, Ketua, Bendahara, Staff -->
                    @if(in_array(auth()->user()->role, ['admin', 'ketua', 'bendahara', 'staff']))
                        <x-nav-link :href="route('loans.index')" :active="request()->routeIs('loans.*')">
                            {{ __('Pinjaman Kredit') }}
                        </x-nav-link>
                    @endif

                    <!-- 5. Menu RIWAYAT ANGSURAN: Untuk Admin, Bendahara, Staff -->
                    @if(in_array(auth()->user()->role, ['admin', 'bendahara', 'staff']))
                        <x-nav-link :href="route('installments.index')" :active="request()->routeIs('installments.*')">
                            {{ __('Riwayat Angsuran') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Pengaturan Akun di Pojok Kanan -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="flex items-center space-x-4">
                    <!-- Badge Penanda Jabatan/Role -->
                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 uppercase border border-indigo-200">
                        💼 {{ auth()->user()->role }}
                    </span>
                    
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                    
                    <!-- Tombol Keluar / Log Out -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">
                            Keluar →
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
