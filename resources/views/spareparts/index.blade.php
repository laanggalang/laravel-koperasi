@extends('layouts.app')

@section('header')
    Sparepart
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Data Sparepart</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola master barang: oli, ban, aki, dan lainnya.</p>
        </div>
        <a href="{{ route('spareparts.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Sparepart
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Item</p>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['total_item'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aktif</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['available'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Stok Menipis</p>
            <p class="text-2xl font-black {{ $stats['low_stock'] > 0 ? 'text-red-500' : 'text-gray-800' }} mt-1">{{ $stats['low_stock'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nilai Stok (HPP)</p>
            <p class="text-lg font-black text-indigo-600 mt-1">Rp {{ number_format($stats['stock_value'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Daftar Barang</h3>
                <p class="card-subtitle">{{ $spareparts->total() }} item terdaftar</p>
            </div>
            <form method="GET" action="{{ route('spareparts.index') }}" class="w-full sm:w-auto">
                <x-search-box placeholder="Cari nama, kode, kategori..." />
            </form>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <x-sort-link column="kode" label="Kode" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="nama" label="Nama" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="kategori" label="Kategori" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Harga Anggota</th>
                        <th scope="col">Harga Driver</th>
                        <th scope="col">Harga Umum</th>
                        <x-sort-link column="stok_qty" label="Stok" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spareparts as $sp)
                    <tr>
                        <td class="whitespace-nowrap font-mono font-semibold text-indigo-600">{{ $sp->kode }}</td>
                        <td class="whitespace-nowrap text-gray-700 font-medium">
                            {{ $sp->nama }}
                            @if($sp->status !== 'active') <span class="badge bg-gray-100 text-gray-600 ms-1">Nonaktif</span> @endif
                        </td>
                        <td class="whitespace-nowrap"><span class="badge-indigo capitalize">{{ $sp->kategori }}</span></td>
                        <td class="whitespace-nowrap font-bold text-emerald-600">Rp {{ number_format($sp->harga_anggota, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap text-gray-600">Rp {{ number_format($sp->harga_driver, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap text-gray-600">Rp {{ number_format($sp->harga_umum, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap">
                            @if($sp->isLowStock())
                                <span class="badge-danger">{{ $sp->stok_qty }} {{ $sp->satuan }}</span>
                            @else
                                <span class="badge-success">{{ $sp->stok_qty }} {{ $sp->satuan }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="flex gap-1.5">
                                <a href="{{ route('spareparts.show', $sp) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">Detail</a>
                                @if($sp->isActive())
                                    <a href="{{ route('spareparts.restockForm', $sp) }}" class="text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">Restock</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-sm text-center text-gray-400 italic">
                            Belum ada sparepart terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $spareparts->links() }}
        </div>
    </div>
</div>
@endsection
