@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <div class="mb-4">
            <a href="{{ route('installments.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                ← Kembali ke Riwayat Angsuran
            </a>
        </div>

        <!-- Kwitansi Container -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            <!-- Header Nota -->
            <div class="bg-gray-50 px-6 py-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Kwitansi Pembayaran Angsuran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Bukti pembayaran cicilan resmi koperasi.</p>
                </div>
                <div class="text-right">
                    <span class="text-sm font-mono font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded border border-indigo-100">
                        AGS-{{ sprintf('%04d', $installment->id) }}
                    </span>
                </div>
            </div>

            <!-- Body Nota -->
            <div class="p-6 space-y-6">
                <!-- Banner Berhasil -->
                <div class="flex justify-between items-center bg-green-50 border border-green-100 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-500 rounded-full text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-900">Pembayaran Diterima</p>
                            <p class="text-xs text-green-600">Angsuran Ke-{{ $installment->installment_no }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Jumlah Bayar</p>
                        <p class="text-xl font-black text-green-600">Rp {{ number_format($installment->amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Rincian Data -->
                <div class="border-t border-b border-gray-100 py-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Rincian Transaksi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Nama Anggota</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $installment->loan->member->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Kode Pinjaman</span>
                            <span class="text-sm font-mono font-bold text-indigo-600">{{ $installment->loan->loan_no }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Tanggal Bayar</span>
                            <span class="text-sm text-gray-900">{{ $installment->paid_date->format('d F Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Sisa Pinjaman Anggota</span>
                            <span class="text-sm font-bold text-red-500">Rp {{ number_format($installment->loan->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <span class="block text-xs font-medium text-gray-400 mb-1">Keterangan</span>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed min-h-[50px]">
                        {{ $installment->notes ?? 'Pembayaran angsuran berjalan lancar.' }}
                    </p>
                </div>
            </div>

            <!-- Footer Nota -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-colors">
                    🖨️ Cetak Kwitansi
                </button>
            </div>
        </div>

    </div>
</div>
@endsection
