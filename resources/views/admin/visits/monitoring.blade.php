@extends('layouts.app')

@section('title', 'Monitoring Kunjungan')

@section('content')
<div class="mb-6">
    <h2 class="display text-2xl">Monitoring Kunjungan</h2>
    <p class="text-sm" style="color:var(--slate);">Pantau aktivitas salesman lapangan hari ini.</p>
</div>

<!-- HEADER FILTER (Tampil di semua layar) -->
<div class="card p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="condensed w-full md:w-auto">Aktivitas Hari Ini ({{ $plans->total() }})</div>
    <form method="GET" action="{{ route('salesman.visits.index') }}" class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
        <select name="status" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Status</option>
            <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Belum Check-in</option>
            <option value="visited" {{ request('status') == 'visited' ? 'selected' : '' }}>Sedang Visit</option>
            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Selesai</option>
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
                    <th class="p-4">Salesman</th>
                    <th class="p-4">Toko</th>
                    <th class="p-4">Waktu Check-in</th>
                    <th class="p-4">Jarak GPS</th>
                    <th class="p-4">Bukti Foto</th>
                    <th class="p-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plans as $plan)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 text-sm">{{ $plan->employee->full_name ?? '-' }}</td>
                    <td class="p-4 text-sm font-semibold" style="color:var(--ink);">{{ $plan->customer->name }}</td>
                    <td class="p-4 mono text-xs">
                        @if($plan->visit && $plan->visit->check_in_at)
                            {{ $plan->visit->check_in_at->format('H:i') }}
                        @else
                            <span style="color:var(--slate);">-</span>
                        @endif
                    </td>
                    <td class="p-4 mono text-xs">
                        @if($plan->visit) 
                            <span style="{{ $plan->visit->distance_meters > 200 ? 'color:var(--red);' : '' }}">{{ $plan->visit->distance_meters }} m</span> 
                        @else 
                            <span style="color:var(--slate);">-</span> 
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if($plan->visit && $plan->visit->check_in_photo)
                            <a href="{{ asset('storage/' . $plan->visit->check_in_photo) }}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat Foto</a>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($plan->status == 'planned')
                            <span class="badge badge-amber">Planned</span>
                        @elseif($plan->status == 'completed' && $plan->visit)
                            @if($plan->visit->check_out_at)
                                <span class="badge badge-green">Selesai</span>
                            @else
                                <span class="badge badge-slate" style="background:var(--ink); color:#fff;">Sedang Visit</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center" style="color:var(--slate);">Tidak ada data kunjungan sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t" style="border-color:var(--border);">
        {{ $plans->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($plans as $plan)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $plan->customer->name }}</div>
                <div class="text-xs" style="color:var(--slate);">{{ $plan->employee->full_name ?? '-' }}</div>
            </div>
            @if($plan->status == 'planned')
                <span class="badge badge-amber">Planned</span>
            @elseif($plan->status == 'completed' && $plan->visit)
                @if($plan->visit->check_out_at)
                    <span class="badge badge-green">Selesai</span>
                @else
                    <span class="badge badge-slate" style="background:var(--ink); color:#fff;">Sedang Visit</span>
                @endif
            @endif
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Waktu Check-in:</span>
                <span class="font-semibold mono text-right">
                    @if($plan->visit && $plan->visit->check_in_at)
                        {{ $plan->visit->check_in_at->format('H:i') }}
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Jarak GPS:</span>
                @if($plan->visit) 
                    <span class="font-semibold mono text-right" style="{{ $plan->visit->distance_meters > 200 ? 'color:var(--red);' : '' }}">{{ $plan->visit->distance_meters }} m</span> 
                @else 
                    <span class="font-semibold text-right">-</span> 
                @endif
            </div>
            <div class="flex justify-between items-center">
                <span style="color:var(--slate);">Bukti Foto:</span>
                @if($plan->visit && $plan->visit->check_in_photo)
                    <a href="{{ asset('storage/' . $plan->visit->check_in_photo) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">Lihat Foto</a>
                @else
                    <span class="font-semibold text-right">-</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Tidak ada data kunjungan sesuai filter.
    </div>
    @endforelse
    
    <!-- Pagination Mobile -->
    @if($plans->hasPages())
    <div class="mt-4">
        {{ $plans->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
    @endif
</div>
@endsection