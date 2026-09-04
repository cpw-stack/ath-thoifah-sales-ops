@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="display text-2xl mb-6">Tambah Produk Baru</h2>
    
    <div class="card">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">SKU / Kode Produk</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" class="w-full" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Kategori</label>
                        <select name="product_category_id" class="w-full">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Satuan</label>
                        <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" class="w-full" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">HPP (Harga Modal)</label>
                        <input type="number" name="hpp" value="{{ old('hpp', 0) }}" class="w-full" required min="0">
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Harga Jual</label>
                        <input type="number" name="price" value="{{ old('price', 0) }}" class="w-full" required min="0">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Jumlah Stok</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full" required min="0">
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Status</label>
                        <select name="status" class="w-full">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4">
                    <a href="{{ route('admin.products.index') }}" class="btn-outline mr-2">Batal</a>
                    <button type="submit" class="btn">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection