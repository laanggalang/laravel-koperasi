<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Banner Selamat Datang -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-500 p-6 sm:p-8 text-white shadow-lg shadow-indigo-200">
            <div class="absolute -end-8 -top-8 w-40 h-40 rounded-full bg-white/10"></div>
            <div class="absolute end-20 -bottom-10 w-32 h-32 rounded-full bg-white/10"></div>
            <div class="relative">
                <h2 class="text-xl sm:text-2xl font-black">Selamat Datang Kembali, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-sm text-indigo-100 mt-1.5 max-w-2xl">Berikut ringkasan performa finansial koperasi periode ini. Semua sistem berjalan normal.</p>
            </div>
        </div>

        <!-- Grid Ringkasan Statistik Finansial -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- Card 1: Anggota Aktif -->
            <div class="card p-5 flex items-center justify-between hover:shadow-card-hover transition-shadow">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Anggota Aktif</span>
                    <span class="block text-3xl font-black text-gray-800 mt-2">{{ $memberCount }}</span>
                    <span class="block text-xs text-gray-400 mt-1">orang terdaftar</span>
                </div>
                <div class="stat-icon bg-blue-50 text-blue-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Kas Simpanan -->
            <div class="card p-5 flex items-center justify-between hover:shadow-card-hover transition-shadow">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Kas Simpanan</span>
                    <span class="block text-2xl font-black text-gray-800 mt-2">Rp {{ number_format($savingTotal, 0, ',', '.') }}</span>
                    <span class="block text-xs text-emerald-600 font-semibold mt-1">total terkumpul</span>
                </div>
                <div class="stat-icon bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Total Pinjaman -->
            <div class="card p-5 flex items-center justify-between hover:shadow-card-hover transition-shadow">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pinjaman</span>
                    <span class="block text-2xl font-black text-gray-800 mt-2">Rp {{ number_format($loanTotal, 0, ',', '.') }}</span>
                    <span class="block text-xs text-red-500 font-semibold mt-1">disalurkan</span>
                </div>
                <div class="stat-icon bg-red-50 text-red-500">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 4: Angsuran Masuk -->
            <div class="card p-5 flex items-center justify-between hover:shadow-card-hover transition-shadow">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Angsuran Masuk</span>
                    <span class="block text-2xl font-black text-gray-800 mt-2">Rp {{ number_format($installmentTotal, 0, ',', '.') }}</span>
                    <span class="block text-xs text-indigo-600 font-semibold mt-1">total diterima</span>
                </div>
                <div class="stat-icon bg-indigo-50 text-indigo-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Row: Chart + Outstanding -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Chart Tren -->
            <div class="card lg:col-span-2">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Tren Transaksi 6 Bulan Terakhir</h3>
                        <p class="card-subtitle">Perbandingan penerimaan simpanan vs angsuran per bulan</p>
                    </div>
                    <span class="badge-indigo">Update {{ now()->translatedFormat('d M Y') }}</span>
                </div>
                <div class="p-5">
                    <div style="height: 300px">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Outstanding -->
            <div class="card p-6 flex flex-col">
                <div class="stat-icon bg-amber-50 text-amber-600 w-fit">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mt-4">Sisa Tagihan Berjalan</h3>
                <p class="text-3xl font-black text-amber-600 mt-2 font-mono">Rp {{ number_format($loanOutstanding, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">Total modal koperasi yang masih dipinjam dan berada di tangan anggota (pinjaman berstatus aktif).</p>
                <a href="{{ route('loans.index') }}" class="mt-auto pt-4">
                    <span class="btn-secondary w-full">Lihat Semua Pinjaman →</span>
                </a>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="card p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Navigasi Pintasan Cepat</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('members.index') }}" class="group p-4 bg-gray-50 hover:bg-blue-50 rounded-xl border border-gray-100 hover:border-blue-200 transition-all text-center">
                    <span class="block text-2xl mb-1.5">👥</span>
                    <span class="text-sm font-semibold text-gray-700 group-hover:text-blue-700">Data Anggota</span>
                </a>
                <a href="{{ route('savings.index') }}" class="group p-4 bg-gray-50 hover:bg-emerald-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition-all text-center">
                    <span class="block text-2xl mb-1.5">💰</span>
                    <span class="text-sm font-semibold text-gray-700 group-hover:text-emerald-700">Kas Simpanan</span>
                </a>
                <a href="{{ route('loans.index') }}" class="group p-4 bg-gray-50 hover:bg-red-50 rounded-xl border border-gray-100 hover:border-red-200 transition-all text-center">
                    <span class="block text-2xl mb-1.5">📉</span>
                    <span class="text-sm font-semibold text-gray-700 group-hover:text-red-700">Pinjaman Kredit</span>
                </a>
                <a href="{{ route('installments.index') }}" class="group p-4 bg-gray-50 hover:bg-indigo-50 rounded-xl border border-gray-100 hover:border-indigo-200 transition-all text-center">
                    <span class="block text-2xl mb-1.5">📋</span>
                    <span class="text-sm font-semibold text-gray-700 group-hover:text-indigo-700">Riwayat Angsuran</span>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('trendChart');
            if (!ctx || !window.Chart) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Simpanan',
                            data: @json($chartSavings),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.08)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#4f46e5',
                        },
                        {
                            label: 'Angsuran',
                            data: @json($chartInstallments),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 20 },
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: (c) => ' ' + c.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(c.parsed.y),
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            border: { display: false },
                            grid: { color: '#f3f4f6' },
                            ticks: {
                                callback: (v) => v >= 1000000 ? (v / 1000000) + 'jt' : v >= 1000 ? (v / 1000) + 'rb' : v,
                            },
                        },
                    },
                },
            });
        });
    </script>
    @endpush
</x-app-layout>
