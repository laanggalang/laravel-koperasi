@extends('layouts.app')

@section('header')
    Tambah Nomor Pintu
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('door-numbers.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pendaftaran Nomor Pintu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Masukkan aset nomor pintu ke pool KBP. Status awal: <span class="badge-warning">Tersedia</span> — menunggu diambil anggota.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <form action="{{ route('door-numbers.store') }}" method="POST" class="p-6 space-y-5" x-data="{ mode: '{{ old('mode', 'single') }}' }">
            @csrf

            <!-- Mode -->
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="mode = 'single'"
                    :class="mode === 'single' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-500' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                    class="rounded-xl border p-3 text-sm font-bold transition-all">
                    Nomor Tunggal
                </button>
                <button type="button" @click="mode = 'range'"
                    :class="mode === 'range' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-500' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'"
                    class="rounded-xl border p-3 text-sm font-bold transition-all">
                    Rentang (Banyak Sekaligus)
                </button>
            </div>
            <input type="hidden" name="mode" :value="mode">

            <!-- Single mode -->
            <div x-show="mode === 'single'">
                <label for="door_no" class="block text-sm font-medium text-gray-700">Nomor Pintu <span class="text-red-500">*</span></label>
                <input type="text" name="door_no" id="door_no" value="{{ old('door_no') }}" placeholder="cth: NP-101"
                    class="form-input-custom mt-1 block w-full uppercase">
                @error('door_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Range mode -->
            <div x-show="mode === 'range'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="door_no_start" class="block text-sm font-medium text-gray-700">Nomor Awal <span class="text-red-500">*</span></label>
                    <input type="text" name="door_no_start" id="door_no_start" value="{{ old('door_no_start') }}" placeholder="cth: NP-001"
                        class="form-input-custom mt-1 block w-full uppercase">
                    @error('door_no_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="door_no_end" class="block text-sm font-medium text-gray-700">Nomor Akhir <span class="text-red-500">*</span></label>
                    <input type="text" name="door_no_end" id="door_no_end" value="{{ old('door_no_end') }}" placeholder="cth: NP-100"
                        class="form-input-custom mt-1 block w-full uppercase">
                    @error('door_no_end') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <p class="md:col-span-2 text-[11px] text-gray-400">*Prefix (huruf) harus sama. Maksimal 500 nomor per pendaftaran. Contoh: NP-001 s/d NP-100 → dibuat 100 aset sekaligus.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="registered_date" class="block text-sm font-medium text-gray-700">Tanggal Daftar <span class="text-red-500">*</span></label>
                    <input type="date" name="registered_date" id="registered_date" value="{{ old('registered_date', date('Y-m-d')) }}" required
                        class="form-input-custom mt-1 block w-full">
                    @error('registered_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="cth: batch armada 2026"
                        class="form-input-custom mt-1 block w-full">
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                Semua nomor pintu didaftarkan berstatus <strong>Tersedia (milik KBP)</strong>, tanpa pemegang.
                Plat, kendaraan, dan pengemudi diisi kemudian saat anggota <strong>mengambil</strong> nomor pintu
                (menu <strong>Ambil Nomor Pintu</strong>) atau lewat edit data aset.
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('door-numbers.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Daftarkan ke Pool
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
