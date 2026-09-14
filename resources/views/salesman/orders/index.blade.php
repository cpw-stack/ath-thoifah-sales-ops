@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-4">
    <div>
        <h2 class="display text-xl">Riwayat Order</h2>
        <p class="text-xs mt-1" style="color:var(--slate);">Pantau status order toko mitra Anda.</p>
    </div>

    @if(session('success'))
        <div class="card p-3 text-sm" style="background:var(--green-soft); color:var(--green);">✅ {{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @forelse ($orders as $order)
        <!-- Bungkus card dengan tag <a> agar bisa diklik -->
        <a href="{{ route('salesman.orders.show', $order) }}" class="card p-4 block active:opacity-80 active:scale-[0.98] transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="font-bold text-base">{{ $order->customer->name }}</div>
                    <div class="text-xs mono" style="color:var(--slate);">{{ $order->order_code }}</div>
                </div>
                <span class="chip {{ $order->status == 'completed' ? 'chip-done' : 'chip-pending' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="text-xs space-y-1 mb-3 border-t pt-2" style="border-color:var(--border);">
                <div class="flex justify-between">
                    <span style="color:var(--slate);">Tanggal:</span>
                    <span class="font-semibold">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--slate);">Pembayaran:</span>
                    <span class="font-semibold uppercase">{{ $order->payment_type }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--slate);">Total:</span>
                    <span class="font-bold mono" style="color:var(--ink);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            
            @if($order->delivery_date)
            <div class="text-xs text-center font-bold p-2 rounded-lg" style="background:var(--paper-dim); color:var(--ink);">
                🚚 Estimasi Kirim: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}
            </div>
            @endif
            
            <div class="mt-3 pt-2 border-t text-xs text-center font-bold" style="border-color:var(--border); color:var(--orange);">
                Lihat Detail Order →
            </div>
        </a>
        @empty
        <div class="card p-8 text-center text-sm" style="color:var(--slate);">
            <span class="text-3xl block mb-2">📦</span>
            Belum ada riwayat order.<br>Buat order saat melakukan kunjungan toko.
        </div>
        @endforelse
    </div>

    <!-- Custom Mobile Pagination -->
    <div class="mt-4">
        {{ $orders->links('vendor.pagination.mobile') }}
    </div>
</div>
@endsection