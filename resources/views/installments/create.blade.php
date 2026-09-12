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

        <!-- Form Card Container -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            <!-- Card Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">Catat Pembayaran Angsuran</h2>
                <p class="text-xs text-gray-500 mt-0.5">Pilih kode pinjaman anggota dan masukkan jumlah nominal angsuran yang dibayarkan.</p>
            </div>

            <!-- Form Body -->
            <form action="{{ route('installments.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Dropdown Pilih Pinjaman Aktif -->
                <div>
                    <label for="loan_id" class="block text-sm font-medium text-gray-700">Pilih Kode Pinjaman Anggota</label>
                    <select name="loan_id" id="loan_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="" data-monthly="0" data-remaining="0">-- Pilih Kode Pinjaman --</option>
                        @foreach($loans as $loan)
                            <option value="{{ $loan->id }}" 
                                    data-monthly="{{ $loan->monthly_payment }}" 
                                    data-remaining="{{ $loan->remaining_balance }}"
                                    {{ old('loan_id') == $loan->id ? 'selected' : '' }}>
                                {{ $loan->loan_no }} - {{ $loan->member->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('loan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Info Box Ringkas (Mirip loans/show) -->
                <div id="info-box" class="hidden grid grid-cols-2 gap-4 text-center bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-300">
                    <div class="p-3 bg-white border rounded-lg shadow-sm bg-green-50 border-green-100">
                        <span class="block text-xs text-green-600 font-medium">Angsuran / Bulan</span>
                        <span id="txt-monthly" class="text-base font-black text-green-700">Rp 0</span>
                    </div>
                    <div class="p-3 bg-white border rounded-lg shadow-sm bg-indigo-50 border-indigo-100">
                        <span class="block text-xs text-indigo-500 font-medium">Sisa Tagihan</span>
                        <span id="txt-remaining" class="text-base font-black text-indigo-700">Rp 0</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Input Jumlah Bayar / Nominal -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah Nominal Bayar (Rp)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1"
                                placeholder="0"
                                class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">*Jika nominal bayar melebihi sisa tagihan, sistem otomatis memotong pas sesuai sisa saldo.</p>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Input Tanggal Bayar -->
                    <div>
                        <label for="paid_date" class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                        <input type="date" name="paid_date" id="paid_date" value="{{ old('paid_date', date('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('paid_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Input Catatan / Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Keterangan / Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Contoh: Pembayaran cicilan bulan ke-2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('installments.index') }}" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors">
                        Catat Pembayaran
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- 💡 Skrip Otomatis untuk Memunculkan Info Kotak Finansial -->
<script>
    document.getElementById('loan_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const monthly = parseFloat(selectedOption.getAttribute('data-monthly')) || 0;
        const remaining = parseFloat(selectedOption.getAttribute('data-remaining')) || 0;
        
        const infoBox = document.getElementById('info-box');
        const txtMonthly = document.getElementById('txt-monthly');
        const txtRemaining = document.getElementById('txt-remaining');
        const inputAmount = document.getElementById('amount');

        if (this.value) {
            // Format Rupiah standar Indonesia
            const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            
            txtMonthly.innerText = formatter.format(monthly);
            txtRemaining.innerText = formatter.format(remaining);
            
            // Set otomatis isi input Nominal Bayar dengan jumlah angsuran/bulan biar cepat
            inputAmount.value = Math.round(monthly);
            
            infoBox.classList.remove('hidden');
        } else {
            infoBox.classList.add('hidden');
            inputAmount.value = '';
        }
    });

    // Jalankan pengecekan awal saat halaman memuat ulang (jika ada error validasi)
    window.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('loan_id');
        if(select.value) {
            select.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
