@extends('layouts.app')

@section('title', 'Collection & Piutang')

@section('content')
<div class="mb-6">
    <h2 class="display text-2xl">Collection & Piutang</h2>
    <p class="text-sm" style="color:var(--slate);">Verifikasi pembayaran dan pantau piutang overdue.</p>
</div>

@if ($errors->any())
    <div class="card p-4 mb-4" style="background:var(--red-soft); color:var(--red);">
        @foreach ($errors->all() as $error) <p class="text-sm">{{ $error }}</p> @endforeach
    </div>
@endif

@if($overdues->count() > 0)
<div class="card p-4 mb-6" style="background:var(--red-soft); border:1px solid var(--red);">
    <div class="font-bold text-sm mb-2" style="color:var(--red);">⚠️ Perhatian! Piutang Overdue ({{ $overdues->count() }} Toko):</div>
    <div class="flex flex-wrap gap-2">
        @foreach($overdues as $od)
            <span class="badge badge-red">{{ $od->customer->name }} (Rp {{ number_format($od->total_amount - $od->paid_amount, 0, ',', '.') }})</span>
        @endforeach
    </div>
</div>
@endif

<!-- HEADER FILTER (Tampil di semua layar) -->
<div class="card p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="condensed w-full md:w-auto">Riwayat Penagihan & Verifikasi</div>
    <form method="GET" action="{{ route('admin.collections.index') }}" class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
        <select name="status" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Toko</th>
                    <th class="p-4">Salesman</th>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Metode</th>
                    <th class="p-4">Jumlah</th>
                    <th class="p-4">Bukti</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($collections as $c)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 text-sm font-semibold">{{ $c->receivable->customer->name }}</td>
                    <td class="p-4 text-sm">{{ $c->employee->full_name }}</td>
                    <td class="p-4 mono text-xs">{{ $c->payment_date->format('d M Y') }}</td>
                    <td class="p-4"><span class="badge badge-slate">{{ $c->payment_method }}</span></td>
                    <td class="p-4 mono text-sm font-bold">Rp {{ number_format($c->amount, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        @if($c->payment_proof)
                            <a href="{{ asset('storage/' . $c->payment_proof) }}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat</a>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($c->status == 'pending')
                            <span class="badge badge-amber">Pending</span>
                        @elseif($c->status == 'verified')
                            <span class="badge badge-green">Verified</span>
                        @else
                            <span class="badge badge-red">Rejected</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        @if($c->status == 'pending')
                            <form action="{{ route('admin.collections.verify', $c) }}" method="POST" class="inline-flex gap-2">
                                @csrf
                                <button type="submit" name="action" value="verify" class="btn-outline text-xs" style="padding:6px 10px; border-color:var(--green); color:var(--green);" onclick="return confirm('Anda yakin? Apakah dana dari {{ $c->receivable->customer->name }} sudah diterima?')">Verifikasi</button>
                                <button type="submit" name="action" value="reject" class="text-red-600 text-xs font-bold p-1" onclick="return confirm('Tolak pembayaran ini? Piutang akan dikembalikan ke semula.')">Tolak</button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-8 text-center" style="color:var(--slate);">Belum ada riwayat penagihan sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t" style="border-color:var(--border);">
        {{ $collections->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($collections as $c)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $c->receivable->customer->name }}</div>
                <div class="text-xs mono" style="color:var(--slate);">{{ $c->payment_date->format('d M Y') }}</div>
            </div>
            @if($c->status == 'pending')
                <span class="badge badge-amber">Pending</span>
            @elseif($c->status == 'verified')
                <span class="badge badge-green">Verified</span>
            @else
                <span class="badge badge-red">Rejected</span>
            @endif
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Salesman:</span>
                <span class="font-semibold text-right">{{ $c->employee->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Metode:</span>
                <span class="font-semibold text-right">{{ $c->payment_method }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Jumlah:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($c->amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span style="color:var(--slate);">Bukti:</span>
                @if($c->payment_proof)
                    <a href="{{ asset('storage/' . $c->payment_proof) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">Lihat Bukti</a>
                @else
                    <span class="font-semibold text-right">-</span>
                @endif
            </div>
        </div>

        @if($c->status == 'pending')
            <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
                <form action="{{ route('admin.collections.verify', $c) }}" method="POST" class="flex gap-2 w-full">
                    @csrf
                    <button type="submit" name="action" value="verify" class="btn-outline text-xs flex-1 text-center" style="padding:8px 10px; border-color:var(--green); color:var(--green);" onclick="return confirm('Anda yakin? Apakah dana dari {{ $c->receivable->customer->name }} sudah diterima?')">Verifikasi</button>
                    <button type="submit" name="action" value="reject" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded flex-1" style="border-color:var(--border);" onclick="return confirm('Tolak pembayaran ini? Piutang akan dikembalikan ke semula.')">Tolak</button>
                </form>
            </div>
        @else
            <div class="w-full text-center text-xs text-gray-400 py-2 border-t" style="border-color:var(--border);">Selesai</div>
        @endif
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Belum ada riwayat penagihan sesuai filter.
    </div>
    @endforelse
    
    <!-- Pagination Mobile -->
    @if($collections->hasPages())
    <div class="mt-4">
        {{ $collections->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
    @endif
</div>
@endsection