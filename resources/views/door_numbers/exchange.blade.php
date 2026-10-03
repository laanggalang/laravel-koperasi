@extends('layouts.app')

@section('header')
    Tukar Nomor Pintu
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
            <h2 class="text-xl font-bold text-gray-800">Pertukaran Nomor Pintu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Tukar posisi dua nomor pintu antar anggota aktif.</p>
        </div>
    </div>

    @if($candidates->count() < 2)
    <div class="card p-6 text-center">
        <p class="text-sm text-gray-500">Syarat pertukaran: dua nomor pintu <strong>nonaktif</strong>, milik <strong>anggota aktif yang berbeda</strong>, dan hak kewajiban beres.</p>
        <p class="text-sm text-gray-400 mt-1 italic">Nomor pintu yang memenuhi syarat kurang dari 2. Nonaktifkan dulu nomor pintu yang ingin ditukar.</p>
        <a href="{{ route('door-numbers.index') }}" class="btn-secondary mt-4">Kembali ke Daftar</a>
    </div>
    @else
    <!-- Form -->
    <div class="card">
        <form action="{{ route('door-numbers.exchange') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="door_number_a" class="block text-sm font-medium text-gray-700">Nomor Pintu A <span class="text-red-500">*</span></label>
                    <select name="door_number_a" id="door_number_a" required class="form-input-custom mt-1 block w-full">
                        <option value="">-- Pilih --</option>
                        @foreach($candidates as $c)
                            <option value="{{ $c->id }}" {{ old('door_number_a') ?? $preselectA == $c->id ? 'selected' : '' }}>
                                {{ $c->door_no }} — {{ $c->member->name }} ({{ $c->plate_no }})
                            </option>
                        @endforeach
                    </select>
                    @error('door_number_a') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="door_number_b" class="block text-sm font-medium text-gray-700">Nomor Pintu B <span class="text-red-500">*</span></label>
                    <select name="door_number_b" id="door_number_b" required class="form-input-custom mt-1 block w-full">
                        <option value="">-- Pilih --</option>
                        @foreach($candidates as $c)
                            <option value="{{ $c->id }}">{{ $c->door_no }} — {{ $c->member->name }} ({{ $c->plate_no }})</option>
                        @endforeach
                    </select>
                    @error('door_number_b') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="exchange_date" class="block text-sm font-medium text-gray-700">Tanggal Tukar <span class="text-red-500">*</span></label>
                    <input type="date" name="exchange_date" id="exchange_date" value="{{ old('exchange_date', now()->format('Y-m-d')) }}" required
                        max="{{ now()->format('Y-m-d') }}" class="form-input-custom mt-1 block w-full">
                    @error('exchange_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="cth: kesepakatan kedua pihak"
                        class="form-input-custom mt-1 block w-full">
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                <strong>Syarat:</strong> kedua nomor pintu nonaktif, milik anggota aktif yang berbeda, hak &amp; kewajiban beres.
                Setelah tukar, keduanya langsung <strong>aktif</strong> dengan pemegang yang bertukar.
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('door-numbers.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" onclick="return confirm('Tukarkan kedua nomor pintu ini?')" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    Proses Pertukaran
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
