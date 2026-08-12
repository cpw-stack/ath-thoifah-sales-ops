@extends('layouts.app')

@section('title', 'Order Management')

@section('content')
<div class="mb-6">
    <h2 class="display text-2xl">Order Masuk</h2>
    <p class="text-sm" style="color:var(--slate);">Kelola status pesanan dari salesman.</p>
</div>

<!-- HEADER FILTER (Tampil di semua layar) -->
<div class="card p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="condensed w-full md:w-auto">Daftar Order</div>
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
        <select name="status" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <select name="employee_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Salesman</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
            @endforeach
        </select>

        <select name="customer_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Toko Mitra</option>
            @foreach($customers as $cust)
                <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- 1. TAMPILAN DESKTOP (Tabel) - Hanya muncul di layar besar -->
<div class="card overflow-hidden hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Kode Order</th>
                    <th class="p-4">Toko</th>
                    <th class="p-4">Salesman</th>
                    <th class="p-4">Total</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $o)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 mono text-xs">{{ $o->order_code }}</td>
                    <td class="p-4 text-sm font-semibold">{{ $o->customer->name }}</td>
                    <td class="p-4 text-sm">{{ $o->employee->full_name }}</td>
                    <td class="p-4 mono text-sm">Rp {{ number_format($o->total_amount, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        <span class="badge {{ $o->status == 'delivered' ? 'badge-green' : ($o->status == 'processed' ? 'badge-amber' : ($o->status == 'cancelled' ? 'badge-red' : 'badge-slate')) }}">{{ $o->status }}</span>
                    </td>
                    <td class="p-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $o) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Detail</a>
                        <form action="{{ route('admin.orders.status', $o) }}" method="POST" class="inline-flex">
                            @csrf @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="text-xs p-1 rounded border" style="border-color:var(--border);">
                                <option value="pending" {{ $o->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processed" {{ $o->status == 'processed' ? 'selected' : '' }}>Processed</option>
                                <option value="delivered" {{ $o->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $o->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center" style="color:var(--slate);">Belum ada order sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t" style="border-color:var(--border);">
        {{ $orders->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($orders as $o)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $o->customer->name }}</div>
                <div class="text-xs mono" style="color:var(--slate);">{{ $o->order_code }}</div>
            </div>
            <span class="badge {{ $o->status == 'delivered' ? 'badge-green' : ($o->status == 'processed' ? 'badge-amber' : ($o->status == 'cancelled' ? 'badge-red' : 'badge-slate')) }}">{{ $o->status }}</span>
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Salesman:</span>
                <span class="font-semibold text-right">{{ $o->employee->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Total:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($o->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-2 border-t pt-3" style="border-color:var(--border);">
            <a href="{{ route('admin.orders.show', $o) }}" class="btn-outline text-xs w-full text-center" style="padding:8px 12px;">Lihat Detail</a>
            <form action="{{ route('admin.orders.status', $o) }}" method="POST" class="flex items-center gap-2 w-full">
                @csrf @method('PUT')
                <span class="text-xs" style="color:var(--slate);">Ubah Status:</span>
                <select name="status" onchange="this.form.submit()" class="text-xs p-1.5 rounded border flex-1" style="border-color:var(--border);">
                    <option value="pending" {{ $o->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ $o->status == 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="delivered" {{ $o->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $o->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Belum ada order sesuai filter.
    </div>
    @endforelse
    
    <!-- Pagination Mobile -->
    @if($orders->hasPages())
    <div class="mt-4">
        {{ $orders->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
    @endif
</div>
@endsection