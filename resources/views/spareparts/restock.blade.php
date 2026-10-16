@extends('layouts.app')

@section('header')
    Restock Sparepart
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('spareparts.show', $sparepart) }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Restock — {{ $sparepart->nama }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Tambah stok dari supplier.</p>
        </div>
    </div>

    <!-- Info stok sekarang -->
    <div class="card p-4 flex items-center justify-between bg-indigo-50/50 border-indigo-100">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stok Saat Ini</p>
            <p class="text-lg font-black text-indigo-700">{{ $sparepart->stok_qty }} {{ $sparepart->satuan }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">HPP Saat Ini</p>
            <p class="text-sm font-bold text-gray-800">Rp {{ number_format($sparepart->harga_beli, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="card">
        <form action="{{ route('spareparts.restock', $sparepart) }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="qty" class="block text-sm font-medium text-gray-700">Jumlah Masuk ({{ $sparepart->satuan }}) <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" id="qty" value="{{ old('qty') }}" required min="0.01" step="0.01"
                        class="form-input-custom mt-1 block w-full">
                    @error('qty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="restock_date" class="block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="restock_date" id="restock_date" value="{{ old('restock_date', date('Y-m-d')) }}" required
                        max="{{ now()->format('Y-m-d') }}" class="form-input-custom mt-1 block w-full">
                    @error('restock_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="harga_beli" class="block text-sm font-medium text-gray-700">Update Harga Beli / HPP</label>
                <div class="relative mt-1">
                    <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-400 text-sm pointer-events-none">Rp</span>
                    <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}" min="0" step="0.01"
                        placeholder="{{ $sparepart->harga_beli }} (kosongkan jika tidak berubah)"
                        class="form-input-custom block w-full ps-10">
                </div>
                <p class="text-[10px] text-gray-400 mt-1">*Opsional — isi jika harga dari supplier berubah; HPP master akan diperbarui.</p>
                @error('harga_beli') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="cth: beli dari toko Ahsan"
                    class="form-input-custom mt-1 block w-full">
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('spareparts.show', $sparepart) }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Proses Restock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
