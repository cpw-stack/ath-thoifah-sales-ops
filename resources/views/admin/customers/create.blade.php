@extends('layouts.app')

@section('title', 'Tambah Mitra')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Tambah Mitra Baru</h2>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Kode Toko</label>
                    <input type="text" name="customer_code" class="w-full border rounded p-2 mt-1" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Nama Toko</label>
                    <input type="text" name="name" class="w-full border rounded p-2 mt-1" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Nama Pemilik</label>
                    <input type="text" name="owner_name" class="w-full border rounded p-2 mt-1">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">No Telepon</label>
                    <input type="text" name="phone_number" class="w-full border rounded p-2 mt-1">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Alamat</label>
                <textarea name="address" rows="2" class="w-full border rounded p-2 mt-1"></textarea>
            </div>
            
            <!-- INPUT URL GOOGLE MAPS -->
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Tempel URL Google Maps (Opsional)</label>
                <input type="text" id="maps_url" class="w-full border rounded p-2 mt-1" placeholder="Tempel link penuh (bukan short link goo.gl)">
                <p class="text-xs text-gray-500 mt-1">*Hanya berfungsi jika link mengandung koordinat (contoh: ?q=-6.123,107.456). Atau gunakan tombol di bawah.</p>
            </div>

            <!-- TOMBOL DETEKSI GPS OTOMATIS -->
            <div class="mb-4">
                <button type="button" id="detect_gps" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                    📍 Deteksi Lokasi Saya (GPS)
                </button>
                <p class="text-xs text-gray-500 mt-1 hidden" id="gps_status">Mengambil lokasi...</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Latitude (GPS)</label>
                    <input type="text" name="latitude" id="latitude" class="w-full border rounded p-2 mt-1 bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Longitude (GPS)</label>
                    <input type="text" name="longitude" id="longitude" class="w-full border rounded p-2 mt-1 bg-gray-100">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Limit Kredit (Rp)</label>
                    <input type="number" name="credit_limit" value="0" class="w-full border rounded p-2 mt-1" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Term Pembayaran (Hari)</label>
                    <input type="number" name="credit_terms_days" value="0" class="w-full border rounded p-2 mt-1" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Status</label>
                    <select name="status" class="w-full border rounded p-2 mt-1">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('admin.customers.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Script untuk ekstrak Lat/Long dari URL Google Maps
    document.getElementById('maps_url').addEventListener('input', function() {
        const url = this.value;
        // Regex untuk menangkap format @lat,lng atau q=lat,lng
        const regex = /(?:@|q=)(-?\d+\.\d+),(-?\d+\.\d+)/;
        const match = url.match(regex);
        
        if (match) {
            document.getElementById('latitude').value = match[1];
            document.getElementById('longitude').value = match[2];
        }
    });

    // Script untuk deteksi lokasi GPS
    document.getElementById('detect_gps').addEventListener('click', function() {
        const statusEl = document.getElementById('gps_status');
        statusEl.classList.remove('hidden');

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    statusEl.textContent = 'Lokasi ditemukan!';
                    statusEl.classList.add('text-green-600');
                    statusEl.classList.remove('text-gray-500');
                },
                function(error) {
                    console.error("Error mendapatkan lokasi GPS:", error);
                    let errorMsg = 'Gagal mendapatkan lokasi.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = "Akses lokasi ditolak oleh pengguna.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = "Informasi lokasi tidak tersedia.";
                            break;
                        case error.TIMEOUT:
                            errorMsg = "Permintaan lokasi timeout.";
                            break;
                    }
                    statusEl.textContent = errorMsg;
                    statusEl.classList.add('text-red-600');
                    statusEl.classList.remove('text-gray-500');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000, // 10 detik
                    maximumAge: 60000 // 1 menit
                }
            );
        } else {
            statusEl.textContent = 'Geolocation tidak didukung browser ini.';
            statusEl.classList.add('text-red-600');
            statusEl.classList.remove('text-gray-500');
        }

        setTimeout(() => {
            statusEl.classList.add('hidden');
            statusEl.classList.remove('text-green-600', 'text-red-600');
            statusEl.classList.add('text-gray-500');
        }, 3000);
    });
</script>
@endsection