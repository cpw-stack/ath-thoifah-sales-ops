@extends('layouts.app')

@section('title', 'Target Management')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <h2 class="display text-2xl">Target Salesman</h2>
    <a href="{{ route('admin.targets.create') }}" class="btn w-full sm:w-auto text-center">+ Tetapkan Target</a>
</div>

@if (session('success')) 
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">{{ session('success') }}</div> 
@endif

<!-- 1. TAMPILAN DESKTOP (Tabel) - Hanya muncul di layar besar -->
<div class="card overflow-hidden hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Salesman</th>
                    <th class="p-4">Periode</th>
                    <th class="p-4">Visit</th>
                    <th class="p-4">Order</th>
                    <th class="p-4">Sales (Rp)</th>
                    <th class="p-4">Collection (Rp)</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($targets as $t)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 font-semibold text-sm">{{ $t->employee->full_name }}</td>
                    <td class="p-4 mono text-xs">{{ $t->period_month }}</td>
                    <td class="p-4 text-sm">{{ $t->visit_target }}</td>
                    <td class="p-4 text-sm">{{ $t->order_target }}</td>
                    <td class="p-4 mono text-sm">{{ number_format($t->sales_target, 0, ',', '.') }}</td>
                    <td class="p-4 mono text-sm">{{ number_format($t->collection_target, 0, ',', '.') }}</td>
                    <td class="p-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.targets.edit', $t) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                        <form action="{{ route('admin.targets.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus target?')">
                            @csrf @method('DELETE') 
                            <button class="text-red-600 text-xs font-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center" style="color:var(--slate);">Belum ada target ditetapkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($targets as $t)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $t->employee->full_name }}</div>
                <div class="text-xs mono" style="color:var(--slate);">Periode: {{ $t->period_month }}</div>
            </div>
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target Visit:</span>
                <span class="font-semibold text-right">{{ $t->visit_target }}x</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target Order:</span>
                <span class="font-semibold text-right">{{ $t->order_target }}x</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target Sales:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($t->sales_target, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target Collection:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($t->collection_target, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
            <a href="{{ route('admin.targets.edit', $t) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Edit</a>
            <form action="{{ route('admin.targets.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus target?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Belum ada target ditetapkan.
    </div>
    @endforelse
</div>
@endsection