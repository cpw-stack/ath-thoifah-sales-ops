@extends('layouts.app')

@section('title', 'Area Penjualan')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Area Penjualan</h2>
        <p class="text-sm" style="color:var(--slate);">Kelola wilayah distribusi salesman.</p>
    </div>
    <a href="{{ route('admin.areas.create') }}" class="btn">+ Tambah Area</a>
</div>

@if (session('success'))
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green);">{{ session('success') }}</div>
@endif

<div class="card overflow-hidden">
    <!-- Header: Search & Import -->
    <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4 border-b" style="border-color:var(--border); background:var(--paper-dim);">
        <form method="GET" action="{{ route('admin.areas.index') }}" class="relative w-full md:max-w-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode area..." class="w-full pr-9">
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--slate);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </form>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.areas.template') }}" class="btn-outline text-xs">Download Template</a>
            <form action="{{ route('admin.areas.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv" class="text-xs border rounded p-1.5" style="border-color:var(--border);" required>
                <button type="submit" class="btn text-xs">Upload Excel</button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Kode Area</th>
                    <th class="p-4">Nama Area</th>
                    <th class="p-4">Deskripsi</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($areas as $area)
                <tr class="border-b hover:bg-gray-50" style="border-color:var(--border);">
                    <td class="p-4 mono text-xs">{{ $area->code }}</td>
                    <td class="p-4 font-semibold text-sm">{{ $area->name }}</td>
                    <td class="p-4 text-sm" style="color:var(--slate);">{{ $area->description ?? '-' }}</td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.areas.edit', $area) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                        <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs font-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-8 text-center" style="color:var(--slate);">Area tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t" style="border-color:var(--border);">
        {{ $areas->appends(['search' => request('search')])->links() }}
    </div>
</div>
@endsection