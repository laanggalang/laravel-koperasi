@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6 bg-white p-6 rounded-lg shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar Anggota</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data seluruh anggota koperasi aktif di sini.</p>
            </div>
            <a href="{{ route('members.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-950 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                + Tambah Anggota
            </a>
        </div>

        <!-- Table Card Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 overflow-x-auto">
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $m->member_no }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $m->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $m->phone ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase">
                                    {{ $m->status ?? 'Active' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('members.show', $m) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded transition-colors mr-2">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Section -->
                <div class="mt-6">
                    {{ $members->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
