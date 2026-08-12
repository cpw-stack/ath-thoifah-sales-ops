@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Tambah Produk Baru</h2>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">SKU / Kode Produk</label>
                    <input type="text" name="sku" class="w-full border rounded p-2 mt-1" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Nama Produk</label>
                    <input type="text" name="name" class="w-full border rounded p-2 mt-1" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-700">Kategori</label>
                    <select name="product_category_id" class="w-full border rounded p-2 mt-1">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Satuan</label>
                    <input type="text" name="unit" value="pcs" class="w-full border rounded p-2 mt-1" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Harga (Rp)</label>
                    <input type="number" name="price" value="0" class="w-full border rounded p-2 mt-1" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-700">Status</label>
                <select name="status" class="w-full border rounded p-2 mt-1">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection