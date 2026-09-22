@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('members.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tambah Anggota Baru</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pastikan seluruh data pribadi diisi dengan benar dan valid.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <form action="{{ route('members.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="member_no" class="block text-sm font-medium text-gray-700">No. Anggota <span class="text-red-500">*</span></label>
                    <input type="text" name="member_no" id="member_no" value="{{ old('member_no') }}" required placeholder="cth: 0004"
                        class="form-input-custom mt-1 block w-full">
                    @error('member_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="form-input-custom mt-1 block w-full">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required
                        class="form-input-custom mt-1 block w-full">
                    @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="cth: 0812xxxxxxx"
                        class="form-input-custom mt-1 block w-full">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="cth: anggota@email.com"
                    class="form-input-custom mt-1 block w-full">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <textarea name="address" id="address" rows="3" class="form-input-custom mt-1 block w-full">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="join_date" class="block text-sm font-medium text-gray-700">Tanggal Bergabung <span class="text-red-500">*</span></label>
                <input type="date" name="join_date" id="join_date" value="{{ old('join_date', date('Y-m-d')) }}" required
                    class="form-input-custom mt-1 block w-full sm:w-56">
                @error('join_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('members.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan Anggota
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
