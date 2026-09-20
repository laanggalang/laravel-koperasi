@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6 bg-white p-6 rounded-lg shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Data Simpanan Anggota</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola dan pantau seluruh transaksi simpanan anggota koperasi.</p>
            </div>
            <a href="{{ route('savings.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-950 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                + Tambah Transaksi
            </a>
        </div>

        <!-- Table Card Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 overflow-x-auto">
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ sprintf('SPN-%04d', $s->id) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $s->member->name ?? 'Tidak Diketahui' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($s->date ?? $s->created_at)->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 uppercase">
                                    {{ $s->type ?? 'Wajib/Pokok' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                Rp {{ number_format($s->amount ?? $s->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('savings.show', $s->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded transition-colors">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 whitespace-nowrap text-sm text-center text-gray-400 italic">
                                Belum ada data transaksi simpanan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination Section -->
                <div class="mt-6">
                    {{ $savings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
