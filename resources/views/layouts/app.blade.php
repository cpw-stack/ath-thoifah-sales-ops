<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Ath-Thoifah') }} — Admin Sales Ops</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    
    <style>
        :root{
            --ink:#1B2A41; --ink-soft:#3A4A63; --paper:#F6F2E9; --paper-dim:#EDE6D6;
            --orange:#E8622C; --orange-dark:#C94E1E; --green:#2F6F4F; --green-soft:#E4EFE8;
            --red:#C23B22; --red-soft:#F7E4DF; --slate:#6B7280; --amber:#B8860B; --amber-soft:#FBEFD9;
            --border:#E4DCC6;
        }
        *{box-sizing:border-box;}
        body{margin:0; font-family:'Inter',sans-serif; background:var(--paper); color:var(--ink); line-height:1.55;}
        .display{font-family:'Archivo Black',sans-serif;}
        .condensed{font-family:'Barlow Condensed',sans-serif; font-weight:700; letter-spacing:.02em;}
        .mono{font-family:'JetBrains Mono',monospace;}
        ::-webkit-scrollbar{width:8px; height:8px;}
        ::-webkit-scrollbar-thumb{background:#D5CAAA; border-radius:8px;}

        .layout{display:flex; min-height:100vh;}
        
        /* PERBAIKAN SIDEBAR: Gunakan 100dvh & tambahkan overflow-y auto */
        .sidebar{
            width:236px; background:var(--ink); color:#E7EAF0; flex-shrink:0;
            display:flex; flex-direction:column; position:fixed; top:0; 
            height:100vh; /* Fallback untuk browser lama */
            height:100dvh; /* Dynamic viewport height untuk mobile */
            z-index:50;
            transition: transform 0.3s ease-in-out;
            overflow-y: auto; /* Agar sidebar bisa di-scroll jika menu panjang */
            -webkit-overflow-scrolling: touch; /* Smooth scroll di iOS */
        }
        .sidebar::-webkit-scrollbar { width: 0; } /* Sembunyikan scrollbar sidebar */

        .brand{padding:22px 20px 16px; border-bottom:1px solid rgba(255,255,255,.08);}
        .brand .stub{font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#8FA0BC;}
        .navsec{font-size:10px; letter-spacing:.14em; text-transform:uppercase; color:#5E7192; padding:22px 20px 8px;}
        .navitem{
            display:flex; align-items:center; gap:11px; padding:12px 20px; font-size:13.5px; font-weight:500;
            color:#B9C4D6; cursor:pointer; border-left:3px solid transparent; text-decoration:none;
        }
        .navitem:hover{background:rgba(255,255,255,.04);}
        .navitem.active{background:rgba(232,98,44,.12); color:#fff; border-left-color:var(--orange); font-weight:600;}
        .navicon{width:18px; text-align:center; opacity:.9;}

        /* PERBAIKAN RESPONSIVE: Margin-left hanya untuk Desktop */
        .main{flex:1; min-width:0; transition: margin 0.3s ease;}
        @media (min-width: 768px) {
            .main{ margin-left: 236px; }
        }

        .topbar{
            background:#fff; border-bottom:1px solid var(--border); padding:20px 32px;
            display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:20;
        }
        .pill{background:var(--paper-dim); border-radius:20px; padding:7px 15px; font-size:12.5px; color:var(--slate); display:flex; align-items:center; gap:6px;}
        .content{padding:32px 32px 70px;}
        .content > * + *{margin-top:26px;}

        .card{background:#fff; border:1px solid var(--border); border-radius:14px;}
        .kpi-card{padding:22px 22px 20px;}
        .kpi-label{font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--slate); font-weight:600;}
        .kpi-value{font-family:'Archivo Black',sans-serif; font-size:27px; margin-top:10px;}
        .kpi-delta{font-size:11.5px; font-weight:600; margin-top:10px; display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px;}
        .up{color:var(--green); background:var(--green-soft);}
        .down{color:var(--red); background:var(--red-soft);}

        table{width:100%; border-collapse:collapse; font-size:13px;}
        th{text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:.06em; color:var(--slate); font-weight:700; padding:13px 18px; border-bottom:1.5px solid var(--border); background:var(--paper-dim);}
        td{padding:15px 18px; border-bottom:1px solid var(--border); vertical-align:middle;}
        tr:hover td{background:#FBF9F3;}

        .badge{font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px; display:inline-block;}
        .badge-green{background:var(--green-soft); color:var(--green);}
        .badge-red{background:var(--red-soft); color:var(--red);}
        .badge-amber{background:var(--amber-soft); color:var(--amber);}
        .badge-slate{background:var(--paper-dim); color:var(--slate);}

        .avatar{width:30px; height:30px; border-radius:50%; background:var(--paper-dim); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:var(--ink-soft); flex-shrink:0;}

        .sectiontitle{font-family:'Barlow Condensed',sans-serif; font-weight:700; font-size:20px; letter-spacing:.01em; margin-bottom:4px;}
        .card > .p-4{padding:22px;}
        .card > .p-5{padding:26px;}
        .card > div > .p-4{padding:20px;}

        .btn{background:var(--orange); color:#fff; font-weight:600; font-size:12.5px; padding:8px 14px; border-radius:8px; border:none; cursor:pointer; text-decoration:none; display:inline-block;}
        .btn-outline{background:#fff; border:1.5px solid var(--border); color:var(--ink-soft); font-weight:600; font-size:12.5px; padding:7px 13px; border-radius:8px; cursor:pointer; text-decoration:none; display:inline-block;}

        .barchart{display:flex; align-items:flex-end; gap:12px; height:180px; padding-top:20px; padding-bottom:25px;}
        .bar{flex:1; background:linear-gradient(180deg,var(--orange),var(--orange-dark)); border-radius:6px 6px 3px 3px; position:relative; min-height:4px;}
        .bar-label{position:absolute; bottom:-22px; left:0; right:0; text-align:center; font-size:11px; color:var(--slate);}
        .bar-value{position:absolute; top:-22px; left:0; right:0; text-align:center; font-size:11px; font-weight:700; color:var(--ink-soft);}

        /* Global Input Styling */
        input:not([type=submit]):not([type=checkbox]):not([type=radio]), select, textarea {
            border:1px solid var(--border); 
            border-radius:8px; 
            padding:9px 12px; 
            font-size:13px; 
            font-family:'Inter',sans-serif; 
            background:#fff;
            width: 100%;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(232, 98, 44, 0.1);
        }
        input[type=file] {
            padding: 6px 0px;
            cursor: pointer;
        }
        label {
            display: block;
            margin-bottom: 4px;
        }

        /* Mobile Overlay */
        .overlay{position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:40; display:none;}
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="layout">

    <!-- Overlay for Mobile -->
    <div class="overlay" :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"></div>

    <!-- SIDEBAR -->
    <div class="sidebar" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="brand">
            <div class="stub">Ath-Thoifah BOP</div>
            <div class="display" style="font-size:17px; margin-top:4px;">Sales Ops</div>
        </div>

        <div class="navsec">Ringkasan</div>
        <a href="{{ route('dashboard') }}" class="navitem {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="navicon">📊</span> Overview</a>

        <div class="navsec">Master Data</div>
        <a href="{{ route('admin.employees.index') }}" class="navitem {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"><span class="navicon">🧑</span> Salesman</a>
        <a href="{{ route('admin.customers.index') }}" class="navitem {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"><span class="navicon">🏪</span> Mitra / Outlet</a>
        <a href="{{ route('admin.products.index') }}" class="navitem {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><span class="navicon">📦</span> Produk</a>
        <a href="{{ route('admin.areas.index') }}" class="navitem {{ request()->routeIs('admin.areas.*') ? 'active' : '' }}"><span class="navicon">🗺️</span> Area</a>

        <div class="navsec">Aktivitas Lapangan</div>
        <a href="{{ route('admin.visit-plans.index') }}" class="navitem {{ request()->routeIs('admin.visit-plans.*') ? 'active' : '' }}"><span class="navicon">🗺️</span> Visit Planning</a>
        <a href="{{ route('salesman.visits.index') }}" class="navitem {{ request()->routeIs('salesman.visits.*') ? 'active' : '' }}"><span class="navicon">📍</span> Monitoring Kunjungan</a>
        <a href="{{ route('admin.orders.index') }}" class="navitem {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><span class="navicon">🧾</span> Order</a>
        <a href="{{ route('admin.collections.index') }}" class="navitem {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}"><span class="navicon">💵</span> Collection</a>
        <a href="{{ route('admin.tasks.index') }}" class="navitem {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}"><span class="navicon">✅</span> Task</a>

        <div class="navsec">Kinerja</div>
        <a href="{{ route('admin.targets.index') }}" class="navitem {{ request()->routeIs('admin.targets.*') ? 'active' : '' }}"><span class="navicon">🎯</span> Target</a>
        <a href="{{ route('admin.reports.index') }}" class="navitem {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><span class="navicon">📄</span> Laporan</a>

        <div style="margin-top:auto; padding:16px 20px; border-top:1px solid rgba(255,255,255,.08); font-size:11.5px; color:#7C8CA8;">
            Phase 1 · Sales Force Mgmt<br>v0.1
        </div>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="topbar">
            <!-- Hamburger Menu for Mobile -->
            <button class="md:hidden mr-4 text-2xl" @click="sidebarOpen = true">☰</button>
            
            <div class="condensed" style="font-size:18px;">@yield('title', 'Dashboard')</div>
            
            <!-- Profile Dropdown -->
            <div class="flex items-center gap-3 relative" x-data="{ profileOpen: false }">
                <div class="pill hidden sm:flex">📅 {{ now()->translatedFormat('l, d F Y') }}</div>
                
                <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 focus:outline-none">
                    <div class="avatar" style="background:var(--ink); color:#fff; width:36px; height:36px;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <span class="condensed hidden md:block" style="font-size:15px;">{{ auth()->user()->name }}</span>
                    <svg class="w-4 h-4 fill-current text-gray-600 hidden md:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 11.828 5.757 6.586 4.343 8z"/></svg>
                </button>

                <!-- Dropdown Content -->
                <div x-show="profileOpen" @click.outside="profileOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 top-14 w-56 bg-white border rounded-xl shadow-xl py-2 z-50" style="border-color:var(--border);">
                    <div class="px-4 py-2 border-b" style="border-color:var(--border);">
                        <div class="font-semibold text-sm">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50">
                        <span>👤</span> Edit Profile
                    </a>
                    <a href="{{ route('scoreboard') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50">
                        <span>🏆</span> Papan Skor Tim
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 text-red-600">
                            <span>🚪</span> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="content">
            @if (session('success'))
                <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="card p-4 mb-4" style="background:var(--red-soft); color:var(--red); border:1px solid var(--red);">
                    ⚠️ {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>

</body>
</html>