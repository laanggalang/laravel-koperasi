<!-- Kompilasi Aset Frontend Vite -->
@vite(['resources/css/app.css', 'resources/js/app.js'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama Koperasi') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Banner Selamat Datang -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900 flex items-center space-x-4">
                    <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-gray-800">Selamat Datang Kembali, {{ auth()->user()->name }}!</h5>
                        <p class="text-sm text-gray-500 mt-0.5">Anda masuk sebagai Administrator. Berikut ringkasan performa finansial koperasi hari ini.</p>
                    </div>
                </div>
            </div>

            <!-- Grid Ringkasan Statistik Finansial -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: Total Anggota -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Anggota Aktif</span>
                        <span class="block text-3xl font-black text-gray-800 mt-2">{{ $memberCount }}</span>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Total Simpanan -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Kas Simpanan</span>
                        <span class="block text-xl font-black text-green-600 mt-3">Rp {{ number_format($savingTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Card 3: Total Pinjaman Disalurkan -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pinjaman</span>
                        <span class="block text-xl font-black text-red-600 mt-3">Rp {{ number_format($loanTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                        </svg>
                    </div>
                </div>

                <!-- Card 4: Total Angsuran Diterima -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Angsuran Masuk</span>
                        <span class="block text-xl font-black text-indigo-600 mt-3">Rp {{ number_format($installmentTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>

            </div>

            <!-- Banner Sisa Tagihan Berjalan (Outstanding) -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-3 sm:mb-0">
                    <h4 class="text-sm font-bold text-amber-900 uppercase tracking-wider">Sisa Saldo Tagihan Koperasi (Outstanding)</h4>
                    <p class="text-xs text-amber-700 mt-0.5">Total aset modal koperasi yang saat ini masih dipinjam dan berada di tangan anggota.</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-2xl font-black text-amber-700 font-mono">Rp {{ number_format($loanOutstanding, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Quick Access / Pintasan Menu Cepat -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Navigasi Pintasan Cepat</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('members.index') }}" class="p-4 bg-gray-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg border text-center transition-all font-medium text-sm text-gray-700 block">👥 Data Anggota</a>
                    <a href="{{ route('savings.index') }}" class="p-4 bg-gray-50 hover:bg-green-50 hover:text-green-600 rounded-lg border text-center transition-all font-medium text-sm text-gray-700 block">💰 Kas Simpanan</a>
                    <a href="{{ route('loans.index') }}" class="p-4 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-lg border text-center transition-all font-medium text-sm text-gray-700 block">📉 Pinjaman Kredit</a>
                    <a href="{{ route('installments.index') }}" class="p-4 bg-gray-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg border text-center transition-all font-medium text-sm text-gray-700 block">📋 Riwayat Angsuran</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
