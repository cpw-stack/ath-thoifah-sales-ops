@extends('layouts.app')

@section('title', 'Mitra Management')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Mitra Management</h2>
        <p class="text-sm" style="color:var(--slate);">Kelola data toko dan limit kredit.</p>
    </div>
    <a href="{{ route('admin.customers.create') }}" class="btn w-full sm:w-auto text-center">+ Tambah Mitra</a>
</div>

@if (session('success'))
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">{{ session('success') }}</div>
@endif

<!-- HEADER SEARCH & IMPORT (Tampil di semua layar) -->
<div class="card p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.customers.index') }}" class="relative w-full md:max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode toko..." class="w-full pr-9">
        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--slate);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </form>
    <div class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
        <a href="{{ route('admin.customers.template') }}" class="btn-outline text-xs w-full sm:w-auto text-center">Download Template</a>
        <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 w-full sm:w-auto">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="text-xs border rounded p-1.5 w-full" style="border-color:var(--border);" required>
            <button type="submit" class="btn text-xs whitespace-nowrap">Upload Excel</button>
        </form>
    </div>
</div>

<!-- 1. TAMPILAN DESKTOP (Tabel) - Hanya muncul di layar besar -->
<div class="card overflow-hidden hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Kode</th>
                    <th class="p-4">Nama Toko</th>
                    <th class="p-4">Pemilik</th>
                    <th class="p-4">Telepon</th>
                    <th class="p-4">Limit Kredit</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                <tr class="border-b hover:bg-gray-50" style="border-color:var(--border);">
                    <td class="p-4 mono text-xs">{{ $customer->customer_code }}</td>
                    <td class="p-4 font-semibold text-sm">{{ $customer->name }}</td>
                    <td class="p-4 text-sm">{{ $customer->owner_name ?? '-' }}</td>
                    <td class="p-4 text-sm">{{ $customer->phone_number ?? '-' }}</td>
                    <td class="p-4 mono text-sm">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</td>
                    <td class="p-4">
                        @if($customer->status == 'active')
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-slate">Inactive</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs font-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center" style="color:var(--slate);">Mitra tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t" style="border-color:var(--border);">
        {{ $customers->appends(['search' => request('search')])->links() }}
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($customers as $customer)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $customer->name }}</div>
                <div class="text-xs mono" style="color:var(--slate);">{{ $customer->customer_code }}</div>
            </div>
            @if($customer->status == 'active')
                <span class="badge badge-green">Active</span>
            @else
                <span class="badge badge-slate">Inactive</span>
            @endif
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Pemilik:</span>
                <span class="font-semibold text-right">{{ $customer->owner_name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Telepon:</span>
                <span class="font-semibold text-right">{{ $customer->phone_number ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Limit Kredit:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Edit</a>
            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Mitra tidak ditemukan.
    </div>
    @endforelse
    
    <!-- Pagination Mobile -->
    @if($customers->hasPages())
    <div class="mt-4">
        {{ $customers->appends(['search' => request('search')])->links() }}
    </div>
    @endif
</div>
@endsection