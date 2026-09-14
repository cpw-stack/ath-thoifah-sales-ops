@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-4">
    <a href="{{ route('salesman.orders.index') }}" class="text-sm font-semibold flex items-center gap-1" style="color:var(--slate);">← Kembali ke Riwayat</a>

    <!-- Header Order -->
    <div class="card p-5">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="display text-xl">{{ $order->customer->name }}</h2>
                <p class="text-xs mono mt-1" style="color:var(--slate);">{{ $order->order_code }}</p>
            </div>
            <span class="chip {{ $order->status == 'completed' ? 'chip-done' : 'chip-pending' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        
        <div class="mt-4 pt-4 border-t text-xs space-y-2" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Tanggal Order</span>
                <span class="font-semibold">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Salesman</span>
                <span class="font-semibold">{{ $salesmanName }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Tipe Pembayaran</span>
                <span class="font-semibold uppercase">{{ $order->payment_type }}</span>
            </div>
            @if($order->delivery_date)
            <div class="flex justify-between">
                <span style="color:var(--slate);">Estimasi Kirim</span>
                <span class="font-semibold">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</span>
            </div>
            @endif
            @if($order->notes)
            <div class="flex justify-between">
                <span style="color:var(--slate);">Catatan</span>
                <span class="text-right font-semibold" style="max-width: 60%;">{{ $order->notes }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- List Item Produk -->
    <div>
        <div class="text-sm font-bold uppercase mb-3" style="color:var(--ink);">Detail Produk</div>
        <div class="card p-4 space-y-3">
            @foreach($order->items as $item)
            <div class="flex justify-between items-start pb-3 border-b" style="border-color:var(--border);">
                <div class="flex-1 pr-2">
                    <div class="font-semibold text-sm">{{ $item->product->name }}</div>
                    <div class="text-xs mt-1" style="color:var(--slate);">
                        {{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                    </div>
                </div>
                <div class="font-bold text-sm mono">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
            
            <!-- Total -->
            <div class="flex justify-between items-center pt-2">
                <span class="font-bold text-base">Total Keseluruhan</span>
                <span class="font-bold text-base mono" style="color:var(--orange);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection