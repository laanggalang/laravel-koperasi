@extends('layouts.app')

@section('header')
    Penjualan Sparepart
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Riwayat Penjualan Sparepart</h2>
            <p class="text-sm text-gray-500 mt-0.5">Semua transaksi penjualan barang koperasi.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            Kasir Baru
        </a>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Daftar Transaksi</h3>
                <p class="card-subtitle">{{ $sales->total() }} struk tercatat</p>
            </div>
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap gap-2 w-full sm:w-auto">
                <x-search-box placeholder="Cari invoice, nama..." />
                <select name="status" class="form-input-custom text-xs">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ $filterStatus === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="partial" {{ $filterStatus === 'partial' ? 'selected' : '' }}>Bayar Sebagian</option>
                    <option value="debt" {{ $filterStatus === 'debt' ? 'selected' : '' }}>Utang</option>
                </select>
                <select name="buyer_type" class="form-input-custom text-xs">
                    <option value="">Semua Pembeli</option>
                    <option value="anggota" {{ $filterBuyer === 'anggota' ? 'selected' : '' }}>Anggota</option>
                    <option value="driver" {{ $filterBuyer === 'driver' ? 'selected' : '' }}>Driver</option>
                    <option value="umum" {{ $filterBuyer === 'umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </form>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <x-sort-link column="invoice_no" label="Invoice" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="buyer" label="Pembeli" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Tipe</th>
                        <x-sort-link column="total" label="Total" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Dibayar</th>
                        <x-sort-link column="status" label="Status" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="sale_date" label="Tanggal" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td class="whitespace-nowrap font-mono font-semibold text-indigo-600 text-xs">{{ $sale->invoice_no }}</td>
                        <td class="whitespace-nowrap text-gray-700 font-medium">{{ $sale->buyer_display }}</td>
                        <td class="whitespace-nowrap"><span class="badge-indigo">{{ $sale->buyer_label }}</span></td>
                        <td class="whitespace-nowrap font-bold text-gray-800">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap text-emerald-600">Rp {{ number_format($sale->total_paid, 0, ',', '.') }}</td>
                        <td class="whitespace-nowrap">
                            @if($sale->status === 'paid')
                                <span class="badge-success">Lunas</span>
                            @elseif($sale->status === 'partial')
                                <span class="badge-warning">Sebagian</span>
                            @else
                                <span class="badge-danger">Utang</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap text-gray-500">{{ $sale->sale_date->format('d-m-Y') }}</td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">Struk</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-sm text-center text-gray-400 italic">
                            Belum ada transaksi penjualan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
