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

        <!-- Salesman Berisiko -->
        <div class="card p-5">
            <div class="sectiontitle mb-4">Salesman Berisiko</div>
            <div class="space-y-4">
                @foreach($atRisk as $s)
                <div class="flex items-center gap-3">
                    <div class="avatar">{{ $s['inisial'] }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold truncate">{{ $s['nama'] }}</div>
                        <div class="text-[11px]" style="color:var(--slate);">{{ $s['alasan'] }}</div>
                    </div>
                    <span class="badge badge-red">{{ $s['pct'] }}%</span>
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

</div>
@endsection