@extends('layouts.app')

@section('title', 'Monitoring Kunjungan')

@section('content')
<div class="mb-6">
    <h2 class="display text-2xl">Monitoring Kunjungan</h2>
    <p class="text-sm" style="color:var(--slate);">Pantau aktivitas salesman lapangan hari ini.</p>
</div>

<div class="card overflow-hidden">
    <!-- Header: Filter Data -->
    <div class="p-4 border-b flex flex-col md:flex-row justify-between items-center gap-4" style="border-color:var(--border); background:var(--paper-dim);">
        <div class="condensed">Aktivitas Hari Ini ({{ $plans->total() }})</div>
        <form method="GET" action="{{ route('salesman.visits.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select name="status" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full md:w-auto" style="border-color:var(--border);">
                <option value="">Semua Status</option>
                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Belum Check-in</option>
                <option value="visited" {{ request('status') == 'visited' ? 'selected' : '' }}>Sedang Visit</option>
                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Selesai</option>
            </select>

            <select name="employee_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full md:w-auto" style="border-color:var(--border);">
                <option value="">Semua Salesman</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                @endforeach
            </select>

            <select name="customer_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full md:w-auto" style="border-color:var(--border);">
                <option value="">Semua Toko Mitra</option>
                @foreach($customers as $cust)
                    <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Table -->
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
</div>
<div class="mt-4">
    {{ $plans->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
</div>
@endsection