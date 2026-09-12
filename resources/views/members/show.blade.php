@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <div class="mb-4">
            <a href="{{ route('members.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                ← Kembali ke Daftar Anggota
            </a>
        </div>

        <!-- Profile Card Container -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Card Header -->
            <div class="bg-indigo-600 px-6 py-6 flex items-center space-x-4">
                <div class="p-3 bg-indigo-500 rounded-full text-white shadow-inner">
                    <!-- Ikon User Sederhana -->
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $member->name }}</h2>
                    <p class="text-indigo-100 text-sm font-mono mt-0.5">No. Anggota: {{ $member->member_no }}</p>
                </div>
            </div>

            <!-- Card Body / Detail Information -->
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Pribadi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Baris NIK -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Induk Kependudukan (NIK)</label>
                        <p class="text-base font-medium text-gray-900 mt-1">{{ $member->nik ?? '-' }}</p>
                    </div>

                    <!-- Baris Telepon -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">No. Telepon / WhatsApp</label>
                        <p class="text-base font-medium text-gray-900 mt-1">{{ $member->phone ?? '-' }}</p>
                    </div>

                    <!-- Baris Email -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Alamat Email</label>
                        <p class="text-base font-medium text-gray-900 mt-1 text-indigo-600">{{ $member->email ?? '-' }}</p>
                    </div>

                    <!-- Baris Status (Opsional jika ada di database) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Keanggotaan</label>
                        <div class="mt-1">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase">
                                Active
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bagian Alamat Lengkap -->
                <div class="mt-6 border-t pt-4">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Alamat Lengkap</label>
                    <p class="text-base font-medium text-gray-700 mt-1 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed">
                        {{ $member->address ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
