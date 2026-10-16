@extends('layouts.app')

@section('header')
    Kasir Penjualan Sparepart
@endsection

@section('content')
@php
    $posData = [
        'spareparts' => $spareparts->map(fn($s) => [
            'id' => $s->id, 'nama' => $s->nama, 'satuan' => $s->satuan,
            'harga_anggota' => (float)$s->harga_anggota, 'harga_driver' => (float)$s->harga_driver,
            'harga_umum' => (float)$s->harga_umum, 'stok_qty' => (float)$s->stok_qty,
        ]),
        'members' => $members->map(fn($m) => ['id' => $m->id, 'label' => $m->member_no . ' - ' . $m->name]),
        'drivers' => $drivers,
        'memberDrivers' => $memberDrivers,
        'preselectMember' => $preselectMember,
        'sukarelaSaldo' => $members->mapWithKeys(fn($m) => [$m->id => (float) $m->savings()->where('type','sukarela')->sum('amount')]),
    ];
@endphp
<div class="max-w-6xl mx-auto space-y-6"
    x-data="posForm(window.__POS_DATA__)"
>
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('sales.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Transaksi Penjualan Sparepart</h2>
            <p class="text-sm text-gray-500 mt-0.5">Buat struk baru — harga otomatis menyesuaikan tipe pembeli.</p>
        </div>
    </div>

    @if(session('error'))
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-card">
        <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" @submit="buildHiddenInputs($event)">
        @csrf

        <!-- Hidden inputs yang diisi dari Alpine saat submit -->
        <div x-ref="hiddenInputs"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom kiri: buyer + items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Buyer -->
                <div class="card p-6">
                    <h3 class="card-title mb-4">1. Tipe Pembeli</h3>
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <button type="button" @click="buyerType = 'anggota'"
                            :class="buyerType === 'anggota' ? 'border-emerald-500 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-500' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                            class="rounded-xl border p-3 text-sm font-bold transition-all">👤 Anggota</button>
                        <button type="button" @click="buyerType = 'driver'"
                            :class="buyerType === 'driver' ? 'border-blue-500 bg-blue-50 text-blue-700 ring-1 ring-blue-500' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                            class="rounded-xl border p-3 text-sm font-bold transition-all">🚐 Driver</button>
                        <button type="button" @click="buyerType = 'umum'"
                            :class="buyerType === 'umum' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-500' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                            class="rounded-xl border p-3 text-sm font-bold transition-all">🛒 Umum</button>
                    </div>

                    <!-- Anggota -->
                    <div x-show="buyerType === 'anggota'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Anggota Aktif <span class="text-red-500">*</span></label>
                        <select x-model="memberId" class="form-input-custom block w-full">
                            <option value="">-- Pilih Anggota --</option>
                            <template x-for="m in members" :key="m.id">
                                <option :value="m.id" x-text="m.label"></option>
                            </template>
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Harga memakai <strong class="text-emerald-600">harga anggota</strong>. Simpanan sukarela tersedia: <strong x-text="memberId ? formatRp(sukarelaSaldo[memberId] || 0) : '-'" class="text-gray-600"></strong></p>
                    </div>

                    <!-- Driver -->
                    <div x-show="buyerType === 'driver'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Driver <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-3 mb-2">
                            <label class="inline-flex items-center text-xs text-gray-600">
                                <input type="radio" value="dropdown" x-model="driverMode" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ms-1.5">Dari daftar nomor pintu</span>
                            </label>
                            <label class="inline-flex items-center text-xs text-gray-600">
                                <input type="radio" value="manual" x-model="driverMode" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ms-1.5">Input manual</span>
                            </label>
                        </div>
                        <select x-show="driverMode === 'dropdown'" x-model="driverSelected" class="form-input-custom block w-full">
                            <option value="">-- Pilih Driver --</option>
                            <template x-for="d in allDrivers" :key="d">
                                <option :value="d" x-text="d"></option>
                            </template>
                        </select>
                        <input x-show="driverMode === 'manual'" x-cloak type="text" x-model="driverManual" placeholder="Nama driver"
                            class="form-input-custom block w-full uppercase">
                        <p class="text-[11px] text-gray-400 mt-1">Harga memakai <strong class="text-blue-600">harga driver</strong>.</p>
                    </div>

                    <!-- Umum -->
                    <div x-show="buyerType === 'umum'" x-cloak>
                        <p class="text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg p-3">Tanpa identitas. Harga memakai <strong class="text-gray-700">harga umum</strong>.</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title">2. Item Barang</h3>
                        <button type="button" @click="addItem" class="btn-secondary !py-1.5">+ Tambah Item</button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex flex-wrap items-end gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-[11px] font-medium text-gray-500 mb-1">Sparepart</label>
                                    <select x-model="item.sparepart_id" class="form-input-custom block w-full">
                                        <option value="">-- Pilih --</option>
                                        <template x-for="sp in spareparts" :key="sp.id">
                                            <option :value="sp.id" x-text="sp.nama + ' (stok: ' + sp.stok_qty + ' ' + sp.satuan + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="w-24">
                                    <label class="block text-[11px] font-medium text-gray-500 mb-1">Qty</label>
                                    <input type="number" x-model.number="item.qty" min="1" step="1" class="form-input-custom block w-full">
                                </div>
                                <div class="w-32">
                                    <label class="block text-[11px] font-medium text-gray-500 mb-1">Harga</label>
                                    <input type="text" :value="formatRp(itemPrice(item))" disabled class="form-input-custom block w-full bg-gray-100 text-gray-500">
                                </div>
                                <div class="w-32">
                                    <label class="block text-[11px] font-medium text-gray-500 mb-1">Subtotal</label>
                                    <input type="text" :value="formatRp(itemSubtotal(item))" disabled class="form-input-custom block w-full bg-gray-100 font-bold text-gray-700">
                                </div>
                                <button type="button" @click="removeItem(index)" class="p-2 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus item">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                                <p class="w-full text-[11px]" :class="stockWarning(item) ? 'text-red-500' : 'invisible'">
                                    <span x-text="stockWarning(item)"></span>
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Payments -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title">3. Pembayaran (Bisa Split)</h3>
                        <button type="button" @click="addPayment" class="btn-secondary !py-1.5">+ Tambah Metode</button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(p, index) in payments" :key="index">
                            <div class="flex items-end gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex-1">
                                    <label class="block text-[11px] font-medium text-gray-500 mb-1">Metode</label>
                                    <select x-model="p.method" class="form-input-custom block w-full">
                                        <option value="cash">Tunai</option>
                                        <option value="simpanan" x-show="buyerType === 'anggota'">Simpanan Sukarela</option>
                                        <option value="utang">Utang</option>
                                    </select>
                                </div>
                                <div class="w-40">
                                    <label class="block text-[11px] font-medium text-gray-500 mb-1">Jumlah</label>
                                    <input type="number" x-model.number="p.amount" min="1" step="1" class="form-input-custom block w-full">
                                </div>
                                <button type="button" @click="removePayment(index)" class="p-2 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2">*Simpanan sukarela hanya untuk pembeli anggota dan dipotong otomatis dari saldonya.</p>
                </div>
            </div>

            <!-- Kolom kanan: ringkasan sticky -->
            <div class="lg:col-span-1">
                <div class="card p-6 lg:sticky lg:top-20">
                    <h3 class="card-title mb-4">Ringkasan</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Item</span>
                            <span class="font-bold text-gray-800" x-text="items.filter(i => i.sparepart_id && i.qty > 0).length + ' item'"></span>
                        </div>
                        <div class="flex justify-between text-base border-t border-gray-100 pt-3">
                            <span class="text-gray-600 font-semibold">Total Belanja</span>
                            <span class="font-black text-indigo-600" x-text="formatRp(total)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Dibayar</span>
                            <span class="font-bold text-emerald-600" x-text="formatRp(totalPaid)"></span>
                        </div>
                        <div class="flex justify-between" x-show="totalPaid > total">
                            <span class="text-gray-500">Kembalian</span>
                            <span class="font-bold text-amber-600" x-text="formatRp(change)"></span>
                        </div>
                        <div class="flex justify-between" x-show="totalPaid < total && total > 0">
                            <span class="text-gray-500">Kurang Bayar</span>
                            <span class="font-bold text-red-600" x-text="formatRp(total - totalPaid)"></span>
                        </div>
                    </div>

                    <!-- Tanggal & catatan -->
                    <div class="mt-5 pt-4 border-t border-gray-100 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                            <input type="date" x-model="saleDate" :max="today" class="form-input-custom block w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <input type="text" x-model="notes" placeholder="opsional" class="form-input-custom block w-full">
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        @click="confirmSubmit($event)"
                        class="btn-primary w-full mt-5 !py-3 !text-sm"
                        :class="{ 'opacity-40 pointer-events-none': !canSubmit }">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span x-text="canSubmit ? 'Simpan Transaksi' : 'Lengkapi Transaksi'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Data dari server (dieksekusi sebelum Alpine start — Alpine adalah module/deferred)
window.__POS_DATA__ = @json($posData);

function posForm(config) {
    return {
        spareparts: config.spareparts,
        members: config.members,
        allDrivers: [...config.drivers, ...config.memberDrivers],
        sukarelaSaldo: config.sukarelaSaldo,
        preselectMember: config.preselectMember,

        buyerType: 'anggota',
        memberId: config.preselectMember || '',
        driverMode: 'dropdown',
        driverSelected: '',
        driverManual: '',
        items: [{ sparepart_id: '', qty: 1 }],
        payments: [{ method: 'cash', amount: 0 }],
        saleDate: new Date().toISOString().slice(0, 10),
        today: new Date().toISOString().slice(0, 10),
        notes: '',

        get selectedMember() { return this.members.find(m => m.id == this.memberId); },

        itemPrice(item) {
            const sp = this.spareparts.find(s => s.id == item.sparepart_id);
            if (!sp) return 0;
            return { anggota: sp.harga_anggota, driver: sp.harga_driver, umum: sp.harga_umum }[this.buyerType] || 0;
        },
        itemSubtotal(item) {
            return (parseFloat(item.qty) || 0) * this.itemPrice(item);
        },
        stockWarning(item) {
            const sp = this.spareparts.find(s => s.id == item.sparepart_id);
            if (!sp) return '';
            // cek total qty semua row utk sparepart yg sama
            const totalQty = this.items
                .filter(i => i.sparepart_id == item.sparepart_id)
                .reduce((a, i) => a + (parseFloat(i.qty) || 0), 0);
            if (totalQty > sp.stok_qty) return `⚠ Total qty (${totalQty}) melebihi stok ${sp.nama} (${sp.stok_qty})`;
            return '';
        },
        get total() { return this.items.reduce((a, i) => a + this.itemSubtotal(i), 0); },
        get totalPaid() { return this.payments.reduce((a, p) => a + (parseFloat(p.amount) || 0), 0); },
        get change() { return Math.max(0, this.totalPaid - this.total); },

        addItem() { this.items.push({ sparepart_id: '', qty: 1 }); },
        removeItem(i) { this.items.length > 1 ? this.items.splice(i, 1) : this.items = [{ sparepart_id: '', qty: 1 }]; },
        addPayment() { this.payments.push({ method: 'cash', amount: 0 }); },
        removePayment(i) { this.payments.length > 1 ? this.payments.splice(i, 1) : this.payments = [{ method: 'cash', amount: 0 }]; },

        get canSubmit() {
            const hasItems = this.items.some(i => i.sparepart_id && i.qty > 0);
            const buyerOk = this.buyerType === 'anggota' ? !!this.memberId
                : this.buyerType === 'driver' ? (this.driverMode === 'manual' ? !!this.driverManual.trim() : !!this.driverSelected)
                : true;
            const paidOk = this.totalPaid >= this.total && this.total > 0;
            const stockOk = !this.items.some(i => this.stockWarning(i));
            // simpanan cukup?
            const simpanan = this.payments.filter(p => p.method === 'simpanan').reduce((a, p) => a + (parseFloat(p.amount) || 0), 0);
            const simpananOk = simpanan === 0 || (this.memberId && simpanan <= (this.sukarelaSaldo[this.memberId] || 0));
            return hasItems && buyerOk && paidOk && stockOk && simpananOk;
        },

        confirmSubmit(e) {
            if (!this.canSubmit) { e.preventDefault(); return; }
            if (!confirm('Simpan transaksi ini? Stok akan langsung berkurang.')) e.preventDefault();
        },

        // Mapping state Alpine -> form fields saat submit
        buildHiddenInputs(e) {
            const form = e.target;
            const container = this.$refs.hiddenInputs;
            container.innerHTML = '';

            const add = (name, value) => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = name; inp.value = value;
                container.appendChild(inp);
            };

            add('buyer_type', this.buyerType);
            if (this.buyerType === 'anggota') add('member_id', this.memberId);
            if (this.buyerType === 'driver') {
                add('driver_name', this.driverMode === 'manual' ? this.driverManual : this.driverSelected);
            }
            add('sale_date', this.saleDate);
            if (this.notes) add('notes', this.notes);

            this.items.filter(i => i.sparepart_id && i.qty > 0).forEach((i, idx) => {
                add(`items[${idx}][sparepart_id]`, i.sparepart_id);
                add(`items[${idx}][qty]`, i.qty);
            });

            this.payments.filter(p => parseFloat(p.amount) > 0).forEach((p, idx) => {
                add(`payments[${idx}][method]`, p.method);
                add(`payments[${idx}][amount]`, p.amount);
            });
        },

        formatRp(v) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v * 100) / 100);
        },
    };
}
</script>
@endpush
@endsection
