@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <div class="mb-4">
            <a href="{{ route('loans.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                ← Kembali ke Data Pinjaman
            </a>
        </div>

        <!-- Form Card Container -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Card Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">Formulir Pengajuan Pinjaman</h2>
                <p class="text-xs text-gray-500 mt-0.5">Catat pengajuan pinjaman baru bagi anggota koperasi secara terstruktur.</p>
            </div>

            <!-- Form Body -->
            <form action="{{ route('loans.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                    <!-- Input Tanggal Mulai Pinjam (Name disesuaikan jadi start_date) -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                                class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        @error('principal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Suku Bunga (Baru, Name disesuaikan jadi interest_rate) -->
                    <div>
                        <label for="interest_rate" class="block text-sm font-medium text-gray-700">Suku Bunga (%)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate', 0) }}" required min="0" step="0.01"
                                placeholder="Contoh: 2.5"
                                class="block w-full pr-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                                class="block w-full pr-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('loans.index') }}" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors">
                        Ajukan Pinjaman
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
