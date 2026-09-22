@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

        <!-- Tombol Kembali -->
        <div class="mb-4">
            <a href="{{ route('members.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                ← Kembali ke Daftar Anggota
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
            <span class="text-sm font-medium text-green-800">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
            <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Profile Card Container -->
        <div class="card">
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

                    <!-- Baris Status -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Keanggotaan</label>
                        <div class="mt-1">
                            @if(($member->status ?? 'active') === 'active')
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase">
                                    Active
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 uppercase">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Baris Tanggal Gabung -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Gabung</label>
                        <p class="text-base font-medium text-gray-900 mt-1">{{ $member->join_date?->format('d-m-Y') ?? '-' }}</p>
                    </div>

                    <!-- Baris Tanggal Keluar -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Keluar</label>
                        <p class="text-base font-medium {{ $member->exit_date ? 'text-gray-900' : 'text-gray-400 italic' }} mt-1">
                            {{ $member->exit_date?->format('d-m-Y') ?? 'Masih aktif' }}
                        </p>
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

            <!-- Panel Aksi Status Keanggotaan -->
            @php
                $hasUnpaidLoans = $member->hasUnpaidLoans();
                $unpaidCount = $member->loans()->whereIn('status', ['pending', 'active'])->count();
            @endphp
            <div class="p-6 bg-gray-50">
                <h3 class="text-base font-bold text-gray-800 mb-1">Aksi Keanggotaan</h3>
                <p class="text-xs text-gray-500 mb-4">Ubah status anggota keluar dari koperasi atau aktifkan kembali.</p>

                @if($hasUnpaidLoans)
                <!-- Peringatan pinjaman belum lunas -->
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Tidak dapat dinonaktifkan</p>
                        <p class="text-xs text-red-600 mt-0.5">
                            Anggota masih memiliki <span class="font-bold">{{ $unpaidCount }}</span> pinjaman berstatus pending/aktif.
                            Lunasi atau batalkan pinjaman terlebih dahulu.
                        </p>
                    </div>
                </div>
                @endif

                @if(($member->status ?? 'active') === 'active')
                <!-- Form: Nonaktifkan anggota -->
                <form action="{{ route('members.updateStatus', $member) }}" method="POST" class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="inactive">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        <div class="flex-1">
                            <x-input-label for="exit_date" value="Tanggal Keluar" class="mb-1" />
                            <x-text-input id="exit_date" name="exit_date" type="date"
                                :value="$member->join_date?->format('Y-m-d')"
                                min="{{ $member->join_date?->format('Y-m-d') }}"
                                max="{{ now()->format('Y-m-d') }}" class="block w-full sm:w-56" required />
                            @error('exit_date')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" @disabled($hasUnpaidLoans)
                            onclick="return confirm('Yakin menonaktifkan {{ $member->name }}? Anggota tidak akan bisa bertransaksi.')"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-bold shadow-sm transition-colors whitespace-nowrap disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-red-600">
                            Nonaktifkan Anggota
                        </button>
                    </div>
                </form>
                @else
                <!-- Form: Aktifkan kembali -->
                <form action="{{ route('members.updateStatus', $member) }}" method="POST" class="bg-green-50 border border-green-200 rounded-lg p-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="active">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            Keluar pada <span class="font-semibold text-gray-800">{{ $member->exit_date?->format('d-m-Y') ?? '-' }}</span>.
                            Aktifkan kembali akan menghapus tanggal keluar.
                        </p>
                        <button type="submit" onclick="return confirm('Yakin mengaktifkan kembali {{ $member->name }}?')"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-bold shadow-sm transition-colors whitespace-nowrap">
                            Aktifkan Kembali
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
@endsection
