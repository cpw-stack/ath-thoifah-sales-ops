<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  *{box-sizing:border-box;}
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

  .wrap{position:relative; z-index:2; padding:34px 54px; height:100vh; display:flex; flex-direction:column;}
  .header{display:flex; align-items:center; justify-content:space-between; margin-bottom:22px;}
  .brand-eyebrow{font-size:13px; letter-spacing:.24em; text-transform:uppercase; color:var(--slate);}
  .brand-title{font-size:30px; margin-top:4px;}
  .clock-box{text-align:right;}
  .clock{font-size:34px;}
  .dateline{font-size:13px; color:var(--slate); margin-top:2px; letter-spacing:.05em;}

  .stage{flex:1; position:relative;}
  .slide{position:absolute; inset:0; opacity:0; transition:opacity .7s ease; pointer-events:none; display:flex; flex-direction:column;}
  .slide.active{opacity:1; pointer-events:auto;}

  .lb-title{font-size:15px; letter-spacing:.18em; text-transform:uppercase; color:var(--orange-glow); margin-bottom:18px; text-align:center;}

  /* Podium & Avatar */
  .podium{display:flex; align-items:flex-end; gap:26px; justify-content:center; margin:10px 0 30px;}
  .pod{
    background:linear-gradient(180deg,#203450,#16283F);
    border-radius:18px 18px 8px 8px; text-align:center; padding:20px 26px; position:relative;
    border:1px solid rgba(255,255,255,.06);
  }
  .pod.first{height:260px; border-top:4px solid var(--amber);}
  .pod.second{height:220px; border-top:4px solid #C7CEDA;}
  .pod.third{height:190px; border-top:4px solid #C97C4A;}
  .pod .rank{font-family:'Archivo Black',sans-serif; font-size:44px; color:var(--orange); margin-top:10px;}
  .pod .name{font-size:19px; font-weight:700; margin-top:6px;}
  .pod .score{font-family:'JetBrains Mono',monospace; font-size:15px; color:var(--green); margin-top:4px;}
  .medal{font-size:26px; position:absolute; top:-15px; left:50%; transform:translateX(-50%);}

  .avatar{width:70px; height:70px; border-radius:50%; background:var(--ink); margin:0 auto 10px; overflow:hidden; border:3px solid var(--slate); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; color:var(--paper);}
  .pod.first .avatar{width:90px; height:90px; border-color:var(--amber);}
  .avatar img{width:100%; height:100%; object-fit:cover;}

  /* Leaderboard List */
  .lb-list{max-width:900px; margin:0 auto; width:100%;}
  .lb-row{display:flex; align-items:center; gap:20px; padding:12px 18px; border-bottom:1px solid rgba(255,255,255,.07);}
  .lb-rank{font-family:'JetBrains Mono',monospace; font-size:18px; width:34px; color:var(--slate);}
  .lb-avatar{width:34px; height:34px; border-radius:50%; background:var(--ink2); overflow:hidden; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;}
  .lb-avatar img{width:100%; height:100%; object-fit:cover;}
  .lb-name{flex:1; font-size:17px; font-weight:600;}
  .lb-bar-track{width:260px; height:10px; background:rgba(255,255,255,.08); border-radius:6px; overflow:hidden;}
  .lb-bar-fill{height:100%; background:linear-gradient(90deg,var(--orange),var(--orange-glow)); border-radius:6px;}
  .lb-score{width:110px; text-align:right; font-family:'JetBrains Mono',monospace; font-size:15px; color:var(--green);}

  /* Gauges */
  .gauge-grid{display:flex; justify-content:center; gap:60px; align-items:center; height:100%;}
  .gauge-item{text-align:center;}
  .gauge-title{font-size:15px; letter-spacing:.14em; text-transform:uppercase; color:var(--slate); margin-top:16px;}

  /* Top Performer */
  .stamp-slide{display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; text-align:center;}
  .stamp-avatar{width:140px; height:140px; border-radius:50%; background:var(--ink); margin-bottom:20px; overflow:hidden; border:4px solid var(--green); display:flex; align-items:center; justify-content:center; font-size:48px; font-weight:700;}
  .stamp-avatar img{width:100%; height:100%; object-fit:cover;}
  .stamp-name{font-size:54px; margin:14px 0 6px;}
  .stamp-badge{
    border:4px solid var(--green); color:var(--green); font-family:'Barlow Condensed',sans-serif; font-weight:800;
    font-size:20px; letter-spacing:.14em; text-transform:uppercase; padding:8px 26px; border-radius:10px; transform:rotate(-3deg);
    margin-top:18px; display:inline-block;
  }
  .quote{font-size:22px; color:var(--slate); font-style:italic; max-width:700px; margin-top:26px; line-height:1.5;}

  /* Top Products Slide */
  .prod-list{max-width:800px; margin:0 auto; width:100%; margin-top:20px;}
  .prod-row{display:flex; align-items:center; gap:20px; padding:15px 20px; background:rgba(255,255,255,0.03); border-radius:12px; margin-bottom:10px; border:1px solid rgba(255,255,255,0.05);}
  .prod-rank{font-family:'Archivo Black',sans-serif; font-size:24px; color:var(--orange); width:40px;}
  .prod-info{flex:1;}
  .prod-name{font-size:18px; font-weight:700;}
  .prod-qty{font-size:13px; color:var(--slate); margin-top:2px;}
  .prod-bar-track{width:200px; height:8px; background:rgba(255,255,255,0.1); border-radius:4px; overflow:hidden;}
  .prod-bar-fill{height:100%; background:linear-gradient(90deg, var(--green), #4EC9A1); border-radius:4px;}
  .prod-total{width:80px; text-align:right; font-family:'JetBrains Mono',monospace; font-size:16px; font-weight:700; color:var(--paper);}

  /* Metric Bars Slide (Slide 5) */
  .metric-grid{max-width:800px; margin:0 auto; width:100%; padding-top:20px;}
  .metric-row{margin-bottom:28px;}
  .metric-header{display:flex; justify-content:space-between; margin-bottom:8px; font-size:16px;}
  .metric-label{font-weight:700;}
  .metric-value{font-family:'JetBrains Mono',monospace; color:var(--orange-glow);}
  .metric-bar-bg{height:24px; background:rgba(255,255,255,0.08); border-radius:12px; overflow:hidden; position:relative;}
  .metric-bar-target{position:absolute; top:0; left:0; height:100%; width:100%; background:repeating-linear-gradient(45deg, rgba(255,255,255,0.05), rgba(255,255,255,0.05) 10px, transparent 10px, transparent 20px);}
  .metric-bar-fill{height:100%; border-radius:12px; background:linear-gradient(90deg, var(--orange), var(--orange-glow)); position:relative; z-index:2; transition:width 1s ease;}
  .metric-bar-fill.green{background:linear-gradient(90deg, var(--green), #4EC9A1);}
  .metric-bar-fill.red{background:linear-gradient(90deg, var(--red), #FF6B5A);}
  .metric-pct{text-align:right; font-size:13px; margin-top:4px; font-family:'JetBrains Mono',monospace; color:var(--slate);}

  /* Ticker */
  .ticker{
    margin-top:20px; border-top:1px solid rgba(255,255,255,.08); padding-top:14px;
    display:flex; align-items:center; gap:16px; overflow:hidden;
  }
  .ticker-label{
    background:var(--orange); color:#12100C; font-weight:800; font-size:12px; letter-spacing:.1em;
    padding:6px 14px; border-radius:6px; flex-shrink:0; text-transform:uppercase;
    position: relative; z-index: 10;
  }
  .ticker-track{
    display:flex; gap:50px; white-space:nowrap; animation:scroll 28s linear infinite;
    min-width: 0;
  }
  .ticker-item{font-size:14.5px; color:#C7D2E3;}
  .ticker-item b{color:var(--paper);}
  @keyframes scroll{ from{transform:translateX(0);} to{transform:translateX(-50%);} }

  .dots{display:flex; gap:8px; justify-content:center; margin-top:14px;}
  .dot{width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,.2); transition:background .3s;}
  .dot.on{background:var(--orange);}

  /* === RESPONSIVE MOBILE === */
  @media (max-width: 640px) {
    .wrap{padding:20px 15px;}
    .brand-title{font-size:20px;}
    .clock{font-size:20px;}
    .dateline{font-size:10px;}
    .lb-title{font-size:12px;}
    
    .podium{gap:10px; margin:10px 0 20px;}
    .pod{padding:10px 8px;}
    .pod.first{height:180px;}
    .pod.second{height:150px;}
    .pod.third{height:130px;}
    .pod .rank{font-size:24px;}
    .pod .name{font-size:11px;}
    .pod .score{font-size:10px;}
    .avatar{width:40px; height:40px; font-size:16px; margin-bottom:5px;}
    .pod.first .avatar{width:50px; height:50px;}
    
    .lb-row{gap:10px; padding:8px 5px;}
    .lb-rank{font-size:12px; width:20px;}
    .lb-avatar{width:24px; height:24px; font-size:10px;}
    .lb-name{font-size:12px;}
    .lb-bar-track{width:50px; height:6px;}
    .lb-score{font-size:11px; width:60px;}
    
    .gauge-grid{flex-wrap:wrap; gap:20px;}
    .gauge-item svg{width:100px; height:100px;}
    .gauge-item svg text{font-size:20px;}
    .gauge-title{font-size:11px; margin-top:5px;}
    
    .stamp-avatar{width:80px; height:80px; font-size:30px;}
    .stamp-name{font-size:28px;}
    .stamp-badge{font-size:14px; padding:5px 15px;}
    .quote{font-size:13px; padding:0 10px;}
    
    .prod-row{padding:10px; gap:10px;}
    .prod-rank{font-size:16px; width:20px;}
    .prod-name{font-size:12px;}
    .prod-qty{font-size:10px;}
    .prod-bar-track{width:60px;}
    .prod-total{font-size:11px; width:50px;}
    
    .metric-header{font-size:12px;}
    .metric-value{font-size:10px;}
    .metric-bar-bg{height:16px;}
    .metric-pct{font-size:10px;}
    
    .ticker-label{font-size:10px; padding:4px 8px;}
    .ticker-item{font-size:11px;}
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
            <div class="lb-bar-track"><div class="lb-bar-fill" style="width: {{ ($s->total_sales / $maxSales) * 100 }}%"></div></div>
            <span class="lb-score">Rp {{ number_format($s->total_sales / 1000000, 1) }}jt</span>
        </div>
        @endforeach
      </div>
    </div>

    <!-- SLIDE 2: TARGET GAUGES -->
    <div class="slide" id="slide-1">
      <div class="lb-title">🎯 Pencapaian Target Tim — {{ now()->translatedFormat('F Y') }}</div>
      <div class="gauge-grid">
        @foreach($gauges as $g)
        <div class="gauge-item">
          <svg width="180" height="180" viewBox="0 0 180 180">
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
      @endif
    </div>

    <!-- SLIDE 4: TOP PRODUCTS -->
    <div class="slide" id="slide-3">
      <div class="lb-title">🔥 Produk Paling Laris Bulan Ini</div>
      <div class="prod-list">
        @foreach($topProducts as $i => $p)
        <div class="prod-row">
          <div class="prod-rank">{{ $i + 1 }}</div>
          <div class="prod-info">
            <div class="prod-name">{{ $p->product->name }}</div>
            <div class="prod-qty">Terjual {{ $p->total_qty }} Pcs</div>
          </div>
          <div class="prod-bar-track">
            <div class="prod-bar-fill" style="width: {{ ($p->total_qty / $maxQty) * 100 }}%"></div>
          </div>
          <div class="prod-total">{{ $p->total_qty }} Pcs</div>
        </div>
        @endforeach
      </div>
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
      @php $tickerItems = $tickerItems->merge($tickerItems); @endphp
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