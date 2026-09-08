@extends('layouts.app')

@section('title', 'Detail Salesman')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="display text-2xl">Detail Salesman</h2>
        <p class="text-sm" style="color:var(--slate);">Statistik dan performa salesman.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.employees.pdf', $employee) }}" target="_blank" class="btn" style="background:#dc2626;">📄 Download PDF</a>
        <a href="{{ route('admin.employees.index') }}" class="btn-outline">← Kembali</a>
    </div>
</div>

<!-- Info Dasar Salesman -->
<div class="card p-5 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Nama Salesman</div>
        <div class="text-lg font-bold" style="color:var(--ink);">{{ $employee->full_name }}</div>
        <div class="text-xs mono" style="color:var(--slate);">{{ $employee->employee_code }}</div>
    </div>
    <div>
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Area & Tipe</div>
        <div class="text-sm font-semibold" style="color:var(--ink);">{{ $employee->salesArea->name ?? '-' }}</div>
        <div class="text-sm">
            @if($employee->type == 'online')
                <span class="badge badge-green">Online</span>
            @else
                <span class="badge badge-amber">Offline</span>
            @endif
        </div>
    </div>
    <div>
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Kontak</div>
        <div class="text-sm" style="color:var(--ink);">{{ $employee->user->name ?? '-' }}</div>
        <div class="text-xs" style="color:var(--slate);">{{ $employee->user->email ?? '-' }}</div>
    </div>
</div>

