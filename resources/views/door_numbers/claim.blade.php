@extends('layouts.app')

@section('header')
    Ambil Nomor Pintu
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
            <h2 class="text-xl font-bold text-gray-800">Pengambilan Nomor Pintu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Anggota aktif memilih nomor pintu yang tersedia di pool KBP.</p>
        </div>
    </div>

    @if($available->isEmpty())
    <div class="card p-6 text-center">
        <p class="text-sm text-gray-500">Tidak ada nomor pintu berstatus <span class="badge-warning">Tersedia</span> saat ini.</p>
        <p class="text-sm text-gray-400 mt-1 italic">Daftarkan nomor pintu baru terlebih dahulu lewat menu Tambah Nomor Pintu.</p>
        <a href="{{ route('door-numbers.create') }}" class="btn-primary mt-4">Tambah Nomor Pintu</a>
    </div>
    @else
    <!-- Form -->
    <div class="card">
        <form action="{{ route('door-numbers.claim') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="member_id" class="block text-sm font-medium text-gray-700">Anggota Pengambil <span class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required class="form-input-custom mt-1 block w-full">
                    <option value="">-- Pilih Anggota Aktif --</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ (old('member_id') ?? $preselectMember) == $member->id ? 'selected' : '' }}>
                            {{ $member->member_no }} - {{ $member->name }}
                        </option>
                    @endforeach
                </select>
                @error('member_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="door_number" class="block text-sm font-medium text-gray-700">Pilih Nomor Pintu Tersedia <span class="text-red-500">*</span></label>
                <select name="door_number" id="door_number" required class="form-input-custom mt-1 block w-full">
                    <option value="">-- {{ $available->count() }} nomor pintu tersedia --</option>
                    @foreach($available as $np)
                        <option value="{{ $np->id }}" {{ (old('door_number') ?? $preselectDoor) == $np->id ? 'selected' : '' }}>
                            {{ $np->door_no }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1">*Setelah diambil, nomor pintu langsung <strong>aktif</strong> atas nama anggota tersebut.</p>
                @error('door_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Data Kendaraan &amp; Pengemudi (Opsional)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="plate_no" class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                        <input type="text" name="plate_no" id="plate_no" value="{{ old('plate_no') }}" placeholder="cth: D 1234 ABC"
                            class="form-input-custom mt-1 block w-full uppercase">
                        @error('plate_no') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
                        <input type="text" name="vehicle_type" id="vehicle_type" value="{{ old('vehicle_type') }}" placeholder="cth: angkot, pickup"
                            class="form-input-custom mt-1 block w-full">
                        @error('vehicle_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="driver_name" class="block text-sm font-medium text-gray-700">Nama Pengemudi</label>
                        <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name') }}"
                            placeholder="Kosongkan jika anggota sendiri yang mengemudi"
                            class="form-input-custom mt-1 block w-full">
                        <p class="text-[10px] text-gray-400 mt-1">*Isi hanya jika pengemudi adalah orang lain (bukan anggota pemegang).</p>
                        @error('driver_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div>
                <label for="claim_date" class="block text-sm font-medium text-gray-700">Tanggal Ambil <span class="text-red-500">*</span></label>
                <input type="date" name="claim_date" id="claim_date" value="{{ old('claim_date', date('Y-m-d')) }}" required
                    max="{{ now()->format('Y-m-d') }}" class="form-input-custom mt-1 block w-full sm:w-56">
                @error('claim_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('door-numbers.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" onclick="return confirm('Proses pengambilan nomor pintu oleh anggota yang dipilih?')" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Proses Pengambilan
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
