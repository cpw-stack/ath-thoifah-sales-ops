@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    <a href="{{ route('salesman.home') }}" class="text-xs font-semibold flex items-center gap-1" style="color:var(--slate);">← Kembali ke Beranda</a>

    <div class="condensed text-sm uppercase tracking-wide" style="color:var(--ink-soft);">Pengaturan Akun</div>

    <!-- Profile Header -->
    <div class="card p-4 flex flex-col items-center text-center">
        @if($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}" class="w-20 h-20 rounded-full object-cover mb-3">
        @else
            <div class="w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold mb-3" style="background:var(--paper-dim);">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        @endif
        <div class="display text-lg">{{ $user->name }}</div>
        <div class="text-xs" style="color:var(--slate);">{{ $user->email }}</div>
    </div>

    <!-- Flash Messages -->
    @if (session('status') === 'profile-updated')
        <div class="card p-3 text-sm" style="background:var(--green-soft); color:var(--green);">✅ Profil berhasil diperbarui.</div>
    @elseif (session('status') === 'password-updated')
        <div class="card p-3 text-sm" style="background:var(--green-soft); color:var(--green);">✅ Password berhasil diperbarui.</div>
    @endif

    <!-- Form Info Dasar -->
    <div class="card p-4 space-y-4">
        <div class="condensed text-sm uppercase tracking-wide" style="color:var(--ink-soft);">Informasi Dasar</div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">No. WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Alamat Domisili</label>
                    <textarea name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Foto Profil</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full mt-4">Simpan Perubahan</button>
        </form>
    </div>

    <!-- Form Password -->
    <div class="card p-4 space-y-4">
        <div class="condensed text-sm uppercase tracking-wide" style="color:var(--ink-soft);">Keamanan</div>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Password Saat Ini</label>
                    <input type="password" name="current_password" required>
                    @error('current_password') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Password Baru</label>
                    <input type="password" name="password" required>
                    @error('password') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full mt-4" style="background:var(--ink); box-shadow:0 4px 0 #111827;">Update Password</button>
        </form>
    </div>

    <!-- Tombol Logout -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-primary w-full" style="background:var(--red); box-shadow:0 4px 0 #8A3A2C;">🚪 Logout</button>
    </form>
</div>
@endsection