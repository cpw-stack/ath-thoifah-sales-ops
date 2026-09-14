<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Ath-Thoifah — Papan Skor Tim Sales</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#0F1B2D; --ink2:#16283F;
    --paper:#F6F2E9;
    --orange:#E8622C; --orange-glow:#FF8A54;
    --green:#3EA579; --green-soft:#173428;
    --amber:#E8B23C;
    --red:#E14F3A;
    --slate:#8FA0BC;
  }
  *{box-sizing:border-box; -webkit-tap-highlight-color: transparent;}
  body{
    margin:0; min-height:100vh; overflow:hidden;
    background:radial-gradient(circle at 20% 10%, #1B2E4A 0%, var(--ink) 55%);
    font-family:'Inter',sans-serif; color:var(--paper);
    position:relative;
  }
  .display{font-family:'Archivo Black',sans-serif;}
  .condensed{font-family:'Barlow Condensed',sans-serif; font-weight:800;}
  .mono{font-family:'JetBrains Mono',monospace;}

  body::before{
    content:''; position:absolute; inset:0; opacity:.05; pointer-events:none;
    background-image: radial-gradient(circle, #fff 1px, transparent 1px);
    background-size: 26px 26px;
  }

  .wrap{position:relative; z-index:2; padding:24px; height:100vh; display:flex; flex-direction:column;}
  .header{display:flex; align-items:center; justify-content:space-between; margin-bottom:15px;}
  .brand-eyebrow{font-size:11px; letter-spacing:.24em; text-transform:uppercase; color:var(--slate);}
  .brand-title{font-size:22px; margin-top:2px;}
  .clock-box{text-align:right;}
  .clock{font-size:20px;}
  .dateline{font-size:10px; color:var(--slate); margin-top:2px; letter-spacing:.05em;}

  .stage{flex:1; position:relative; min-height: 0;}
  .slide{position:absolute; inset:0; opacity:0; transition:opacity .7s ease; pointer-events:none; display:flex; flex-direction:column; overflow-y:auto;}
  .slide.active{opacity:1; pointer-events:auto;}

  .lb-title{font-size:13px; letter-spacing:.18em; text-transform:uppercase; color:var(--orange-glow); margin-bottom:15px; text-align:center;}

  /* Podium & Avatar */
  .podium{display:flex; align-items:flex-end; gap:15px; justify-content:center; margin:10px 0 20px;}
  .pod{
    background:linear-gradient(180deg,#203450,#16283F);
    border-radius:18px 18px 8px 8px; text-align:center; padding:15px 12px; position:relative;
    border:1px solid rgba(255,255,255,.06);
  }
  .pod.first{height:220px; border-top:4px solid var(--amber);}
  .pod.second{height:190px; border-top:4px solid #C7CEDA;}
  .pod.third{height:160px; border-top:4px solid #C97C4A;}
  .pod .rank{font-family:'Archivo Black',sans-serif; font-size:32px; color:var(--orange); margin-top:8px;}
  .pod .name{font-size:14px; font-weight:700; margin-top:4px;}
  .pod .score{font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--green); margin-top:4px;}
  .medal{font-size:24px; position:absolute; top:-15px; left:50%; transform:translateX(-50%);}

  .avatar{width:60px; height:60px; border-radius:50%; background:var(--ink); margin:0 auto 8px; overflow:hidden; border:3px solid var(--slate); display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; color:var(--paper);}
  .pod.first .avatar{width:80px; height:80px; border-color:var(--amber);}
  .avatar img{width:100%; height:100%; object-fit:cover;}

  /* Leaderboard List */
  .lb-list{max-width:900px; margin:0 auto; width:100%;}
  .lb-row{display:flex; align-items:center; gap:15px; padding:10px 12px; border-bottom:1px solid rgba(255,255,255,.07);}
  .lb-rank{font-family:'JetBrains Mono',monospace; font-size:14px; width:24px; color:var(--slate);}
  .lb-avatar{width:32px; height:32px; border-radius:50%; background:var(--ink2); overflow:hidden; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700;}
  .lb-avatar img{width:100%; height:100%; object-fit:cover;}
  .lb-name{flex:1; font-size:14px; font-weight:600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
  .lb-bar-track{width:80px; height:8px; background:rgba(255,255,255,.08); border-radius:6px; overflow:hidden; flex-shrink:0;}
  .lb-bar-fill{height:100%; background:linear-gradient(90deg,var(--orange),var(--orange-glow)); border-radius:6px;}
  .lb-score{width:70px; text-align:right; font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--green);}

  /* Gauges */
  .gauge-grid{display:flex; flex-wrap:wrap; justify-content:center; gap:30px; align-items:center; height:100%; padding: 20px 0;}
  .gauge-item{text-align:center;}
  .gauge-title{font-size:12px; letter-spacing:.14em; text-transform:uppercase; color:var(--slate); margin-top:10px;}

  /* Top Performer */
  .stamp-slide{display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; text-align:center; padding: 20px;}
  .stamp-avatar{width:100px; height:100px; border-radius:50%; background:var(--ink); margin-bottom:20px; overflow:hidden; border:4px solid var(--green); display:flex; align-items:center; justify-content:center; font-size:36px; font-weight:700;}
  .stamp-avatar img{width:100%; height:100%; object-fit:cover;}
  .stamp-name{font-size:32px; margin:14px 0 6px;}
  .stamp-badge{
    border:4px solid var(--green); color:var(--green); font-family:'Barlow Condensed',sans-serif; font-weight:800;
    font-size:16px; letter-spacing:.14em; text-transform:uppercase; padding:6px 20px; border-radius:10px; transform:rotate(-3deg);
    margin-top:18px; display:inline-block;
  }
  .quote{font-size:15px; color:var(--slate); font-style:italic; max-width:600px; margin-top:26px; line-height:1.5;}

  /* Top Products Slide */
  .prod-list{max-width:800px; margin:0 auto; width:100%; margin-top:10px;}
  .prod-row{display:flex; align-items:center; gap:15px; padding:12px 15px; background:rgba(255,255,255,0.03); border-radius:12px; margin-bottom:10px; border:1px solid rgba(255,255,255,0.05);}
  .prod-rank{font-family:'Archivo Black',sans-serif; font-size:18px; color:var(--orange); width:24px;}
  .prod-info{flex:1; min-width:0;}
  .prod-name{font-size:14px; font-weight:700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
  .prod-qty{font-size:11px; color:var(--slate); margin-top:2px;}
  .prod-bar-track{width:60px; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; overflow:hidden; flex-shrink:0;}
  .prod-bar-fill{height:100%; background:linear-gradient(90deg, var(--green), #4EC9A1); border-radius:4px;}
  .prod-total{width:60px; text-align:right; font-family:'JetBrains Mono',monospace; font-size:13px; font-weight:700; color:var(--paper);}

  /* Metric Bars Slide */
  .metric-grid{max-width:800px; margin:0 auto; width:100%; padding-top:10px;}
  .metric-row{margin-bottom:20px;}
  .metric-header{display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;}
  .metric-label{font-weight:700;}
  .metric-value{font-family:'JetBrains Mono',monospace; color:var(--orange-glow); font-size:12px;}
  .metric-bar-bg{height:20px; background:rgba(255,255,255,0.08); border-radius:12px; overflow:hidden; position:relative;}
  .metric-bar-target{position:absolute; top:0; left:0; height:100%; width:100%; background:repeating-linear-gradient(45deg, rgba(255,255,255,0.05), rgba(255,255,255,0.05) 10px, transparent 10px, transparent 20px);}
  .metric-bar-fill{height:100%; border-radius:12px; background:linear-gradient(90deg, var(--orange), var(--orange-glow)); position:relative; z-index:2; transition:width 1s ease;}
  .metric-bar-fill.green{background:linear-gradient(90deg, var(--green), #4EC9A1);}
  .metric-bar-fill.red{background:linear-gradient(90deg, var(--red), #FF6B5A);}
  .metric-pct{text-align:right; font-size:11px; margin-top:4px; font-family:'JetBrains Mono',monospace; color:var(--slate);}

  /* Ticker */
  .ticker{
    margin-top:15px; border-top:1px solid rgba(255,255,255,.08); padding-top:12px;
    display:flex; align-items:center; gap:16px; overflow:hidden;
  }
  .ticker-label{
    background:var(--orange); color:#12100C; font-weight:800; font-size:10px; letter-spacing:.1em;
    padding:4px 10px; border-radius:6px; flex-shrink:0; text-transform:uppercase;
    position: relative; z-index: 10;
  }
  .ticker-track{
    display:flex; gap:40px; white-space:nowrap; animation:scroll 30s linear infinite;
  }
  .ticker-item{font-size:12px; color:#C7D2E3;}
  .ticker-item b{color:var(--paper);}
  @keyframes scroll{ from{transform:translateX(0);} to{transform:translateX(-50%);} }

  .dots{display:flex; gap:6px; justify-content:center; margin-top:10px; flex-shrink:0;}
  .dot{width:6px; height:6px; border-radius:50%; background:rgba(255,255,255,.2); transition:background .3s;}
  .dot.on{background:var(--orange);}
  
  .empty-state{display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; text-align:center; color:var(--slate);}
  .empty-state div{font-size:13px; margin-top:5px;}

  @media (min-width: 768px) {
    .wrap{padding:34px 54px;}
    .brand-title{font-size:30px;}
    .clock{font-size:34px;}
    .dateline{font-size:13px;}
    .lb-title{font-size:15px; margin-bottom:18px;}
    .podium{gap:26px; margin:10px 0 30px;}
    .pod{padding:20px 26px;}
    .pod.first{height:260px;} .pod.second{height:220px;} .pod.third{height:190px;}
    .pod .rank{font-size:44px;} .pod .name{font-size:19px;} .pod .score{font-size:15px;}
    .avatar{width:70px; height:70px; font-size:24px;} .pod.first .avatar{width:90px; height:90px;}
    .lb-row{gap:20px; padding:12px 18px;} .lb-rank{font-size:18px; width:34px;} .lb-avatar{width:34px; height:34px; font-size:12px;}
    .lb-name{font-size:17px;} .lb-bar-track{width:260px; height:10px;} .lb-score{font-size:15px; width:110px;}
    .gauge-grid{gap:60px;} .gauge-item svg{width:180px; height:180px;} .gauge-title{font-size:15px;}
    .stamp-avatar{width:140px; height:140px; font-size:48px;} .stamp-name{font-size:54px;} .stamp-badge{font-size:20px; padding:8px 26px;}
    .quote{font-size:22px; max-width:700px;}
    .prod-row{padding:15px 20px; gap:20px;} .prod-rank{font-size:24px; width:40px;} .prod-name{font-size:18px;} .prod-qty{font-size:13px;}
    .prod-bar-track{width:200px; height:8px;} .prod-total{font-size:16px; width:80px;}
    .metric-grid{padding-top:20px;} .metric-row{margin-bottom:28px;} .metric-header{font-size:16px;} .metric-value{font-size:16px;}
    .metric-bar-bg{height:24px;} .metric-pct{font-size:13px;}
    .ticker-label{font-size:12px; padding:6px 14px;} .ticker-item{font-size:14.5px;}
    .dots{gap:8px; margin-top:14px;} .dot{width:8px; height:8px;}
  }
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div>
      <div class="brand-eyebrow">Ath-Thoifah · Sales Operations</div>
      <div class="display brand-title">Papan Semangat Tim</div>
    </div>
    <div class="clock-box">
      <div class="mono clock" id="clockEl">--:--:--</div>
      <div class="dateline" id="dateEl">—</div>
    </div>
  </div>

  <div class="stage">

    <!-- SLIDE 1: PODIUM + LEADERBOARD -->
    <div class="slide active" id="slide-0">
      <div class="lb-title">🏆 Ranking Penjualan Bulan Ini</div>
      
      @if($top3->isEmpty() && $rest->isEmpty())
        <div class="empty-state">
          <div style="font-size:40px; margin-bottom:10px;">📊</div>
          <div style="font-size:18px; font-weight:bold; color:var(--paper);">Belum Ada Penjualan</div>
          <div>Data ranking akan muncul setelah ada transaksi order.</div>
        </div>
      @else
        <div class="podium">
          @if(isset($top3[1]))
          <div class="pod second">
            <div class="medal">🥈</div>
            <div class="avatar">
              @if($top3[1]->user && $top3[1]->user->photo) <img src="{{ asset('storage/' . $top3[1]->user->photo) }}" alt="Foto"> @else {{ strtoupper(substr($top3[1]->full_name, 0, 1)) }} @endif
            </div>
            <div class="rank">2</div>
            <div class="name">{{ $top3[1]->full_name }}</div>
            <div class="score">Rp {{ number_format($top3[1]->total_sales, 0, ',', '.') }}</div>
          </div>
          @endif
          
          @if(isset($top3[0]))
          <div class="pod first">
            <div class="medal">🥇</div>
            <div class="avatar">
              @if($top3[0]->user && $top3[0]->user->photo) <img src="{{ asset('storage/' . $top3[0]->user->photo) }}" alt="Foto"> @else {{ strtoupper(substr($top3[0]->full_name, 0, 1)) }} @endif
            </div>
            <div class="rank">1</div>
            <div class="name">{{ $top3[0]->full_name }}</div>
            <div class="score">Rp {{ number_format($top3[0]->total_sales, 0, ',', '.') }}</div>
          </div>
          @endif

          @if(isset($top3[2]))
          <div class="pod third">
            <div class="medal">🥉</div>
            <div class="avatar">
              @if($top3[2]->user && $top3[2]->user->photo) <img src="{{ asset('storage/' . $top3[2]->user->photo) }}" alt="Foto"> @else {{ strtoupper(substr($top3[2]->full_name, 0, 1)) }} @endif
            </div>
            <div class="rank">3</div>
            <div class="name">{{ $top3[2]->full_name }}</div>
            <div class="score">Rp {{ number_format($top3[2]->total_sales, 0, ',', '.') }}</div>
          </div>
          @endif
        </div>
        <div class="lb-list">
          @foreach($rest as $s)
          <div class="lb-row">
              <span class="lb-rank">{{ str_pad($loop->iteration + 3, 2, '0', STR_PAD_LEFT) }}</span>
              <div class="lb-avatar">
                @if($s->user && $s->user->photo) <img src="{{ asset('storage/' . $s->user->photo) }}" alt="Foto"> @else {{ strtoupper(substr($s->full_name, 0, 1)) }} @endif
              </div>
              <span class="lb-name">{{ $s->full_name }}</span>
              <div class="lb-bar-track"><div class="lb-bar-fill" style="width: {{ ($s->total_sales / max(1, $maxSales)) * 100 }}%"></div></div>
              <span class="lb-score">Rp {{ number_format($s->total_sales / 1000000, 1) }}jt</span>
          </div>
          @endforeach
        </div>
      @endif
    </div>

    <!-- SLIDE 2: TARGET GAUGES -->
    <div class="slide" id="slide-1">
      <div class="lb-title">🎯 Pencapaian Target Tim — {{ now()->translatedFormat('F Y') }}</div>
      <div class="gauge-grid">
        @foreach($gauges as $g)
        <div class="gauge-item">
          <svg width="140" height="140" viewBox="0 0 180 180">
            <circle cx="90" cy="90" r="76" stroke="#233A57" stroke-width="16" fill="none"/>
            <circle cx="90" cy="90" r="76" stroke="{{ $g['color'] }}" stroke-width="16" fill="none"
              stroke-dasharray="477.5" stroke-dashoffset="{{ $g['offset'] }}" stroke-linecap="round" transform="rotate(-90 90 90)"/>
            <text x="90" y="98" text-anchor="middle" fill="#F6F2E9" font-family="Archivo Black" font-size="32">{{ $g['pct'] }}%</text>
          </svg>
          <div class="gauge-title" style="color: {{ $g['pct'] < 50 ? 'var(--red)' : 'var(--slate)' }};">{{ $g['title'] }} @if($g['pct'] < 50) ⚠ @endif</div>
        </div>
        @endforeach
      </div>
    </div>

    <!-- SLIDE 3: TOP PERFORMER -->
    <div class="slide" id="slide-2">
      @if($topPerformer)
      <div class="stamp-slide">
        <div class="brand-eyebrow">🌟 Salesman Terbaik Bulan Ini</div>
        <div class="stamp-avatar">
          @if($topPerformer->user && $topPerformer->user->photo) <img src="{{ asset('storage/' . $topPerformer->user->photo) }}" alt="Foto"> @else {{ strtoupper(substr($topPerformer->full_name, 0, 1)) }} @endif
        </div>
        <div class="display stamp-name">{{ $topPerformer->full_name }}</div>
        <div class="stamp-badge">Target Tercapai {{ $topPerformerPct }}%</div>
        <div class="quote">"Kunjungan yang jujur dan konsisten hari ini adalah fondasi kepercayaan mitra esok hari."</div>
      </div>
      @else
        <div class="empty-state">
          <div style="font-size:40px; margin-bottom:10px;">🌟</div>
          <div style="font-size:18px; font-weight:bold; color:var(--paper);">Belum Ada Top Performer</div>
          <div>Top performer akan muncul setelah ada penjualan.</div>
        </div>
      @endif
    </div>

    <!-- SLIDE 4: TOP PRODUCTS -->
    <div class="slide" id="slide-3">
      <div class="lb-title">🔥 Produk Paling Laris Bulan Ini</div>
      @if($topProducts->isNotEmpty())
      <div class="prod-list">
        @foreach($topProducts as $i => $p)
        <div class="prod-row">
          <div class="prod-rank">{{ $i + 1 }}</div>
          <div class="prod-info">
            <div class="prod-name">{{ $p->product->name }}</div>
            <div class="prod-qty">Terjual {{ $p->total_qty }} Pcs</div>
          </div>
          <div class="prod-bar-track">
            <div class="prod-bar-fill" style="width: {{ ($p->total_qty / max(1, $maxQty)) * 100 }}%"></div>
          </div>
          <div class="prod-total">{{ $p->total_qty }} Pcs</div>
        </div>
        @endforeach
      </div>
      @else
        <div class="empty-state">
          <div style="font-size:40px; margin-bottom:10px;">📦</div>
          <div style="font-size:18px; font-weight:bold; color:var(--paper);">Belum Ada Produk Terjual</div>
          <div>Statistik produk terlaris akan muncul di sini.</div>
        </div>
      @endif
    </div>

    <!-- SLIDE 5: TARGET ACHIEVEMENT BARS -->
    <div class="slide" id="slide-4">
      <div class="lb-title">📊 Target Achievement per Metric — {{ now()->translatedFormat('F Y') }}</div>
      <div class="metric-grid">
        @foreach($metricBars as $m)
        <div class="metric-row">
          <div class="metric-header">
            <span class="metric-label">{{ $m['label'] }}</span>
            <span class="metric-value">{{ $m['actual'] }} / {{ $m['target'] }}</span>
          </div>
          <div class="metric-bar-bg">
            <div class="metric-bar-target"></div>
            <div class="metric-bar-fill {{ $m['pct'] >= 70 ? 'green' : ($m['pct'] < 50 ? 'red' : '') }}" style="width: {{ $m['pct'] }}%"></div>
          </div>
          <div class="metric-pct">Pencapaian: {{ $m['pct'] }}%</div>
        </div>
        @endforeach
      </div>
    </div>

  </div>

  <div class="dots">
    <div class="dot on" id="dot-0"></div>
    <div class="dot" id="dot-1"></div>
    <div class="dot" id="dot-2"></div>
    <div class="dot" id="dot-3"></div>
    <div class="dot" id="dot-4"></div>
  </div>

  <div class="ticker">
    <div class="ticker-label">Live</div>
    <div class="ticker-track">
      @foreach($tickerItems as $t)
      <span class="ticker-item">{!! $t['text'] !!}</span>
      @endforeach
      {{-- Duplikat untuk efek infinite scroll tanpa putus --}}
      @foreach($tickerItems as $t)
      <span class="ticker-item">{!! $t['text'] !!}</span>
      @endforeach
    </div>
  </div>

</div>

<script>
  function updateClock(){
    const d = new Date();
    document.getElementById('clockEl').textContent = d.toLocaleTimeString('id-ID');
    document.getElementById('dateEl').textContent = d.toLocaleDateString('id-ID',{weekday:'long', day:'2-digit', month:'long', year:'numeric'});
  }
  updateClock();
  setInterval(updateClock, 1000);

  let idx = 0;
  const slides = document.querySelectorAll('.slide');
  const dots = document.querySelectorAll('.dot');
  function showSlide(i){
    slides.forEach((s,n)=>s.classList.toggle('active', n===i));
    dots.forEach((d,n)=>d.classList.toggle('on', n===i));
  }
  setInterval(()=>{ idx = (idx+1) % slides.length; showSlide(idx); }, 8000);
</script>
</body>
</html>