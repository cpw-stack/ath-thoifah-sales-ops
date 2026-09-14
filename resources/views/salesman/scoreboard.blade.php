@extends('layouts.mobile')

@section('content')
<style>
    /* Styling khusus untuk halaman scoreboard mobile agar terlihat menarik */
    .sb-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 16px;
    }
    .sb-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--slate);
        margin-bottom: 12px;
        text-align: center;
    }
    
    /* Podium */
    .podium-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1fr;
        gap: 8px;
        align-items: flex-end;
        margin-bottom: 20px;
    }
    .pod {
        background: var(--paper-dim);
        border-radius: 12px 12px 4px 4px;
        padding: 12px 8px;
        text-align: center;
        position: relative;
    }
    .pod-1 { border-top: 4px solid var(--orange); padding-top: 16px; }
    .pod-2 { border-top: 4px solid #C7CEDA; }
    .pod-3 { border-top: 4px solid #C97C4A; }
    .pod-medal { font-size: 20px; position: absolute; top: -14px; left: 50%; transform: translateX(-50%); }
    .pod-avatar { width: 48px; height: 48px; border-radius: 50%; background: var(--ink); margin: 0 auto 6px; overflow:hidden; border: 2px solid #fff; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:bold; }
    .pod-1 .pod-avatar { width: 56px; height: 56px; border-color: var(--orange); }
    .pod-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .pod-name { font-size: 11px; font-weight: 700; color: var(--ink); line-height: 1.2; }
    .pod-score { font-size: 10px; font-family: 'JetBrains Mono', monospace; color: var(--green); margin-top: 2px; font-weight: bold; }

    /* List */
    .lb-list { display: flex; flex-direction: column; gap: 8px; }
    .lb-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--border); }
    .lb-row:last-child { border-bottom: none; }
    .lb-rank { font-size: 12px; font-weight: bold; color: var(--slate); width: 20px; text-align: center; font-family: 'JetBrains Mono', monospace; }
    .lb-avatar { width: 28px; height: 28px; border-radius: 50%; background: var(--paper-dim); overflow: hidden; display:flex; align-items:center; justify-content:center; font-size: 10px; font-weight: bold; color: var(--ink); flex-shrink: 0; }
    .lb-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .lb-name { flex: 1; font-size: 13px; font-weight: 600; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lb-score { font-size: 12px; font-family: 'JetBrains Mono', monospace; font-weight: bold; color: var(--green); }

    /* Gauges */
    .gauges-flex { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
    .gauge-item { text-align: center; width: 45%; max-width: 140px; }
    .gauge-svg { width: 100%; height: auto; }
    .gauge-label { font-size: 11px; font-weight: 700; color: var(--slate); margin-top: -8px; text-transform: uppercase; }

    /* Metric Bars */
    .metric-row { margin-bottom: 12px; }
    .metric-head { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; }
    .metric-label { font-weight: 600; color: var(--ink); }
    .metric-val { font-family: 'JetBrains Mono', monospace; color: var(--orange); font-weight: 700; font-size: 11px; }
    .metric-bg { height: 12px; background: var(--paper-dim); border-radius: 6px; overflow: hidden; }
    .metric-fill { height: 100%; border-radius: 6px; background: linear-gradient(90deg, var(--orange), #FF8A54); }
    .metric-fill.green { background: linear-gradient(90deg, var(--green), #4EC9A1); }
    .metric-fill.red { background: linear-gradient(90deg, var(--red), #FF6B5A); }

    /* Top Performer */
    .performer-box { text-align: center; padding: 20px; background: linear-gradient(135deg, var(--ink) 0%, #2A3B54 100%); border-radius: 16px; color: #fff; }
    .performer-avatar { width: 70px; height: 70px; border-radius: 50%; border: 3px solid var(--green); margin: 0 auto 10px; overflow: hidden; background: #fff; display:flex; align-items:center; justify-content:center; font-size: 24px; font-weight: bold; color: var(--ink); }
    .performer-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .performer-name { font-size: 18px; font-weight: 800; font-family: 'Archivo Black', sans-serif; }
    .performer-badge { display: inline-block; margin-top: 8px; background: var(--green); color: #fff; font-size: 10px; font-weight: bold; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .05em; }

    /* Ticker */
    .ticker-box { background: var(--paper-dim); border-radius: 12px; padding: 8px 0; overflow: hidden; display: flex; align-items: center; }
    .ticker-label { background: var(--orange); color: #fff; font-size: 9px; font-weight: 800; padding: 4px 8px; border-radius: 4px; margin: 0 10px; flex-shrink: 0; text-transform: uppercase; }
    .ticker-track { display: flex; gap: 30px; white-space: nowrap; animation: scrollTx 25s linear infinite; }
    .ticker-item { font-size: 11px; color: var(--ink); font-weight: 500; }
    @keyframes scrollTx { from { transform: translateX(0); } to { transform: translateX(-50%); } }
</style>

<div class="p-4 space-y-5" style="padding-bottom: 40px;">
    
    <div>
        <h2 class="display text-xl" style="color:var(--ink);">Papan Skor Tim</h2>
        <p class="text-xs mt-1" style="color:var(--slate);">Peringkat penjualan bulan {{ now()->translatedFormat('F Y') }}.</p>
    </div>

    <!-- 1. PODIUM TOP 3 -->
    <div class="sb-card">
        <div class="sb-title">🏆 Top 3 Terbaik</div>
        <div class="podium-grid">
            <!-- Peringkat 2 -->
            @if(isset($top3[1]))
            <div class="pod pod-2">
                <div class="pod-medal">🥈</div>
                <div class="pod-avatar">
                    @if($top3[1]->user && $top3[1]->user->photo) <img src="{{ asset('storage/' . $top3[1]->user->photo) }}" alt=""> @else {{ strtoupper(substr($top3[1]->full_name, 0, 1)) }} @endif
                </div>
                <div class="pod-name">{{ $top3[1]->full_name }}</div>
                <div class="pod-score">Rp {{ number_format($top3[1]->total_sales / 1000000, 1) }}jt</div>
            </div>
            @endif

            <!-- Peringkat 1 -->
            @if(isset($top3[0]))
            <div class="pod pod-1">
                <div class="pod-medal">🥇</div>
                <div class="pod-avatar">
                    @if($top3[0]->user && $top3[0]->user->photo) <img src="{{ asset('storage/' . $top3[0]->user->photo) }}" alt=""> @else {{ strtoupper(substr($top3[0]->full_name, 0, 1)) }} @endif
                </div>
                <div class="pod-name">{{ $top3[0]->full_name }}</div>
                <div class="pod-score">Rp {{ number_format($top3[0]->total_sales / 1000000, 1) }}jt</div>
            </div>
            @endif

            <!-- Peringkat 3 -->
            @if(isset($top3[2]))
            <div class="pod pod-3">
                <div class="pod-medal">🥉</div>
                <div class="pod-avatar">
                    @if($top3[2]->user && $top3[2]->user->photo) <img src="{{ asset('storage/' . $top3[2]->user->photo) }}" alt=""> @else {{ strtoupper(substr($top3[2]->full_name, 0, 1)) }} @endif
                </div>
                <div class="pod-name">{{ $top3[2]->full_name }}</div>
                <div class="pod-score">Rp {{ number_format($top3[2]->total_sales / 1000000, 1) }}jt</div>
            </div>
            @endif
        </div>

        <!-- List 4 dst -->
        @if($rest->count() > 0)
        <div class="lb-list mt-4 border-t pt-2" style="border-color:var(--border);">
            @foreach($rest as $s)
            <div class="lb-row">
                <span class="lb-rank">{{ $loop->iteration + 3 }}</span>
                <div class="lb-avatar">
                    @if($s->user && $s->user->photo) <img src="{{ asset('storage/' . $s->user->photo) }}" alt=""> @else {{ strtoupper(substr($s->full_name, 0, 1)) }} @endif
                </div>
                <span class="lb-name">{{ $s->full_name }}</span>
                <span class="lb-score">Rp {{ number_format($s->total_sales / 1000000, 1) }}jt</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- 2. PENCAPAIAN TARGET (GAUGES) -->
    <div class="sb-card">
        <div class="sb-title">🎯 Pencapaian Target Tim</div>
        <div class="gauges-flex">
            @foreach($gauges as $g)
            <div class="gauge-item">
                <svg class="gauge-svg" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="50" stroke="#EDE6D6" stroke-width="10" fill="none"/>
                    <circle cx="60" cy="60" r="50" stroke="{{ $g['color'] }}" stroke-width="10" fill="none"
                            stroke-dasharray="314.16" stroke-dashoffset="{{ 314.16 * (1 - ($g['pct'] / 100)) }}" 
                            stroke-linecap="round" transform="rotate(-90 60 60)"/>
                    <text x="60" y="65" text-anchor="middle" fill="#1B2A41" font-family="Archivo Black" font-size="22">{{ $g['pct'] }}%</text>
                </svg>
                <div class="gauge-label" style="color: {{ $g['pct'] < 50 ? 'var(--red)' : 'var(--slate)' }}">{{ $g['title'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 3. TOP PERFORMER -->
    @if($topPerformer)
    <div class="performer-box">
        <div class="text-xs uppercase tracking-wider mb-2" style="color:#9DAEC7;">🌟 Bintang Utama Bulan Ini</div>
        <div class="performer-avatar">
            @if($topPerformer->user && $topPerformer->user->photo) <img src="{{ asset('storage/' . $topPerformer->user->photo) }}" alt=""> @else {{ strtoupper(substr($topPerformer->full_name, 0, 1)) }} @endif
        </div>
        <div class="performer-name">{{ $topPerformer->full_name }}</div>
        <div class="performer-badge">Target Tercapai {{ $topPerformerPct }}%</div>
    </div>
    @endif

    <!-- 4. PRODUK TERLARIS -->
    <div class="sb-card">
        <div class="sb-title">🔥 Produk Paling Laris</div>
        @if($topProducts->isNotEmpty())
        <div class="lb-list">
            @foreach($topProducts as $i => $p)
            <div class="lb-row">
                <span class="lb-rank" style="color:var(--orange);">{{ $i + 1 }}</span>
                <div class="lb-avatar" style="background:var(--orange); color:#fff; font-size:16px;">📦</div>
                <span class="lb-name">{{ $p->product->name }}</span>
                <span class="lb-score" style="color:var(--ink);">{{ $p->total_qty }} Pcs</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center text-xs py-4" style="color:var(--slate);">Belum ada produk terjual bulan ini.</div>
        @endif
    </div>

    <!-- 5. TARGET ACHIEVEMENT BARS -->
    <div class="sb-card">
        <div class="sb-title">📊 Statistik Per Metric</div>
        @foreach($metricBars as $m)
        <div class="metric-row">
            <div class="metric-head">
                <span class="metric-label">{{ $m['label'] }}</span>
                <span class="metric-val">{{ $m['actual'] }} / {{ $m['target'] }}</span>
            </div>
            <div class="metric-bg">
                <div class="metric-fill {{ $m['pct'] >= 70 ? 'green' : ($m['pct'] < 50 ? 'red' : '') }}" style="width: {{ $m['pct'] }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 6. LIVE TICKER -->
    @if($tickerItems->isNotEmpty())
    <div class="ticker-box">
        <div class="ticker-label">Live</div>
        <div class="ticker-track">
            @foreach($tickerItems as $t)<span class="ticker-item">{!! $t['text'] !!}</span>@endforeach
            @foreach($tickerItems as $t)<span class="ticker-item">{!! $t['text'] !!}</span>@endforeach
        </div>
    </div>
    @endif

</div>
@endsection