@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Row 1: KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="card kpi-card">
            <div class="kpi-label">Visit Completion</div>
            <div class="kpi-value">{{ $stats['visit_pct'] }}%</div>
            <span class="kpi-delta up">▲ {{ $stats['visit_delta'] }}</span>
        </div>
        <div class="card kpi-card">
            <div class="kpi-label">Order Hari Ini</div>
            <div class="kpi-value">{{ $stats['orders'] }}</div>
            <span class="kpi-delta up">▲ {{ $stats['order_delta'] }}</span>
        </div>
        <div class="card kpi-card">
            <div class="kpi-label">Sales Value</div>
            <div class="kpi-value">{{ $stats['sales_value'] }}</div>
            <span class="kpi-delta up">▲ {{ $stats['sales_delta'] }}</span>
        </div>
        <div class="card kpi-card">
            <div class="kpi-label">Collection</div>
            <div class="kpi-value">{{ $stats['collections'] }}</div>
            <span class="kpi-delta down">▼ {{ $stats['collection_delta'] }}</span>
        </div>
    </div>

    <!-- Row 2: Chart & Risk Salesman -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Bar Chart Kunjungan -->
        <div class="card p-5 lg:col-span-2">
            <div class="sectiontitle mb-1">Kunjungan per Area — 7 hari terakhir</div>
            <div class="text-xs mb-4" style="color:var(--slate);">Jumlah kunjungan valid tercatat per hari</div>
            <div class="barchart px-2">
                @foreach($weekVisits as $d)
                <div class="bar" style="height: {{ ($d['val'] / $maxVisit) * 100 }}%">
                    <div class="bar-value">{{ $d['val'] }}</div>
                    <div class="bar-label">{{ $d['day'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Performers -->
        <div class="card p-5">
            <div class="sectiontitle mb-4">🏆 Top Performers Bulan Ini</div>
            <div class="space-y-4">
                @foreach($topPerformers as $i => $emp)
                <div class="flex items-center gap-3">
                    <span class="condensed text-lg w-6" style="color: {{ $i == 0 ? 'var(--amber)' : 'var(--slate)' }};">{{ $i + 1 }}</span>
                    <div class="avatar">{{ strtoupper(substr($emp->full_name, 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold truncate">{{ $emp->full_name }}</div>
                        <div class="text-[11px]" style="color:var(--slate);">Area: {{ $emp->salesArea->name ?? '-' }}</div>
                    </div>
                    <span class="badge badge-green mono">Rp {{ number_format($emp->orders_sum_total_amount / 1000000, 1) }}jt</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Row 3: Target Metrics & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Target Achievement -->
        <div class="card p-5">
            <div class="sectiontitle mb-4">Target Achievement per Metric</div>
            <div class="space-y-4">
                @foreach($orgMetrics as $m)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-semibold">{{ $m['label'] }}</span>
                        <span class="mono" style="color:var(--slate);">{{ $m['pct'] }}%</span>
                    </div>
                    <div style="background:var(--paper-dim); border-radius:8px; height:9px; overflow:hidden;">
                        <div style="width: {{ $m['pct'] }}%; height:100%; border-radius:8px; background: {{ $m['pct'] >= 70 ? 'var(--green)' : ($m['pct'] >= 50 ? 'var(--amber)' : 'var(--red)') }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="card p-5">
            <div class="sectiontitle mb-4">Aktivitas Terbaru</div>
            <div class="space-y-4">
                @foreach($recentActivities as $a)
                <div class="flex items-start gap-3 text-sm">
                    <span class="mono text-[11px] mt-0.5 flex-shrink-0" style="color:var(--slate); width:40px;">{{ $a['time'] }}</span>
                    <div class="flex-1">
                        <span class="font-semibold">{{ $a['who'] }}</span> 
                        <span style="color:var(--slate);">{{ $a['what'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Row 4: Actionable Alerts (Perlu Tindakan) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Piutang Jatuh Tempo -->
        <div class="card p-5">
            <div class="flex justify-between items-center mb-4">
                <div class="sectiontitle">Piutang Jatuh Tempo</div>
                <span class="badge badge-red"> Mendesak </span>
            </div>
            <div class="space-y-3">
                @forelse ($dueReceivables as $r)
                <a href="{{ route('admin.customers.show', $r->customer_id) }}" class="block p-3 rounded-lg border hover:bg-gray-50 transition-colors" style="border-color:var(--border);">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold" style="color:var(--ink);">{{ $r->customer->name }}</span>
                        <span class="mono text-xs font-bold {{ $r->due_date < today() ? 'text-red-600' : 'text-amber-600' }}">
                            {{ $r->due_date < today() ? 'Overdue' : $r->due_date->diffInDays(today()) . ' hari lagi' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span style="color:var(--slate);">Sisa Tagihan</span>
                        <span class="mono font-bold">Rp {{ number_format($r->total_amount - $r->paid_amount, 0, ',', '.') }}</span>
                    </div>
                </a>
                @empty
                <div class="text-center text-sm py-6" style="color:var(--slate);">
                    Tidak ada piutang jatuh tempo.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Stok Gudang Menipis -->
        <div class="card p-5">
            <div class="flex justify-between items-center mb-4">
                <div class="sectiontitle">Stok Menipis</div>
                <span class="badge badge-amber"> < {{ $lowStockThreshold }} pcs </span>
            </div>
            <div class="space-y-3">
                @forelse ($lowStockProducts as $p)
                <a href="{{ route('admin.products.edit', $p) }}" class="block p-3 rounded-lg border hover:bg-gray-50 transition-colors" style="border-color:var(--border);">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-sm font-semibold" style="color:var(--ink);">{{ $p->name }}</div>
                            <div class="text-[11px] mono" style="color:var(--slate);">{{ $p->sku }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-red-600">{{ $p->stock }}</div>
                            <div class="text-[10px] uppercase" style="color:var(--slate);">Tersisa</div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-sm py-6" style="color:var(--slate);">
                    Stok semua produk aman.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Order Perlu Diproses -->
        <div class="card p-5">
            <div class="flex justify-between items-center mb-4">
                <div class="sectiontitle">Order Perlu Diproses</div>
                <span class="badge badge-slate"> Pending </span>
            </div>
            <div class="space-y-3">
                @forelse ($pendingOrders as $o)
                <a href="{{ route('admin.orders.show', $o) }}" class="block p-3 rounded-lg border hover:bg-gray-50 transition-colors" style="border-color:var(--border);">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold" style="color:var(--ink);">{{ $o->customer->name }}</span>
                        <span class="mono text-xs font-bold" style="color:var(--orange);">Rp {{ number_format($o->total_amount / 1000, 0) }}rb</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="mono" style="color:var(--slate);">{{ $o->order_code }}</span>
                        <span class="badge {{ $o->payment_type == 'cash' ? 'badge-green' : ($o->payment_type == 'konsinyasi' ? 'badge-amber' : 'badge-red') }}">{{ ucfirst($o->payment_type) }}</span>
                    </div>
                </a>
                @empty
                <div class="text-center text-sm py-6" style="color:var(--slate);">
                    Tidak ada order pending.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection