@extends('layouts.app')

@section('title', 'Product Management')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Product Management</h2>
        <p class="text-sm" style="color:var(--slate);">Kelola daftar produk dan harga.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn w-full sm:w-auto text-center">+ Tambah Produk</a>
</div>

@if (session('success'))
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">{{ session('success') }}</div>
@endif

<!-- HEADER SEARCH & IMPORT (Tampil di semua layar) -->
<div class="card p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.products.index') }}" class="relative w-full md:max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..." class="w-full pr-9">
        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--slate);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </form>
    <div class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
        <a href="{{ route('admin.products.template') }}" class="btn-outline text-xs w-full sm:w-auto text-center">Download Template</a>
        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 w-full sm:w-auto">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="text-xs border rounded p-1.5 w-full" style="border-color:var(--border);" required>
            <button type="submit" class="btn text-xs whitespace-nowrap">Upload Excel</button>
        </form>
    </div>
</div>

<!-- 1. TAMPILAN DESKTOP (Tabel) - Hanya muncul di layar besar -->
<div class="card overflow-hidden hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">SKU</th>
                    <th class="p-4">Nama Produk</th>
                    <th class="p-4">Kategori</th>
                    <th class="p-4">Satuan</th>
                    <th class="p-4">Harga</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr class="border-b hover:bg-gray-50" style="border-color:var(--border);">
                    <td class="p-4 mono text-xs">{{ $product->sku }}</td>
                    <td class="p-4 font-semibold text-sm">{{ $product->name }}</td>
                    <td class="p-4 text-sm">{{ $product->category->name ?? '-' }}</td>
                    <td class="p-4 text-sm">{{ $product->unit }}</td>
                    <td class="p-4 mono text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="p-4">
                        @if($product->status == 'active')
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-slate">Inactive</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs font-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center" style="color:var(--slate);">Produk tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t" style="border-color:var(--border);">
        {{ $products->appends(['search' => request('search')])->links() }}
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($products as $product)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $product->name }}</div>
                <div class="text-xs mono" style="color:var(--slate);">SKU: {{ $product->sku }}</div>
            </div>
            @if($product->status == 'active')
                <span class="badge badge-green">Active</span>
            @else
                <span class="badge badge-slate">Inactive</span>
            @endif
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Kategori:</span>
                <span class="font-semibold text-right">{{ $product->category->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Satuan:</span>
                <span class="font-semibold text-right">{{ $product->unit }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Harga:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Edit</a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Produk tidak ditemukan.
    </div>
    @endforelse
    
    <!-- Pagination Mobile -->
    @if($products->hasPages())
    <div class="mt-4">
        {{ $products->appends(['search' => request('search')])->links() }}
    </div>
    @endif
</div>
@endsection