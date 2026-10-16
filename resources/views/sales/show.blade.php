@extends('layouts.app')

@section('header')
    Struk {{ $sale->invoice_no }}
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('sales.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Kembali">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Struk Penjualan</h2>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-card">
        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Struk Card (printable) -->
    <div class="card" id="struk">
        <!-- Struk header -->
        <div class="bg-indigo-600 px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <x-app-logo class="w-10 h-10 object-contain bg-white rounded-lg p-1" />
                <div>
                    <h2 class="text-lg font-black text-white">KABAPIN</h2>
                    <p class="text-indigo-100 text-xs">Bukti Penjualan Sparepart</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-indigo-100 text-[10px] uppercase tracking-wider">No. Invoice</p>
                <p class="text-white font-mono font-bold text-sm">{{ $sale->invoice_no }}</p>
            </div>
        </div>

        <!-- Info pembeli -->
        <div class="px-6 py-4 border-b border-gray-100 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Pembeli</p>
                <p class="font-bold text-gray-800">{{ $sale->buyer_display }}</p>
                <p class="text-xs text-gray-400">{{ $sale->buyer_label }}@if($sale->member) • {{ $sale->member->member_no }} @endif</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Tanggal</p>
                <p class="font-bold text-gray-800">{{ $sale->sale_date->format('d-m-Y') }}</p>
                <p class="text-xs text-gray-400">oleh {{ $sale->performer?->name }}</p>
            </div>
        </div>

        <!-- Items -->
        <div class="px-6 py-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-[10px] uppercase tracking-wider text-gray-400">
                        <th class="text-start py-2">Barang</th>
                        <th class="text-center py-2 w-16">Qty</th>
                        <th class="text-end py-2 w-24">Harga</th>
                        <th class="text-end py-2 w-28">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr class="border-b border-gray-50">
                        <td class="py-2.5 text-gray-700 font-medium">{{ $item->nama_snapshot }}</td>
                        <td class="py-2.5 text-center font-mono text-gray-600">{{ $item->qty }}</td>
                        <td class="py-2.5 text-end font-mono text-gray-600">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-2.5 text-end font-mono font-bold text-gray-800">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="3" class="py-3 text-end font-bold text-gray-700">Total</td>
                        <td class="py-3 text-end font-black text-lg text-indigo-600">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payments -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Pembayaran</p>
            <div class="space-y-1.5 text-sm">
                @foreach($sale->payments as $p)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ $p->method_label }} @if($p->reference)<span class="text-gray-300 text-xs">({{ $p->reference }})</span>@endif</span>
                    <span class="font-mono font-bold text-gray-800">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
                @if($sale->change_amount > 0)
                <div class="flex justify-between border-t border-gray-200 pt-1.5">
                    <span class="text-gray-600 font-semibold">Kembalian</span>
                    <span class="font-mono font-bold text-amber-600">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->remaining_debt > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600 font-semibold">Sisa Utang</span>
                    <span class="font-mono font-bold text-red-600">Rp {{ number_format($sale->remaining_debt, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
            <div class="mt-3">
                @if($sale->status === 'paid')
                    <span class="badge-success">Lunas</span>
                @elseif($sale->status === 'partial')
                    <span class="badge-warning">Bayar Sebagian</span>
                @else
                    <span class="badge-danger">Utang</span>
                @endif
                @if(str_contains($sale->notes ?? '', '[VOID'))
                    <span class="badge-danger ms-1">VOID</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Aksi -->
    <div class="flex flex-wrap gap-2">
        <button onclick="window.print()" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
            </svg>
            Cetak Struk
        </button>
        @if(!str_contains($sale->notes ?? '', '[VOID'))
        <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini? Stok dan simpanan akan dikembalikan.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Batalkan Transaksi (Void)</button>
        </form>
        @endif
    </div>
</div>
@endsection
