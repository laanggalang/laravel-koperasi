@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <div class="mb-4">
            <a href="{{ route('savings.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                ← Kembali ke Data Simpanan
            </a>
        </div>

        <!-- Form Card Container -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Card Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">Tambah Transaksi Simpanan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Catat transaksi simpanan masuk dari anggota koperasi secara akurat.</p>
            </div>

            <!-- Form Body -->
            <form action="{{ route('savings.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Input No Transaksi / Simpanan -->
                    <div>
                        <label for="saving_no" class="block text-sm font-medium text-gray-700">No. Transaksi / Simpanan</label>
                        <input type="text" name="saving_no" id="saving_no" value="{{ old('saving_no') }}" required
                            placeholder="Contoh: SPN-0001"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('saving_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Tanggal Transaksi -->
                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" id="transaction_date" value="{{ old('date', date('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Dropdown Pilih Anggota -->
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Pilih Anggota Koperasi</label>
                    <select name="member_id" id="member_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                                class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('savings.index') }}" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
