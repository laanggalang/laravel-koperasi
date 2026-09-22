@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('savings.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali ke Data Simpanan">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Kembali ke Data Simpanan</h2>
    </div>

        <!-- Receipt / Nota Container -->
        <div class="card">
            <!-- Nota Header -->
            <div class="bg-gray-50 px-6 py-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Tanda Terima Simpanan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Bukti transaksi simpanan resmi koperasi.</p>
                </div>
                <div class="text-right">
                    <span class="text-sm font-mono font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded border border-indigo-100">
                        TRX-{{ sprintf('SPN-%04d', $saving->id) }}
                    </span>
                </div>
            </div>

            <!-- Nota Body -->
            <div class="p-6 space-y-6">
                <!-- Status & Info Utama -->
                <div class="flex justify-between items-center bg-green-50 border border-green-100 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-500 rounded-full text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-900">Transaksi Berhasil</p>
                            <p class="text-xs text-green-600">Dana telah masuk ke kas koperasi.</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Total Setoran</p>
                        <p class="text-xl font-black text-green-600">Rp {{ number_format($saving->amount ?? $saving->jumlah, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Detail Data Grid -->
                <div class="border-t border-b border-gray-100 py-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Rincian Transaksi</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                        <!-- Nama Anggota -->
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Nama Anggota</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $saving->member->name ?? 'Tidak Diketahui' }}</span>
                        </div>

                        <!-- No Anggota -->
                        <div>
                            <span class="block text-xs font-medium text-gray-400">No. Anggota</span>
                            <span class="text-sm font-mono text-gray-700">{{ $saving->member->member_no ?? '-' }}</span>
                        </div>

                        <!-- Tanggal Transaksi -->
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Tanggal Transaksi</span>
                            <span class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($saving->transaction_date ?? $saving->date)->format('d F Y') }}
                            </span>
                        </div>

                        <!-- Jenis Simpanan -->
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Jenis Simpanan</span>
                            <span class="mt-1 px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 uppercase">
                                Simpanan {{ ucfirst($saving->type) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Keterangan / Deskripsi -->
                <div>
                    <span class="block text-xs font-medium text-gray-400 mb-1">Keterangan / Deskripsi</span>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed min-h-[50px]">
                        {{ $saving->description ?? 'Tidak ada keterangan tambahan.' }}
                    </p>
                </div>
            </div>

            <!-- Nota Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="window.print()" class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                </svg>
                Cetak Nota
            </button>
            </div>
        </div>
    </div>
@endsection
