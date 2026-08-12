<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} — Sales Ops</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #000; }
        .display { font-family: 'Archivo Black', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-black text-white relative overflow-hidden">
    
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: radial-gradient(#ff0000 1px, transparent 1px); background-size: 24px 24px;"></div>
    
    <!-- Glow Effect -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] pointer-events-none z-0" style="background: radial-gradient(circle, rgba(200, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0) 70%);"></div>

    <!-- Form Card Container -->
    <div class="relative z-10 w-full sm:max-w-md px-6 py-10 bg-zinc-900/90 backdrop-blur-sm border border-red-800/30 rounded-2xl shadow-2xl shadow-red-900/20 mt-8 mb-8">
        
        <!-- Logo Ath-Thoifah (Bentuk Capsule) -->
        <div class="flex flex-col items-center mb-8">
            <div class="mb-6 transform transition-transform hover:scale-105 duration-300">
                <div class="inline-flex p-4 bg-zinc-900/80 rounded-full border-2 border-red-800 shadow-2xl shadow-red-900/50">
                    <img src="{{ asset('storage/dummy_proofs/logo-big-ath-thoifah.png') }}" alt="Ath-Thoifah Logo" class="h-24 md:h-28 w-auto object-contain">
                </div>
            </div>
            <div class="display text-2xl tracking-wider text-white">ATH-THOIFAH</div>
            <div class="text-xs text-red-600 font-bold uppercase tracking-[0.2em] mt-1">Sales Operations</div>
        </div>

        <!-- Dynamic Content -->
        @yield('content')
    </div>
</body>
</html>