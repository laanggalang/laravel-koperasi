@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <div class="mb-4">
            <a href="{{ route('savings.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                ← Kembali ke Data Simpanan
            </a>
        </div>

        <!-- Receipt / Nota Container -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
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
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    🖨️ Cetak Nota
                </button>
            </div>
        </div>

    </div>
</div>
@endsection
