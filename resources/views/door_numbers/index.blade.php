@extends('layouts.app')

@section('header')
    Nomor Pintu
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Data Nomor Pintu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola aset nomor pintu (pengemudi) milik koperasi.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('door-numbers.claimForm') }}" class="btn-success">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                Ambil
            </a>
            <a href="{{ route('door-numbers.exchangeForm') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                </svg>
                Tukar
            </a>
            <a href="{{ route('door-numbers.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Daftar Nomor Pintu</h3>
                <p class="card-subtitle">{{ $doorNumbers->total() }} Nomor Pintu Terdaftar</p>
                <p class="card-subtitle">{{ $activeDoorNumbers }} Nomor Pintu Aktif</p>
                <p class="card-subtitle">{{ $availableDoorNumbers }} Nomor Pintu Tersedia</p>
            </div>
            <form method="GET" action="{{ route('door-numbers.index') }}" class="w-full sm:w-auto">
                <x-search-box placeholder="Cari no. pintu, plat, pemegang..." />
            </form>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <x-sort-link column="door_no" label="No. Pintu" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="member" label="Pemegang" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="driver" label="Pengemudi" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="plate" label="Plat" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Kendaraan</th>
                        <x-sort-link column="status" label="Status" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="registered_date" label="Tgl Daftar" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doorNumbers as $np)
                    <tr>
                        <td class="whitespace-nowrap font-mono font-bold text-indigo-600">{{ $np->door_no }}</td>
                        <td class="whitespace-nowrap text-gray-700 font-medium">
                            {{ $np->holder_display }}
                        </td>
                        <td class="whitespace-nowrap text-gray-700">
                            {{ $np->driver_display }}
                        </td>
                        <td class="whitespace-nowrap font-mono text-xs text-gray-600">{{ $np->plate_no }}</td>
                        <td class="whitespace-nowrap text-gray-500">{{ $np->vehicle_type ?? '-' }}</td>
                        <td class="whitespace-nowrap">
                            @if($np->isActive())
                                <span class="badge-success">Aktif</span>
                            @elseif($np->isAvailable())
                                <span class="badge-warning">Tersedia</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap text-gray-500">{{ $np->registered_date->format('d-m-Y') }}</td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('door-numbers.show', $np) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-sm text-center text-gray-400 italic">
                            Belum ada nomor pintu terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $doorNumbers->links() }}
        </div>
    </div>
</div>
@endsection
