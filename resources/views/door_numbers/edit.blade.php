@extends('layouts.app')

@section('header')
    Edit Nomor Pintu
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('door-numbers.show', $doorNumber) }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit {{ $doorNumber->door_no }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pemegang tidak dapat diubah di sini — gunakan alur Transfer/Tukar agar tercatat di histori.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <form action="{{ route('door-numbers.update', $doorNumber) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Pemegang (read-only) -->
            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pemegang Saat Ini</p>
                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $doorNumber->holder_display }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="door_no" class="block text-sm font-medium text-gray-700">Nomor Pintu <span class="text-red-500">*</span></label>
                    <input type="text" name="door_no" id="door_no" value="{{ old('door_no', $doorNumber->door_no) }}" required
                        class="form-input-custom mt-1 block w-full">
                    @error('door_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="registered_date" class="block text-sm font-medium text-gray-700">Tanggal Daftar <span class="text-red-500">*</span></label>
                    <input type="date" name="registered_date" id="registered_date" value="{{ old('registered_date', $doorNumber->registered_date->format('Y-m-d')) }}" required
                        class="form-input-custom mt-1 block w-full">
                    @error('registered_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="plate_no" class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                    <input type="text" name="plate_no" id="plate_no" value="{{ old('plate_no', $doorNumber->plate_no) }}"
                        placeholder="cth: D 1234 ABC"
                        class="form-input-custom mt-1 block w-full uppercase">
                    @error('plate_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
                    <input type="text" name="vehicle_type" id="vehicle_type" value="{{ old('vehicle_type', $doorNumber->vehicle_type) }}"
                        class="form-input-custom mt-1 block w-full">
                    @error('vehicle_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="driver_name" class="block text-sm font-medium text-gray-700">Nama Pengemudi</label>
                <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name', $doorNumber->driver_name) }}"
                    placeholder="Kosongkan jika anggota sendiri yang mengemudi"
                    class="form-input-custom mt-1 block w-full">
                @error('driver_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('door-numbers.show', $doorNumber) }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
