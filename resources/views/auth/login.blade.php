@extends('layouts.guest')

@section('content')
    <x-auth-session-status class="mb-4 p-3 bg-green-900/50 border border-green-500 text-green-300 rounded-lg text-sm" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-5">
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                   class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:border-transparent outline-none transition" />
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                   class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:border-transparent outline-none transition" />
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-between items-center mb-6">
            <label for="remember_me" class="inline-flex items-center text-sm text-zinc-400 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded bg-zinc-800 border-zinc-600 text-red-600 focus:ring-red-500 mr-2" name="remember">
                Ingat Saya
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-red-500 hover:text-red-400 font-semibold" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white py-3 rounded-lg font-bold transition-all duration-300 shadow-lg shadow-red-700/30 hover:shadow-red-500/50">
            Masuk Sistem
        </button>
        
        <div class="mt-6 text-center text-sm text-zinc-500">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-red-500 hover:text-red-400 font-semibold">Daftar di sini</a>
        </div>
    </form>
@endsection