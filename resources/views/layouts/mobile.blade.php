<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Ath-Thoifah — Sales Force</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1B2A41">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#1B2A41; --ink-soft:#3A4A63; --paper:#F6F2E9; --paper-dim:#EDE6D6;
    --orange:#E8622C; --orange-dark:#C94E1E; --green:#2F6F4F; --green-soft:#E4EFE8;
    --red:#C23B22; --red-soft:#F7E4DF; --slate:#6B7280; --amber:#B8860B;
  }
  *{box-sizing:border-box;}

  html{
    height:100%;
    height:100dvh; 
    overflow:hidden;
  }
  body{
    margin:0; padding:0;
    height:100%;
    height:100dvh;
    overflow:hidden; 
    background:#DCD3BE;
    font-family:'Inter',sans-serif;
    color:var(--ink);
    line-height:1.55;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px 12px;
  }

  /* Fullscreen di HP asli */
  @media (max-width: 640px) {
    body{padding:0; background:var(--paper); align-items:stretch;}
    .device{
      border-radius:0;
      box-shadow:none;
      width:100%;
      max-width:100%;
      height:100vh;   
      height:100dvh;
      max-height:none;
      padding-bottom:env(safe-area-inset-bottom, 0px);
    }
    .notch{display:none;}
  }

  .display{font-family:'Archivo Black',sans-serif;}
  .condensed{font-family:'Barlow Condensed',sans-serif; font-weight:700; letter-spacing:.02em;}
  .mono{font-family:'JetBrains Mono',monospace;}

  .device{
    width:420px; max-width:100%; background:var(--paper);
    border-radius:34px;
    box-shadow:0 30px 60px -20px rgba(27,42,65,.45), 0 0 0 10px #14202f;
    overflow:hidden; position:relative;
    height:100vh;
    height:100dvh;
    max-height:860px;
    display:flex;
    flex-direction:column; 
  }
  .notch{position:absolute; top:0; left:50%; transform:translateX(-50%); width:150px; height:22px; background:#14202f; border-radius:0 0 16px 16px; z-index:50;}

  .topbar{background:var(--ink); color:var(--paper); padding:34px 20px 16px; position:relative; flex-shrink:0; z-index:10;}
  .topbar .stub{font-size:11px; letter-spacing:.16em; text-transform:uppercase; color:#9DAEC7;}
  .chip{display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:600; padding:4px 9px; border-radius:20px;}
  .chip-done{background:var(--green-soft); color:var(--green);}
  .chip-pending{background:#FBEFD9; color:var(--amber);}
  .chip-late{background:var(--red-soft); color:var(--red);}
  .chip-dark{background:rgba(255,255,255,.12); color:#E7EAF0;}

  .card{background:#fff; border:1px solid #E7DFCB; border-radius:14px;}
  .btn-primary{background:var(--orange); color:#fff; font-weight:700; border-radius:10px; padding:13px 16px; text-align:center; box-shadow:0 4px 0 var(--orange-dark); display:inline-block;}
  .btn-outline-green{border:1.5px solid var(--green); color:var(--green); font-weight:700; border-radius:10px; padding:10px 14px; display:inline-block;}

  .progress-track{background:#EAE2CB; border-radius:8px; height:10px; overflow:hidden;}
  .progress-fill{height:100%; border-radius:8px; background:var(--green);}

  /* Bottom Nav Styling */
  .navbtn{display:flex; flex-direction:column; align-items:center; gap:4px; font-size:10.5px; font-weight:600; color:#9DAEC7; background:none; border:none; cursor:pointer; text-decoration:none;}
  .navbtn.active{color:var(--paper);}
  .navbtn.active .navicon{background:var(--orange);}
  .navicon{width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.08);}

  .screen{
    flex:1 1 auto;
    min-height:0;      
    overflow-y:auto;
    -webkit-overflow-scrolling:touch;
    overscroll-behavior:contain; 
    padding-bottom:8px;
  }
  .screen::-webkit-scrollbar{width:0;}

  .stamp{border:2px solid var(--green); color:var(--green); font-family:'Barlow Condensed',sans-serif; font-weight:700; letter-spacing:.08em; text-transform:uppercase; font-size:11px; padding:3px 10px; border-radius:6px; transform:rotate(-4deg); display:inline-block;}
</style>
</head>
<body>

<div class="device">
  <div class="notch"></div>

  <!-- TOP BAR -->
  <div class="topbar">
    <div class="flex items-center justify-between">
      <div>
        <div class="stub">Ath-Thoifah · Sales Force</div>
        <div class="display text-lg mt-1" style="font-size:19px;">Halo, {{ auth()->user()->name }} 👋</div>
      </div>
      <div class="text-right">
        <div class="mono text-xs" id="clockEl" style="color:#C7D2E3;">--:--</div>
        @php $emp = auth()->user()->employee; @endphp
        <div class="chip chip-dark mt-2">
          {{ $emp && $emp->type === 'online' ? 'Mode Online' : 'Area: ' . ($emp->salesArea->name ?? 'N/A') }}
        </div>
      </div>
    </div>
  </div>

  <!-- SCREEN DYNAMIC CONTENT -->
  <div class="screen" style="background:var(--paper);">
    @yield('content')
  </div>

  <!-- BOTTOM NAV (Dynamic based on Salesman Type) -->
  <div class="flex items-center justify-around py-2.5" style="background:var(--ink); padding-bottom:18px; flex-shrink:0; z-index:10;">
    @php $isOnline = $emp && $emp->type === 'online'; @endphp

    <a href="{{ route('salesman.home') }}" class="navbtn {{ (request()->routeIs('salesman.home') || request()->routeIs('salesman.visits.index')) ? 'active' : '' }}">
      <span class="navicon">{{ $isOnline ? '📝' : '🏠' }}</span> {{ $isOnline ? 'Laporan' : 'Beranda' }}
    </a>

    @if(!$isOnline)
      <a href="{{ route('salesman.schedule.create') }}" class="navbtn {{ request()->routeIs('salesman.schedule.*') ? 'active' : '' }}">
        <span class="navicon">📅</span> Jadwal
      </a>
      <a href="{{ route('salesman.visits.index') }}" class="navbtn {{ request()->routeIs('salesman.visits.*') ? 'active' : '' }}">
        <span class="navicon">📍</span> Kunjungan
      </a>
    @endif

    <a href="{{ route('profile.edit') }}" class="navbtn {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
      <span class="navicon">👤</span> Akun
    </a>
  </div>
</div>

<!-- Simple Script for Clock (No Alpine needed) -->
<script>
  function updateClock(){
    const d = new Date();
    const time = d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
    const date = d.toLocaleDateString('id-ID',{day:'2-digit',month:'short'});
    document.getElementById('clockEl').textContent = time + ' · ' + date;
  }
  updateClock();
  setInterval(updateClock, 30000);
</script>
</body>
</html>