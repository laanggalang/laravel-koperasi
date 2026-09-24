@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Data Pinjaman Anggota</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola, tinjau, dan pantau pengajuan serta status pinjaman anggota.</p>
        </div>
        <a href="{{ route('loans.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Ajukan Pinjaman
        </a>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Daftar Pinjaman</h3>
                <p class="card-subtitle">{{ $loans->total() }} transaksi pinjaman</p>
            </div>
            <form method="GET" action="{{ route('loans.index') }}" class="w-full sm:w-auto">
                <x-search-box placeholder="Cari kode, nama, status..." />
            </form>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <x-sort-link column="loan_no" label="No. Kode" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="member" label="Nama Anggota" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="start_date" label="Tanggal Pinjam" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="principal" label="Jumlah Pokok (Rp)" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="tenor" label="Tenor" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="status" label="Status" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $l)
                    <tr>
                        <td class="whitespace-nowrap font-mono font-semibold text-gray-900">
                            LN-{{ sprintf('%04d', $l->id) }}
                        </td>
                        <td class="whitespace-nowrap text-gray-700">
                            {{ $l->member->name ?? 'Tidak Diketahui' }}
                        </td>
                        <td class="whitespace-nowrap text-gray-500">
                            {{ \Carbon\Carbon::parse($l->start_date ?? $l->created_at)->format('d-m-Y') }}
                        </td>
                        <td class="whitespace-nowrap font-bold text-red-600">
                            Rp {{ number_format($l->principal, 0, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap text-gray-700">
                            {{ $l->tenor ?? 0 }} Bulan
                        </td>
                        <td class="whitespace-nowrap">
                            @php
                                $status = strtolower($l->status ?? 'pending');
                                $badgeClass = match ($status) {
                                    'active', 'aktif' => 'badge-success',
                                    'paid', 'lunas' => 'badge-info',
                                    'rejected', 'ditolak', 'cancelled' => 'badge-danger',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $l->status ?? 'Pending' }}</span>
                        </td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('loans.show', $l) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-sm text-center text-gray-400 italic">
                            Belum ada data transaksi pinjaman.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $loans->links() }}
        </div>
    </div>
</div>
@endsection
