@extends('layouts.app')

@section('title', 'Edit Mitra')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Data Mitra</h2>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Kode Toko</label>
                    <input type="text" name="customer_code" value="{{ old('customer_code', $customer->customer_code) }}" class="w-full border rounded p-2 mt-1" required>
                    @error('customer_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Nama Toko</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full border rounded p-2 mt-1" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Nama Pemilik</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name', $customer->owner_name) }}" class="w-full border rounded p-2 mt-1">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">No Telepon</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $customer->phone_number) }}" class="w-full border rounded p-2 mt-1">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-700">Alamat</label>
                <textarea name="address" rows="2" class="w-full border rounded p-2 mt-1">{{ old('address', $customer->address) }}</textarea>
            </div>

            <!-- INPUT URL GOOGLE MAPS -->
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Tempel URL Google Maps (Opsional)</label>
                <input type="text" id="maps_url" class="w-full border rounded p-2 mt-1" placeholder="https://www.google.com/maps/...">
                <p class="text-xs text-gray-500 mt-1">Koordinat akan terisi otomatis di bawah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Latitude (GPS)</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $customer->latitude) }}" class="w-full border rounded p-2 mt-1" readonly>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Longitude (GPS)</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $customer->longitude) }}" class="w-full border rounded p-2 mt-1" readonly>
                </div>
            </div>

            <!-- TOMBOL LINK GOOGLE MAPS -->
            <div class="mb-4">
                @if($customer->latitude && $customer->longitude)
                    <a href="https://www.google.com/maps?q={{ $customer->latitude }},{{ $customer->longitude }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lihat Lokasi di Google Maps
                    </a>
                @else
                    <p class="text-sm text-gray-400">Belum ada koordinat GPS tersimpan.</p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Limit Kredit (Rp)</label>
                    <input type="number" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" class="w-full border rounded p-2 mt-1" min="0" step="any" placeholder="Contoh: 5000000" required>
                    @error('credit_limit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Term Pembayaran (Hari)</label>
                    <input type="number" name="credit_terms_days" value="{{ old('credit_terms_days', $customer->credit_terms_days) }}" class="w-full border rounded p-2 mt-1" min="0" step="1" placeholder="Contoh: 30" required>
                    @error('credit_terms_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Status Toko</label>
                    <select name="status" class="w-full border rounded p-2 mt-1">
                        <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- TAMBAHAN: INPUT DISKON & STATUS DISKON -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Diskon (%)</label>
                    <input type="number" name="discount" value="{{ old('discount', $customer->discount ?? 0) }}" class="w-full border rounded p-2 mt-1" min="0" max="100" step="0.01" placeholder="Contoh: 5">
                    @error('discount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Status Diskon</label>
                    <select name="discount_status" class="w-full border rounded p-2 mt-1">
                        <option value="inactive" {{ old('discount_status', $customer->discount_status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="submitted" {{ old('discount_status', $customer->discount_status) == 'submitted' ? 'selected' : '' }}>Diajukan</option>
                        <option value="active" {{ old('discount_status', $customer->discount_status) == 'active' ? 'selected' : '' }}>Aktif (Disetujui)</option>
                    </select>
                    @error('discount_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end border-t pt-4 mt-4">
                <a href="{{ route('admin.customers.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('maps_url').addEventListener('input', function() {
        const url = this.value;
        const regex = /(?:@|q=)(-?\d+\.\d+),(-?\d+\.\d+)/;
        const match = url.match(regex);
        
        if (match) {
            document.getElementById('latitude').value = match[1];
            document.getElementById('longitude').value = match[2];
        }
    });
</script>
@endsection