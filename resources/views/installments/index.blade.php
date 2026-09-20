@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6 bg-white p-6 rounded-lg shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Riwayat Angsuran (Cicilan)</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau dan kelola seluruh catatan transaksi pembayaran angsuran pinjaman anggota.</p>
            </div>
            <a href="{{ route('installments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-950 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                + Catat Angsuran
            </a>
        </div>

        <!-- Notifikasi Sukses -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm flex items-center justify-between">
            <span class="text-sm font-medium text-green-800">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Table Card Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            <div class="p-6 bg-white border-b border-gray-200 overflow-x-auto">
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-indigo-600">
                                {{ $i->loan->loan_no }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                {{ $i->loan->member->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center sm:text-left">
                                <span class="px-2.5 py-1 text-xs font-mono font-bold rounded bg-gray-100 text-gray-800 border">
                                    {{ $i->installment_no }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $i->paid_date->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-green-600">
                                Rp {{ number_format($i->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 whitespace-nowrap text-sm text-center text-gray-400 italic bg-white">
                                Belum ada riwayat transaksi pembayaran angsuran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination Section -->
                <div class="mt-6">
                    {{ $installments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection