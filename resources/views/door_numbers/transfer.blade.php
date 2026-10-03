@extends('layouts.app')

@section('header')
    Transfer Nomor Pintu
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
            <h2 class="text-xl font-bold text-gray-800">Transfer {{ $doorNumber->door_no }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pindahkan nomor pintu ke anggota aktif lain.</p>
        </div>
    </div>

    <!-- Info aset -->
    <div class="card p-4 flex items-center justify-between bg-indigo-50/50 border-indigo-100">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Pintu</p>
            <p class="text-lg font-black font-mono text-indigo-700">{{ $doorNumber->door_no }} <span class="font-sans text-sm font-medium text-gray-600">({{ $doorNumber->plate_no }})</span></p>
        </div>
        <div class="text-right">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pemegang Saat Ini</p>
            <p class="text-sm font-bold text-gray-800">{{ $doorNumber->holder_display }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="card">
        <form action="{{ route('door-numbers.transfer', $doorNumber) }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="member_id" class="block text-sm font-medium text-gray-700">Anggota Baru <span class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required class="form-input-custom mt-1 block w-full">
                    <option value="">-- Pilih Anggota Aktif --</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->member_no }} - {{ $member->name }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1">*Hanya anggota berstatus aktif yang dapat menerima transfer.</p>
                @error('member_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="transfer_date" class="block text-sm font-medium text-gray-700">Tanggal Transfer <span class="text-red-500">*</span></label>
                    <input type="date" name="transfer_date" id="transfer_date" value="{{ old('transfer_date', now()->format('Y-m-d')) }}" required
                        min="{{ $doorNumber->registered_date->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"
                        class="form-input-custom mt-1 block w-full">
                    @error('transfer_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="cth: serah terima sewa"
                        class="form-input-custom mt-1 block w-full">
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-lg text-xs text-indigo-700">
                Setelah transfer, nomor pintu langsung <strong>aktif</strong> dengan pemegang baru dan tercatat di riwayat.
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('door-numbers.show', $doorNumber) }}" class="btn-secondary">Batal</a>
                <button type="submit" onclick="return confirm('Transfer nomor pintu ini ke anggota yang dipilih?')" class="btn-primary">
                    Proses Transfer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
