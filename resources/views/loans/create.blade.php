@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('loans.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Formulir Pengajuan Pinjaman</h2>
            <p class="text-sm text-gray-500 mt-0.5">Catat pengajuan pinjaman baru bagi anggota koperasi secara terstruktur.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <form action="{{ route('loans.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dropdown Pilih Anggota -->
                    <div>
                        <label for="member_id" class="block text-sm font-medium text-gray-700">Pilih Anggota Koperasi</label>
                        <select name="member_id" id="member_id" required
                            class="mt-1 block w-full form-input-custom">
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->member_no }} - {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('member_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Tanggal Mulai Pinjam (Name disesuaikan jadi start_date) -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                            class="mt-1 block w-full form-input-custom">
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Input Jumlah Pinjaman Pokok (Name disesuaikan jadi principal) -->
                    <div class="md:col-span-1">
                        <label for="principal" class="block text-sm font-medium text-gray-700">Pinjaman Pokok</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="principal" id="principal" value="{{ old('principal') }}" required min="1"
                                placeholder="0"
                                class="block w-full pl-10 form-input-custom">
                        </div>
                        @error('principal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Suku Bunga (Baru, Name disesuaikan jadi interest_rate) -->
                    <div>
                        <label for="interest_rate" class="block text-sm font-medium text-gray-700">Suku Bunga (%)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate', 0) }}" required min="0" step="0.01"
                                placeholder="Contoh: 2.5"
                                class="block w-full pr-12 form-input-custom">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        @error('interest_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Jangka Waktu / Tenor -->
                    <div>
                        <label for="tenor" class="block text-sm font-medium text-gray-700">Tenor</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="tenor" id="tenor" value="{{ old('tenor') }}" required min="1" max="120"
                                placeholder="Bulan"
                                class="block w-full pr-16 form-input-custom">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Bulan</span>
                            </div>
                        </div>
                        @error('tenor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Input Catatan / Keterangan -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan / Keterangan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Keperluan bayar spp sekolah anak"
                        class="mt-1 block w-full form-input-custom">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                    <a href="{{ route('loans.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Ajukan Pinjaman</button>
                </div>
            </form>
        </div>
    </div>
@endsection
