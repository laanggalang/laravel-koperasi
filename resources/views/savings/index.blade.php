@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Data Simpanan Anggota</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola dan pantau seluruh transaksi simpanan anggota koperasi.</p>
        </div>
        <a href="{{ route('savings.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Transaksi
        </a>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Riwayat Simpanan</h3>
                <p class="card-subtitle">{{ $savings->total() }} transaksi tercatat</p>
            </div>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <x-sort-link column="id" label="No. Transaksi" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="member" label="Nama Anggota" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="transaction_date" label="Tanggal" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="type" label="Jenis Simpanan" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="amount" label="Jumlah (Rp)" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($savings as $s)
                    <tr>
                        <td class="whitespace-nowrap font-mono font-semibold text-gray-900">{{ sprintf('SPN-%04d', $s->id) }}</td>
                        <td class="whitespace-nowrap text-gray-700">{{ $s->member->name ?? 'Tidak Diketahui' }}</td>
                        <td class="whitespace-nowrap text-gray-500">
                            {{ \Carbon\Carbon::parse($s->transaction_date ?? $s->created_at)->format('d-m-Y') }}
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="badge-indigo">{{ $s->type ?? 'Wajib/Pokok' }}</span>
                        </td>
                        <td class="whitespace-nowrap font-bold text-emerald-600">
                            Rp {{ number_format($s->amount, 0, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('savings.show', $s) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-sm text-center text-gray-400 italic">
                            Belum ada data transaksi simpanan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $savings->links() }}
        </div>
    </div>
</div>
@endsection
