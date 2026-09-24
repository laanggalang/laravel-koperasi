@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Anggota</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data seluruh anggota koperasi aktif di sini.</p>
        </div>
        <a href="{{ route('members.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Anggota
        </a>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Data Anggota Koperasi</h3>
                <p class="card-subtitle">{{ $members->total() }} anggota terdaftar</p>
            </div>
            <form method="GET" action="{{ route('members.index') }}" class="w-full sm:w-auto">
                <x-search-box placeholder="Cari nama, no. anggota, telepon..." />
            </form>
        </div>
        <div class="overflow-x-auto px-6 py-5">
            <table class="table-grid min-w-full">
                <thead>
                    <tr>
                        <x-sort-link column="member_no" label="No Anggota" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="name" label="Nama" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="phone" label="Telepon" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <x-sort-link column="status" label="Status" :sortBy="$sortBy" :sortDir="$sortDir" />
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $m)
                    <tr>
                        <td class="whitespace-nowrap font-mono font-semibold text-gray-900">{{ $m->member_no }}</td>
                        <td class="whitespace-nowrap text-gray-700">{{ $m->name }}</td>
                        <td class="whitespace-nowrap text-gray-500">{{ $m->phone ?? '-' }}</td>
                        <td class="whitespace-nowrap">
                            <span class="{{ ($m->status ?? 'active') === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ $m->status ?? 'Active' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('members.show', $m) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $members->links() }}
        </div>
    </div>
</div>
@endsection
