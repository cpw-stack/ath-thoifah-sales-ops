@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="display text-2xl">Detail Order</h2>
            <p class="mono text-sm" style="color:var(--slate);">{{ $order->order_code }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn-outline">← Kembali</a>
    </div>

    <div class="card mb-6 p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <div class="text-xs uppercase tracking-wider" style="color:var(--slate);">Toko Mitra</div>
            <div class="font-semibold mt-1">{{ $order->customer->name }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wider" style="color:var(--slate);">Salesman</div>
            <div class="font-semibold mt-1">{{ $order->employee->full_name }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wider mb-1" style="color:var(--slate);">Status Order</div>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex gap-2">
                @csrf
                @method('PUT')
                <select name="status" class="text-sm p-2 rounded border flex-1" style="border-color:var(--border);">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ $order->status == 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn text-xs">Update</button>
            </form>
        </div>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Produk</th>
                    <th class="p-4">Harga</th>
                    <th class="p-4 text-center">Qty</th>
                    <th class="p-4 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 text-sm font-semibold">{{ $item->product->name }}</td>
                    <td class="p-4 mono text-xs">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="p-4 mono text-xs text-center">{{ $item->qty }}</td>
                    <td class="p-4 mono text-sm text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="p-4 text-right font-bold">Total Keseluruhan:</td>
                    <td class="p-4 mono text-lg text-right font-bold" style="color:var(--orange);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection