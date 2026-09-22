@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Tombol Kembali & Notifikasi -->
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('loans.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                ← Kembali ke Daftar Pinjaman
            </a>
            @if(session('success'))
                <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full font-medium">{{ session('success') }}</span>
            @endif
        </div>

        <!-- Main Card Container (Detail Pinjaman) -->
        <div class="card mb-6">
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Pinjaman {{ $loan->loan_no }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Bukti transaksi pinjaman resmi koperasi.</p>
                </div>
                <div>
                    @php
                        $status = strtolower($loan->status);
                        $colorClass = 'bg-yellow-100 text-yellow-800';
                        if ($status == 'active' || $status == 'aktif') $colorClass = 'bg-green-100 text-green-800';
                        if ($status == 'paid' || $status == 'lunas') $colorClass = 'bg-blue-100 text-blue-800';
                        if ($status == 'cancelled') $colorClass = 'bg-red-100 text-red-800';
                    @endphp
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $colorClass }} uppercase tracking-wider">
                        {{ $loan->status }}
                    </span>
                </div>
            </div>

            <!-- Body Information Grid -->
            <div class="p-6 space-y-6">
                <!-- Rincian Akun -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <span class="block text-xs font-medium text-gray-400 uppercase tracking-wider">Nama Anggota</span>
                    <span class="text-lg font-bold text-gray-900">{{ $loan->member->name }}</span>
                </div>

                <!-- Perhitungan Finansial -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Rincian Finansial Pinjaman</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div class="p-3 bg-white border rounded-lg shadow-sm">
                            <span class="block text-xs text-gray-400 font-medium">Pinjaman Pokok</span>
                            <span class="text-base font-bold text-gray-800">Rp {{ number_format($loan->principal, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-white border rounded-lg shadow-sm bg-red-50 border-red-100">
                            <span class="block text-xs text-red-500 font-medium">Total Pembayaran</span>
                            <span class="text-base font-black text-red-600">Rp {{ number_format($loan->total_payment, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-white border rounded-lg shadow-sm bg-green-50 border-green-100">
                            <span class="block text-xs text-green-600 font-medium">Angsuran / Bulan</span>
                            <span class="text-base font-black text-green-700">Rp {{ number_format($loan->monthly_payment, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sisa Saldo Tagihan -->
                <div class="flex justify-between items-center bg-indigo-50 border border-indigo-100 rounded-lg p-4 shadow-sm">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Sisa Tagihan (Sisa)</p>
                        <p class="text-xs text-indigo-500">Kewajiban pinjaman yang belum dibayarkan.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-black text-indigo-700">Rp {{ number_format($loan->remaining_balance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 💡 BLOK BARU: TOMBOL PERSETUJUAN KREDIT (HANYA UNTUK ADMIN & PENGURUS) -->
                @if(in_array(auth()->user()->role, ['admin', 'pengurus']) && strtolower($loan->status) === 'pending')
        <div class="card p-6 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between !bg-amber-50/50 !border-amber-200">
            <div class="mb-4 sm:mb-0">
                <h4 class="text-sm font-bold text-amber-900 uppercase tracking-wider">Panel Persetujuan Kredit Pengurus</h4>
                <p class="text-xs text-amber-700 mt-0.5">Tinjau pengajuan ini dengan saksama sebelum memberikan keputusan persetujuan dana.</p>
            </div>
            <div class="flex space-x-3">
                <!-- Tombol Setujui (Mengubah status menjadi active) -->
                <form action="{{ route('loans.update', $loan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pengajuan pinjaman ini?')" 
                        class="btn-success">
                        ✔ Setujui (Aktifkan)
                    </button>
                </form>

                <!-- Tombol Tolak / Batalkan (Mengubah status menjadi cancelled) -->
                <form action="{{ route('loans.update', $loan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin MENOLAK/MEMBATALKAN pengajuan pinjaman ini?')" 
                        class="btn-danger">
                        ❌ Tolak Pengajuan
                    </button>
                </form>
            </div>
        </div>
        @endif


        <!-- History Cicilan Container -->
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-base font-bold text-gray-800">Riwayat Angsuran</h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="table-grid min-w-full">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->installments as $i)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono font-bold">{{ $i->installment_no }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i->paid_date->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">Rp {{ number_format($i->amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        @if($loan->installments->isEmpty())
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-sm text-center text-gray-400 italic bg-white">
                                Belum ada catatan pembayaran angsuran untuk pinjaman ini.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection