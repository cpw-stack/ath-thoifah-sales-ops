<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ath-Thoifah — Business Operations Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .display { font-family: 'Archivo Black', sans-serif; }
        /* Efek glow merah di belakang */
        .glow-bg::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(200, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-black text-white relative overflow-hidden">
    
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: radial-gradient(#ff0000 1px, transparent 1px); background-size: 24px 24px;"></div>

    <div class="glow-bg relative min-h-screen flex flex-col items-center justify-center text-center px-4 z-10">
        
        <!-- Logo Ath-Thoifah (Bentuk Capsule) -->
        <div class="mb-8 transform transition-transform hover:scale-105 duration-300">
            <div class="inline-flex p-4 bg-zinc-900/80 rounded-full border-2 border-red-800 shadow-2xl shadow-red-900/50">
                <img src="{{ asset('storage/dummy_proofs/logo-big-ath-thoifah.png') }}" alt="Ath-Thoifah Logo" class="h-28 md:h-36 w-auto object-contain">
            </div>
        </div>

        <!-- Judul -->
        <h1 class="display text-4xl md:text-6xl mb-3 tracking-wider text-white">ATH-THOIFAH</h1>
        <p class="text-lg md:text-xl text-red-600 font-bold mb-8 uppercase tracking-[0.2em]">Business Operations Platform</p>
        
        <!-- Deskripsi -->
        <p class="max-w-2xl text-gray-400 mb-10 text-base md:text-lg leading-relaxed">
            Sistem Pengelolaan Aktivitas & Performa Sales Lapangan. Tingkatkan produktivitas, pantau kunjungan, dan kelola order dengan mudah.
        </p>
        
        <!-- Tombol Aksi -->
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
            <a href="/login" class="bg-red-700 hover:bg-red-800 text-white px-10 py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg shadow-red-700/30 hover:shadow-red-500/50 border border-red-500/50">
                Masuk Sistem
            </a>
            <a href="/scoreboard" class="bg-transparent hover:bg-white/5 text-white border-2 border-gray-700 px-10 py-4 rounded-lg font-bold text-lg transition-all duration-300">
                Lihat Papan Skor
            </a>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-6 left-0 right-0 text-center text-gray-600 text-xs">
            &copy; {{ date('Y') }} Ath-Thoifah. All Rights Reserved.
        </div>

    </div>
</body>
</html>