<!-- Statistik & Target Bulan Ini -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    <!-- Target Achievement -->
    <div class="card p-5">
        <div class="sectiontitle mb-4">Pencapaian Target Bulan Ini</div>
        @if($target)
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold">Kunjungan</span>
                    <span class="mono" style="color:var(--slate);">{{ $metrics['visit'] }}%</span>
                </div>
                <div style="background:var(--paper-dim); border-radius:8px; height:9px; overflow:hidden;">
                    <div style="width: {{ $metrics['visit'] }}%; height:100%; border-radius:8px; background: {{ $metrics['visit'] >= 70 ? 'var(--green)' : ($metrics['visit'] >= 50 ? 'var(--amber)' : 'var(--red)') }};"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold">Order</span>
                    <span class="mono" style="color:var(--slate);">{{ $metrics['order'] }}%</span>
                </div>
                <div style="background:var(--paper-dim); border-radius:8px; height:9px; overflow:hidden;">
                    <div style="width: {{ $metrics['order'] }}%; height:100%; border-radius:8px; background: {{ $metrics['order'] >= 70 ? 'var(--green)' : ($metrics['order'] >= 50 ? 'var(--amber)' : 'var(--red)') }};"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold">Sales Value</span>
                    <span class="mono" style="color:var(--slate);">{{ $metrics['sales'] }}%</span>
                </div>
                <div style="background:var(--paper-dim); border-radius:8px; height:9px; overflow:hidden;">
                    <div style="width: {{ $metrics['sales'] }}%; height:100%; border-radius:8px; background: {{ $metrics['sales'] >= 70 ? 'var(--green)' : ($metrics['sales'] >= 50 ? 'var(--amber)' : 'var(--red)') }};"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold">Collection</span>
                    <span class="mono" style="color:var(--slate);">{{ $metrics['collection'] }}%</span>
                </div>
                <div style="background:var(--paper-dim); border-radius:8px; height:9px; overflow:hidden;">
                    <div style="width: {{ $metrics['collection'] }}%; height:100%; border-radius:8px; background: {{ $metrics['collection'] >= 70 ? 'var(--green)' : ($metrics['collection'] >= 50 ? 'var(--amber)' : 'var(--red)') }};"></div>
                </div>
            </div>
        </div>
        @else
        <p class="text-sm text-center py-4" style="color:var(--slate);">Belum ada target ditetapkan untuk bulan ini.</p>
        @endif
    </div>

    <!-- Statistik Real -->
    <div class="card p-5">
        <div class="sectiontitle mb-4">Statistik Real Bulan Ini</div>
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 rounded-lg" style="background:var(--paper-dim);">
                <div class="text-xs uppercase font-bold" style="color:var(--slate);">Kunjungan</div>
                <div class="text-2xl font-bold" style="color:var(--ink);">{{ $stats['visits'] }}</div>
                <div class="text-xs" style="color:var(--slate);">Target: {{ $target->visit_target ?? 0 }}</div>
            </div>
            <div class="p-4 rounded-lg" style="background:var(--paper-dim);">
                <div class="text-xs uppercase font-bold" style="color:var(--slate);">Total Order</div>
                <div class="text-2xl font-bold" style="color:var(--ink);">{{ $stats['orders'] }}</div>
                <div class="text-xs" style="color:var(--slate);">Target: {{ $target->order_target ?? 0 }}</div>
            </div>
            <div class="p-4 rounded-lg" style="background:var(--paper-dim);">
                <div class="text-xs uppercase font-bold" style="color:var(--slate);">Nilai Penjualan</div>
                <div class="text-xl font-bold" style="color:var(--ink);">Rp {{ number_format($stats['sales'], 0, ',', '.') }}</div>
                <div class="text-xs" style="color:var(--slate);">Target: Rp {{ number_format($target->sales_target ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="p-4 rounded-lg" style="background:var(--paper-dim);">
                <div class="text-xs uppercase font-bold" style="color:var(--slate);">Penagihan (Collection)</div>
                <div class="text-xl font-bold" style="color:var(--ink);">Rp {{ number_format($stats['collections'], 0, ',', '.') }}</div>
                <div class="text-xs" style="color:var(--slate);">Target: Rp {{ number_format($target->collection_target ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Aktivitas Bulan Ini -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Monthly Visits -->
    <div class="card p-5">
        <div class="flex justify-between items-center mb-4">
            <div class="sectiontitle">Riwayat Kunjungan Bulan Ini</div>
            <span class="badge badge-slate">{{ $monthlyVisits->count() }} Kunjungan</span>
        </div>
        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
            @forelse($monthlyVisits as $v)
            <div class="flex justify-between items-center pb-3 border-b" style="border-color:var(--border);">
                <div>
                    <div class="text-sm font-semibold" style="color:var(--ink);">{{ $v->customer->name ?? '-' }}</div>
                    <div class="text-xs" style="color:var(--slate);">{{ $v->check_in_at->format('d M Y H:i') }}</div>
                </div>
                @if($v->check_out_at)
                    <span class="badge badge-green">Selesai</span>
                @else
                    <span class="badge badge-amber">Sedang Visit</span>
                @endif
            </div>
            @empty
            <p class="text-sm text-center py-4" style="color:var(--slate);">Belum ada kunjungan bulan ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Monthly Orders -->
    <div class="card p-5">
        <div class="flex justify-between items-center mb-4">
            <div class="sectiontitle">Riwayat Order Bulan Ini</div>
            <span class="badge badge-slate">{{ $monthlyOrders->count() }} Order</span>
        </div>
        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
            @forelse($monthlyOrders as $o)
            <div class="flex justify-between items-center pb-3 border-b" style="border-color:var(--border);">
                <div>
                    <div class="text-sm font-semibold" style="color:var(--ink);">{{ $o->customer->name ?? '-' }}</div>
                    <div class="text-xs mono" style="color:var(--slate);">{{ $o->order_code }} - {{ $o->created_at->format('d M Y') }}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold" style="color:var(--ink);">Rp {{ number_format($o->total_amount, 0, ',', '.') }}</div>
                    <span class="badge {{ $o->status == 'delivered' ? 'badge-green' : 'badge-slate' }}">{{ $o->status }}</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-center py-4" style="color:var(--slate);">Belum ada order bulan ini.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection