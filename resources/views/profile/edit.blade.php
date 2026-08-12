@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="display text-2xl mb-6">Pengaturan Profil</h2>

    <!-- Notifikasi Sukses Breeze -->
    @if (session('status') === 'profile-updated')
        <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">
            ✅ Profil berhasil diperbarui.
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">
            ✅ Password berhasil diperbarui.
        </div>
    @endif

    <!-- Form Info Dasar -->
    <div class="card mb-6">
        <div class="p-5 border-b" style="border-color:var(--border);">
            <div class="sectiontitle">Informasi Dasar</div>
            <div class="text-xs" style="color:var(--slate);">Perbarui nama dan email akun Anda.</div>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="p-5" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full" required>
                    @error('name') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full" required>
                    @error('email') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">No. WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" class="w-full">
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Jenis Kelamin</label>
                    <select name="gender" class="w-full">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('gender', $user->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender', $user->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-xs mb-1" style="color:var(--slate);">Alamat Domisili</label>
                <textarea name="address" rows="2" class="w-full">{{ old('address', $user->address) }}</textarea>
            </div>
            <div class="mt-4 flex items-center gap-4">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Foto Profil</label>
                    <input type="file" name="photo" accept="image/*" class="text-sm">
                </div>
                @if($user->photo_path)
                    <img src="{{ asset('storage/' . $user->photo_path) }}" class="w-16 h-16 rounded-full object-cover">
                @endif
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="btn">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <!-- Form Password -->
    <div class="card">
        <div class="p-5 border-b" style="border-color:var(--border);">
            <div class="sectiontitle">Keamanan & Password</div>
            <div class="text-xs" style="color:var(--slate);">Pastikan akun Anda menggunakan password yang kuat.</div>
        </div>
        <form method="POST" action="{{ route('profile.password') }}" class="p-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-5 mb-4">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Password Saat Ini</label>
                    <input type="password" name="current_password" class="w-full" required>
                    @error('current_password') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Password Baru</label>
                        <input type="password" name="password" class="w-full" required>
                        @error('password') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full" required>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection