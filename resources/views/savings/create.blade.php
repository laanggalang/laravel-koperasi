@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">


        <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('savings.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tambah Transaksi Simpanan</h2>
            <p class="text-sm text-gray-500 mt-0.5">Catat transaksi simpanan masuk dari anggota koperasi secara akurat.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
            <form action="{{ route('savings.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Input No Transaksi / Simpanan -->
                    <div>
                        <label for="saving_no" class="block text-sm font-medium text-gray-700">No. Transaksi / Simpanan</label>
                        <input type="text" name="saving_no" id="saving_no" value="{{ old('saving_no') }}" required
                            placeholder="Contoh: SPN-0001"
                            class="mt-1 block w-full form-input-custom">
                        @error('saving_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Tanggal Transaksi -->
                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" id="transaction_date" value="{{ old('date', date('Y-m-d')) }}" required
                            class="mt-1 block w-full form-input-custom">
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dropdown Jenis Simpanan -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Jenis Simpanan</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full form-input-custom">
                            <option value="pokok" {{ old('type') == 'pokok' ? 'selected' : '' }}>Simpanan Pokok</option>
                            <option value="wajib" {{ old('type') == 'wajib' ? 'selected' : '' }}>Simpanan Wajib</option>
                            <option value="sukarela" {{ old('type') == 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela</option>
                        </select>
                        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Jumlah Uang / Nominal -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah Nominal (Rp)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1"
                                placeholder="0"
                                class="block w-full pl-10 form-input-custom">
                        </div>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                    <a href="{{ route('savings.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
