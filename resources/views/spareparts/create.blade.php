@extends('layouts.app')

@section('header')
    Tambah Sparepart
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('spareparts.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tambah Sparepart</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kode barang dibuat otomatis oleh sistem.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <form action="{{ route('spareparts.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required placeholder="cth: Oli Mesin Castrol 20W-50"
                        class="form-input-custom mt-1 block w-full">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori" id="kategori" required class="form-input-custom mt-1 block w-full">
                            @foreach($kategori as $k)
                                <option value="{{ $k }}" {{ old('kategori') === $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                        @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="satuan" id="satuan" value="{{ old('satuan', 'pcs') }}" required placeholder="pcs/liter/dus"
                            class="form-input-custom mt-1 block w-full">
                        @error('satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Harga -->
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Harga (per {{ old('satuan', 'satuan') }})</p>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="harga_beli" class="block text-xs font-medium text-gray-600">Harga Beli (HPP) <span class="text-red-500">*</span></label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-400 text-sm pointer-events-none">Rp</span>
                            <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}" required min="0" step="0.01"
                                class="form-input-custom block w-full ps-10">
                        </div>
                        @error('harga_beli') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="harga_anggota" class="block text-xs font-medium text-emerald-700">Harga Anggota <span class="text-red-500">*</span></label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-400 text-sm pointer-events-none">Rp</span>
                            <input type="number" name="harga_anggota" id="harga_anggota" value="{{ old('harga_anggota') }}" required min="0" step="0.01"
                                class="form-input-custom block w-full ps-10 border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        @error('harga_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="harga_driver" class="block text-xs font-medium text-blue-700">Harga Driver <span class="text-red-500">*</span></label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-400 text-sm pointer-events-none">Rp</span>
                            <input type="number" name="harga_driver" id="harga_driver" value="{{ old('harga_driver') }}" required min="0" step="0.01"
                                class="form-input-custom block w-full ps-10 border-blue-200 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        @error('harga_driver') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="harga_umum" class="block text-xs font-medium text-gray-700">Harga Umum <span class="text-red-500">*</span></label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-400 text-sm pointer-events-none">Rp</span>
                            <input type="number" name="harga_umum" id="harga_umum" value="{{ old('harga_umum') }}" required min="0" step="0.01"
                                class="form-input-custom block w-full ps-10">
                        </div>
                        @error('harga_umum') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Stok -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="stok_qty" class="block text-sm font-medium text-gray-700">Stok Awal <span class="text-red-500">*</span></label>
                    <input type="number" name="stok_qty" id="stok_qty" value="{{ old('stok_qty', 0) }}" required min="0" step="0.01"
                        class="form-input-custom mt-1 block w-full">
                    @error('stok_qty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="stok_minimal" class="block text-sm font-medium text-gray-700">Stok Minimal (Alert) <span class="text-red-500">*</span></label>
                    <input type="number" name="stok_minimal" id="stok_minimal" value="{{ old('stok_minimal', 0) }}" required min="0" step="0.01"
                        class="form-input-custom mt-1 block w-full">
                    <p class="text-[10px] text-gray-400 mt-1">*Badge merah muncul saat stok <= nilai ini.</p>
                    @error('stok_minimal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('spareparts.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
