@extends('layouts.app')

@section('header')
    Detail Sparepart
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('spareparts.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Detail Sparepart</h2>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-card">
        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Detail Card -->
    <div class="card">
        <div class="bg-indigo-600 px-6 py-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-500 rounded-full text-white shadow-inner">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $sparepart->nama }}</h2>
                    <p class="text-indigo-100 text-sm font-mono mt-0.5">{{ $sparepart->kode }} • {{ ucfirst($sparepart->kategori) }}</p>
                </div>
            </div>
            <div>
                @if($sparepart->isActive())
                    <span class="badge bg-emerald-100 text-emerald-800">Aktif</span>
                @else
                    <span class="badge bg-gray-200 text-gray-700">Nonaktif</span>
                @endif
            </div>
        </div>

        <div class="p-6">
            <!-- Stok highlight -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="p-4 rounded-xl border {{ $sparepart->isLowStock() ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }}">
                    <p class="text-xs font-semibold {{ $sparepart->isLowStock() ? 'text-red-500' : 'text-emerald-600' }} uppercase tracking-wider">Stok Saat Ini</p>
                    <p class="text-2xl font-black {{ $sparepart->isLowStock() ? 'text-red-600' : 'text-emerald-700' }} mt-1">{{ $sparepart->stok_qty }} <span class="text-sm font-semibold">{{ $sparepart->satuan }}</span></p>
                    @if($sparepart->isLowStock())
                        <p class="text-[11px] text-red-500 mt-0.5">⚠ Stok menipis (min. {{ $sparepart->stok_minimal }})</p>
                    @endif
                </div>
                <div class="p-4 rounded-xl border border-gray-200 bg-white">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nilai Stok (HPP)</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($sparepart->stock_value, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-xl border border-gray-200 bg-white">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stok Minimal</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ $sparepart->stok_minimal }} <span class="text-sm font-semibold">{{ $sparepart->satuan }}</span></p>
                </div>
            </div>

            <!-- Harga -->
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Harga per {{ $sparepart->satuan }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="p-3 rounded-lg bg-gray-50 border">
                    <p class="text-xs text-gray-500">Harga Beli (HPP)</p>
                    <p class="text-base font-bold text-gray-800">Rp {{ number_format($sparepart->harga_beli, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                    <p class="text-xs text-emerald-600">Anggota</p>
                    <p class="text-base font-black text-emerald-700">Rp {{ number_format($sparepart->harga_anggota, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-lg bg-blue-50 border border-blue-100">
                    <p class="text-xs text-blue-600">Driver</p>
                    <p class="text-base font-black text-blue-700">Rp {{ number_format($sparepart->harga_driver, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border">
                    <p class="text-xs text-gray-500">Umum</p>
                    <p class="text-base font-black text-gray-800">Rp {{ number_format($sparepart->harga_umum, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Aksi -->
            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
                <a href="{{ route('spareparts.edit', $sparepart) }}" class="btn-secondary">Edit Data</a>
                @if($sparepart->isActive())
                    <a href="{{ route('spareparts.restockForm', $sparepart) }}" class="btn-primary">Restock</a>
                    <form action="{{ route('spareparts.updateStatus', $sparepart) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="inactive">
                        <button type="submit" onclick="return confirm('Nonaktifkan {{ $sparepart->nama }}? Barang tidak akan muncul di kasir.')" class="btn-danger">Nonaktifkan</button>
                    </form>
                @else
                    <form action="{{ route('spareparts.updateStatus', $sparepart) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="active">
                        <button type="submit" onclick="return confirm('Aktifkan kembali {{ $sparepart->nama }}?')" class="btn-success">Aktifkan Kembali</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat stok -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Riwayat Stok</h3>
                <p class="card-subtitle">Audit trail masuk/keluar/penyesuaian</p>
            </div>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            @if($sparepart->stockMovements->isEmpty())
                <p class="text-sm text-gray-400 italic text-center py-4">Belum ada riwayat stok.</p>
            @else
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Tipe</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Referensi</th>
                        <th scope="col">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sparepart->stockMovements as $m)
                    <tr>
                        <td class="whitespace-nowrap text-gray-500">{{ $m->created_at->format('d-m-Y H:i') }}</td>
                        <td class="whitespace-nowrap">
                            <span class="{{ $m->type === 'in' ? 'badge-success' : ($m->type === 'out' ? 'badge-danger' : 'badge-warning') }}">{{ $m->type_label }}</span>
                        </td>
                        <td class="whitespace-nowrap font-mono font-bold {{ $m->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $m->type === 'out' ? '-' : '+' }}{{ $m->qty }}
                        </td>
                        <td class="whitespace-nowrap text-gray-600 font-mono text-xs">{{ $m->stock_before }} → {{ $m->stock_after }}</td>
                        <td class="whitespace-nowrap text-gray-500 text-xs">{{ $m->reference }}@if($m->notes) — {{ $m->notes }} @endif</td>
                        <td class="whitespace-nowrap text-gray-500 text-xs">{{ $m->performer?->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
