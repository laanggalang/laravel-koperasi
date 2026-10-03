@extends('layouts.app')

@section('header')
    Nomor Pintu {{ $doorNumber->door_no }}
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('door-numbers.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Detail Nomor Pintu</h2>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-card">
        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-card">
        <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Detail Card -->
    <div class="card">
        <div class="bg-indigo-600 px-6 py-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-500 rounded-full text-white shadow-inner">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white font-mono">{{ $doorNumber->door_no }}</h2>
                    <p class="text-indigo-100 text-sm mt-0.5">Pemegang: <span class="font-bold">{{ $doorNumber->holder_display }}</span></p>
                </div>
            </div>
            <div>
                @if($doorNumber->isActive())
                    <span class="badge bg-emerald-100 text-emerald-800">Aktif</span>
                @elseif($doorNumber->isAvailable())
                    <span class="badge bg-amber-100 text-amber-800">Tersedia</span>
                @else
                    <span class="badge bg-gray-200 text-gray-700">Nonaktif</span>
                @endif
            </div>
        </div>

        <div class="p-6 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Aset</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Anggota Pemegang</label>
                    @if($doorNumber->member)
                        <a href="{{ route('members.show', $doorNumber->member) }}" class="text-base font-medium text-indigo-600 hover:text-indigo-800 mt-1 inline-flex items-center gap-1">
                            {{ $doorNumber->member->name }}
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        </a>
                    @else
                        <p class="text-base font-medium text-gray-900 mt-1">KBP (Koperasi) — menunggu pemegang baru</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengemudi</label>
                    <p class="text-base font-medium text-gray-900 mt-1">{{ $doorNumber->driver_display }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Plat Nomor</label>
                    <p class="text-base font-medium font-mono text-gray-900 mt-1">{{ $doorNumber->plate_no }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Jenis Kendaraan</label>
                    <p class="text-base font-medium text-gray-900 mt-1">{{ $doorNumber->vehicle_type ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Daftar</label>
                    <p class="text-base font-medium text-gray-900 mt-1">{{ $doorNumber->registered_date->format('d-m-Y') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Nonaktif</label>
                    <p class="text-base font-medium {{ $doorNumber->deactivated_date ? 'text-gray-900' : 'text-gray-400 italic' }} mt-1">
                        {{ $doorNumber->deactivated_date?->format('d-m-Y') ?? 'Tidak dinonaktifkan' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Tombol Edit -->
        <div class="px-6 py-3 border-b border-gray-100 flex justify-end">
            <a href="{{ route('door-numbers.edit', $doorNumber) }}" class="btn-secondary">Edit Data Aset</a>
        </div>
    </div>

    <!-- Panel Aksi Kontekstual -->
    <div class="card p-6 bg-gray-50/50">
        <h3 class="text-base font-bold text-gray-800 mb-1">Aksi Nomor Pintu</h3>
        <p class="text-xs text-gray-500 mb-4">Pergantian status tercatat di histori. Hak &amp; kewajiban harus beres sebelum proses.</p>

        @if($doorNumber->isActive())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Nonaktifkan -->
            <form action="{{ route('door-numbers.updateStatus', $doorNumber) }}" method="POST" class="bg-white border border-amber-200 rounded-xl p-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="deactivate">
                <p class="text-sm font-bold text-gray-800">Nonaktifkan</p>
                <p class="text-[11px] text-gray-500 mt-0.5 mb-3">Tetap menempel ke pemegang, tidak dapat ditransfer.</p>
                <input type="date" name="action_date" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" min="{{ $doorNumber->registered_date->format('Y-m-d') }}" class="form-input-custom w-full mb-3" required>
                <button type="submit" onclick="return confirm('Nonaktifkan {{ $doorNumber->door_no }}?')" class="w-full px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition-colors">Nonaktifkan</button>
            </form>

            <!-- Transfer (blocked, harus nonaktif dulu) -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 opacity-60">
                <p class="text-sm font-bold text-gray-400">Transfer / Lepas</p>
                <p class="text-[11px] text-gray-400 mt-0.5 mb-3">Nomor pintu masih aktif. Nonaktifkan dulu untuk transfer atau lepas ke KBP.</p>
                <button type="button" disabled class="w-full px-3 py-2 bg-gray-200 text-gray-400 rounded-lg text-xs font-bold cursor-not-allowed">Terkunci</button>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 opacity-60">
                <p class="text-sm font-bold text-gray-400">Tukar</p>
                <p class="text-[11px] text-gray-400 mt-0.5 mb-3">Pertukaran hanya bisa dilakukan saat nomor pintu nonaktif.</p>
                <button type="button" disabled class="w-full px-3 py-2 bg-gray-200 text-gray-400 rounded-lg text-xs font-bold cursor-not-allowed">Terkunci</button>
            </div>
        </div>
        @elseif($doorNumber->isInactive())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Aktifkan kembali -->
            <form action="{{ route('door-numbers.updateStatus', $doorNumber) }}" method="POST" class="bg-white border border-emerald-200 rounded-xl p-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="activate">
                <p class="text-sm font-bold text-gray-800">Aktifkan Kembali</p>
                <p class="text-[11px] text-gray-500 mt-0.5 mb-3">Kembali beroperasi dengan pemegang saat ini.</p>
                <button type="submit" onclick="return confirm('Aktifkan kembali {{ $doorNumber->door_no }}?')" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors">Aktifkan</button>
            </form>

            <!-- Lepas ke KBP -->
            <form action="{{ route('door-numbers.updateStatus', $doorNumber) }}" method="POST" class="bg-white border border-indigo-200 rounded-xl p-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="release">
                <p class="text-sm font-bold text-gray-800">Lepas ke KBP</p>
                <p class="text-[11px] text-gray-500 mt-0.5 mb-3">Masuk pool koperasi, menunggu pemegang baru.</p>
                <input type="date" name="action_date" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" min="{{ $doorNumber->registered_date->format('Y-m-d') }}" class="form-input-custom w-full mb-3" required>
                <button type="submit" onclick="return confirm('Lepas {{ $doorNumber->door_no }} ke KBP? Pemegang akan dikosongkan.')" class="w-full px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-colors">Lepaskan</button>
            </form>

            <!-- Transfer -->
            <a href="{{ route('door-numbers.transferForm', $doorNumber) }}" class="bg-white border border-indigo-200 rounded-xl p-4 hover:border-indigo-400 transition-colors">
                <p class="text-sm font-bold text-gray-800">Transfer</p>
                <p class="text-[11px] text-gray-500 mt-0.5 mb-3">Pindahkan langsung ke anggota aktif lain, otomatis aktif kembali.</p>
                <span class="inline-block w-full text-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-colors">Pilih Anggota →</span>
            </a>
        </div>

        <!-- Panel Tukar -->
        <div class="mt-4 bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-sm font-bold text-gray-800 mb-2">Pertukaran Antar Anggota</p>
            @if($exchangeCandidates->isEmpty())
                <p class="text-xs text-gray-400 italic">Tidak ada nomor pintu nonaktif lain yang memenuhi syarat (milik anggota aktif lain).</p>
            @else
                <form action="{{ route('door-numbers.exchange') }}" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-3">
                    @csrf
                    <input type="hidden" name="door_number_a" value="{{ $doorNumber->id }}">
                    <input type="hidden" name="exchange_date" value="{{ now()->format('Y-m-d') }}">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tukar dengan</label>
                        <select name="door_number_b" class="form-input-custom w-full" required>
                            <option value="">-- Pilih nomor pintu nonaktif --</option>
                            @foreach($exchangeCandidates as $c)
                                <option value="{{ $c->id }}">{{ $c->door_no }} — {{ $c->member->name }} ({{ $c->plate_no }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">Kedua nomor pintu akan langsung aktif dengan pemegang yang bertukar.</p>
                    </div>
                    <button type="submit" onclick="return confirm('Tukarkan {{ $doorNumber->door_no }} dengan nomor pintu yang dipilih?')" class="btn-secondary whitespace-nowrap">Tukar Sekarang</button>
                </form>
            @endif
        </div>
        @else
        <!-- AVAILABLE -->
        <div class="bg-white border border-indigo-200 rounded-xl p-4">
            <p class="text-sm font-bold text-gray-800 mb-0.5">Status: Tersedia di KBP</p>
            <p class="text-xs text-gray-500 mb-3">Nomor pintu berada di pool koperasi. Transfer ke anggota aktif untuk mengoperasikannya kembali.</p>
            <a href="{{ route('door-numbers.transferForm', $doorNumber) }}" class="btn-primary">
                Transfer ke Anggota →
            </a>
        </div>
        @endif
    </div>

    <!-- Timeline Histori -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Riwayat Perpindahan</h3>
                <p class="card-subtitle">Jejak lengkap semua aksi pada nomor pintu ini</p>
            </div>
        </div>
        <div class="p-6">
            @if($doorNumber->histories->isEmpty())
                <p class="text-sm text-gray-400 italic text-center py-4">Belum ada histori.</p>
            @else
                <ol class="relative border-s-2 border-gray-100 ms-3 space-y-6">
                    @foreach($doorNumber->histories as $h)
                    <li class="ms-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-4 ring-white
                            {{ $h->action === \App\Models\DoorNumberHistory::ACTION_REGISTERED ? 'bg-indigo-100 text-indigo-600' :
                               ($h->action === \App\Models\DoorNumberHistory::ACTION_EXCHANGED ? 'bg-purple-100 text-purple-600' :
                               ($h->action === \App\Models\DoorNumberHistory::ACTION_TRANSFERRED ? 'bg-emerald-100 text-emerald-600' :
                               ($h->action === \App\Models\DoorNumberHistory::ACTION_RELEASED ? 'bg-amber-100 text-amber-600' :
                               'bg-gray-100 text-gray-500'))) }}">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                        </span>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-bold text-gray-800">{{ $h->action_label }}</span>
                            <span class="text-xs text-gray-400">{{ $h->action_date->format('d-m-Y') }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-0.5">
                            @if($h->from_member_id !== $h->to_member_id)
                                {{ $h->fromMember?->name ?? 'KBP' }} → <span class="font-semibold">{{ $h->toMember?->name ?? 'KBP' }}</span>
                            @else
                                {{ $h->fromMember?->name ?? 'KBP' }}
                            @endif
                            <span class="text-gray-300 mx-1">•</span>
                            oleh {{ $h->performer?->name ?? '-' }}
                        </p>
                        @if($h->notes)
                            <p class="text-xs text-gray-400 mt-0.5 italic">{{ $h->notes }}</p>
                        @endif
                    </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>
</div>
@endsection
