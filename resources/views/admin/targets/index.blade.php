@extends('layouts.app')

@section('title', 'Target Management')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Target Salesman</h2>
        <p class="text-sm" style="color:var(--slate);">Periode: {{ now()->translatedFormat('F Y') }}</p>
    </div>
    <a href="{{ route('admin.targets.create') }}" class="btn w-full sm:w-auto text-center">+ Tetapkan Target Manual</a>
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
                    <th class="p-4">Tipe</th>
                    <th class="p-4">Visit / Laporan</th>
                    <th class="p-4">Order</th>
                    <th class="p-4">Sales (Rp)</th>
                    <th class="p-4">Collection (Rp)</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $emp)
                    @php 
                        // Ambil target untuk periode bulan ini
                        $target = $emp->targets->firstWhere('period_month', now()->format('Y-m'));
                    @endphp
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 font-semibold text-sm">{{ $emp->full_name }}</td>
                    <td class="p-4">
                        @if($emp->type == 'online')
                            <span class="badge badge-green">Online</span>
                        @else
                            <span class="badge badge-amber">Offline</span>
                        @endif
                    </td>
                    
                    <!-- Kolom Visit / Laporan -->
                    <td class="p-4 text-sm">
                        @if($target)
                            @if($emp->type == 'online')
                                <span class="mono">{{ $target->visit_target }}x</span> <span class="text-xs text-gray-400">(Laporan)</span>
                            @else
                                <span class="mono">{{ $target->visit_target }}x</span>
                            @endif
                        @else
                            <span class="text-xs italic text-gray-400">Belum disetting</span>
                        @endif
                    </td>

                    <!-- Kolom Order -->
                    <td class="p-4 text-sm">
                        @if($target)
                            <span class="mono">{{ $target->order_target }}x</span>
                        @else
                            <span class="text-xs italic text-gray-400">Belum disetting</span>
                        @endif
                    </td>

                    <!-- Kolom Sales -->
                    <td class="p-4 mono text-sm">
                        @if($target)
                            {{ number_format($target->sales_target, 0, ',', '.') }}
                        @else
                            <span class="text-xs italic text-gray-400">Belum disetting</span>
                        @endif
                    </td>

                    <!-- Kolom Collection -->
                    <td class="p-4 mono text-sm">
                        @if($target)
                            {{ number_format($target->collection_target, 0, ',', '.') }}
                        @else
                            <span class="text-xs italic text-gray-400">Belum disetting</span>
                        @endif
                    </td>

                    <!-- Kolom Aksi -->
                    <td class="p-4 text-right whitespace-nowrap">
                        @if($target)
                            <a href="{{ route('admin.targets.edit', $target) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                            <form action="{{ route('admin.targets.destroy', $target) }}" method="POST" class="inline" onsubmit="return confirm('Hapus target?')">
                                @csrf @method('DELETE') 
                                <button class="text-red-600 text-xs font-bold">Hapus</button>
                            </form>
                        @else
                            <a href="{{ route('admin.targets.create', ['employee_id' => $emp->id]) }}" class="btn text-xs" style="padding:6px 10px;">+ Set Target</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center" style="color:var(--slate);">Tidak ada data salesman ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($employees as $emp)
        @php $target = $emp->targets->firstWhere('period_month', now()->format('Y-m')); @endphp
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $emp->full_name }}</div>
                <div class="flex gap-2 mt-1">
                    @if($emp->type == 'online')
                        <span class="badge badge-green">Online</span>
                    @else
                        <span class="badge badge-amber">Offline</span>
                    @endif
                </div>
            </div>
            @if($target)
                <span class="badge badge-slate">Periode: {{ $target->period_month }}</span>
            @else
                <span class="badge badge-red">Belum Set</span>
            @endif
        </div>
        
        @if($target)
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target {{ $emp->type == 'online' ? 'Laporan' : 'Visit' }}:</span>
                <span class="font-semibold text-right">{{ $target->visit_target }}x</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target Order:</span>
                <span class="font-semibold text-right">{{ $target->order_target }}x</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target Sales:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($target->sales_target, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Target Collection:</span>
                <span class="font-semibold text-right mono">Rp {{ number_format($target->collection_target, 0, ',', '.') }}</span>
            </div>
        </div>
        @else
        <div class="text-xs text-center py-4 border-t mt-3" style="border-color:var(--border); color:var(--slate);">
            Target untuk bulan ini belum disetting.
        </div>
        @endif

        <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
            @if($target)
                <a href="{{ route('admin.targets.edit', $target) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Edit</a>
                <form action="{{ route('admin.targets.destroy', $target) }}" method="POST" class="inline" onsubmit="return confirm('Hapus target?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">Hapus</button>
                </form>
            @else
                <a href="{{ route('admin.targets.create', ['employee_id' => $emp->id]) }}" class="btn text-xs flex-1 text-center" style="padding:6px 12px;">+ Set Target Sekarang</a>
            @endif
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Tidak ada data salesman ditemukan.
    </div>
    @endforelse
</div>
@endsection