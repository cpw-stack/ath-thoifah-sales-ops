@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    <h2 class="display text-xl">Pengaturan Profil</h2>

    @if (session('status') === 'profile-updated')
        <div class="card p-3 text-sm" style="background:var(--green-soft); color:var(--green);">✅ Profil berhasil diperbarui.</div>
    @elseif (session('status') === 'password-updated')
        <div class="card p-3 text-sm" style="background:var(--green-soft); color:var(--green);">✅ Password berhasil diperbarui.</div>
    @endif

    <!-- HEADER PROFILE & STATISTIK SKORBOARD -->
    <div class="card p-6 flex flex-col items-center text-center gap-3">
        <div class="relative">
            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1B2A41&color=fff' }}" class="w-24 h-24 rounded-full object-cover border-4" style="border-color: var(--border);">
            
            <!-- Badge Peringkat Menempel di Foto -->
            @if($rank)
            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md border-2 border-white whitespace-nowrap" style="background: var(--orange);">
                🏆 Rank #{{ $rank }}
            </div>
            @endif
        </div>
        
        <div class="mt-2">
            <h3 class="display text-lg" style="color:var(--ink);">{{ $user->name }}</h3>
            <p class="text-xs" style="color:var(--slate);">{{ $user->email }}</p>
        </div>

        <!-- Grid Statistik -->
        <div class="w-full grid grid-cols-2 gap-3 mt-3 border-t pt-4" style="border-color:var(--border);">
            <div class="text-center">
                <div class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--slate);">Peringkat Bulan Ini</div>
                @if($rank)
                    <div class="text-lg font-bold mono mt-1" style="color:var(--green);">#{{ $rank }}</div>
                @else
                    <div class="text-xs font-bold mt-1" style="color:var(--slate);">Belum Berperingkat</div>
                @endif
            </div>
            <div class="text-center border-l" style="border-color:var(--border);">
                <div class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--slate);">Total Penjualan</div>
                <div class="text-lg font-bold mono mt-1" style="color:var(--ink);">Rp {{ number_format($currentSales / 1000000, 1) }}jt</div>
            </div>
        </div>

        <a href="{{ route('scoreboard.mobile') }}" class="btn-primary w-full text-center text-sm mt-2" style="padding:12px;">
            📺 Lihat Papan Skor Tim
        </a>
    </div>

    <!-- FORM INFO DASAR -->
    <div class="card p-5 space-y-4">
        <div class="font-bold text-sm uppercase tracking-wider" style="color:var(--ink);">Informasi Dasar</div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PATCH')
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">No. WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Jenis Kelamin</label>
                    <select name="gender" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('gender', $user->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender', $user->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Alamat Domisili</label>
                    <textarea name="address" rows="2" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);">{{ old('address', $user->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Foto Profil</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm border rounded-lg p-2" style="border-color:var(--border);">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full text-sm mt-4" style="padding:14px;">Simpan Perubahan</button>
        </form>
    </div>

    <!-- FORM PASSWORD -->
    <div class="card p-5 space-y-4">
        <div class="font-bold text-sm uppercase tracking-wider" style="color:var(--ink);">Keamanan & Password</div>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Password Saat Ini</label>
                    <input type="password" name="current_password" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Password Baru</label>
                    <input type="password" name="password" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="w-full p-3 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full text-sm mt-4" style="padding:14px;">Update Password</button>
        </form>
    </div>
    <!-- Tombol Logout -->
    <div class="card p-5 text-center mt-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-primary w-full text-sm" style="background:var(--red); box-shadow:0 4px 0 #a02f1b; padding:14px;">
                🚪 Logout dari Akun
            </button>
        </form>
    </div>
</div>
@endsection