@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Riwayat Angsuran</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pantau dan kelola seluruh catatan pembayaran angsuran pinjaman anggota.</p>
        </div>
        <a href="{{ route('installments.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Catat Angsuran
        </a>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-card">
        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Catatan Angsuran (Cicilan)</h3>
                <p class="card-subtitle">{{ $installments->total() }} pembayaran tercatat</p>
            </div>
            <form method="GET" action="{{ route('installments.index') }}" class="w-full sm:w-auto">
                <x-search-box placeholder="Cari no. pinjaman, anggota..." />
            </form>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <x-sort-link column="loan_no" label="No Pinjaman" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="member" label="Anggota" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="installment_no" label="Angsuran Ke" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="paid_date" label="Tanggal" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="amount" label="Nominal" :sortBy="$sortBy" :sortDir="$sortDir" />
                    </tr>
                </thead>
                <tbody>
                    @forelse($installments as $i)
                    <tr>
                        <td class="whitespace-nowrap font-mono font-bold text-indigo-600">
                            {{ $i->loan->loan_no }}
                        </td>
                        <td class="whitespace-nowrap text-gray-700 font-medium">
                            {{ $i->loan->member->name }}
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-gray-100 text-gray-800 border border-gray-200">
                                {{ $i->installment_no }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap text-gray-500">
                            {{ $i->paid_date->format('d-m-Y') }}
                        </td>
                        <td class="whitespace-nowrap font-black text-emerald-600">
                            Rp {{ number_format($i->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-sm text-center text-gray-400 italic">
                            Belum ada riwayat transaksi pembayaran angsuran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $installments->links() }}
        </div>
    </div>
</div>
@endsection
