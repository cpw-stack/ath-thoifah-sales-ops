<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    private function cleanNumber($value)
    {
        if (empty($value)) return 0;
        
        // Hapus "Rp", spasi, titik, koma, dan tanda minus
        $value = str_replace(['Rp', ' ', '.', ',', '-'], '', $value);
        
        // Jika setelah dibersihkan bukan angka, return 0
        return is_numeric($value) ? $value : 0;
    }

    public function model(array $row)
    {
        // 1. SKIP JIKA SKU KOSONG ATAU BERISI TANDA BINTANG (*)
        if (empty($row['sku']) || $row['sku'] === '*') {
            return null; // Mengembalikan null akan membuat Excel melewati baris ini
        }

        $categoryName = $row['kategori'] ?? 'Uncategorized';
        $category = ProductCategory::firstOrCreate(['name' => $categoryName], ['code' => 'CAT-' . rand(100,999)]);

        return new Product([
            'sku'     => $row['sku'],
            'name'    => $row['nama_produk'],
            'product_category_id' => $category->id,
            'unit'    => $row['satuan'] ?? 'pcs',
            'hpp'     => $this->cleanNumber($row['hpp'] ?? 0),
            'price'   => $this->cleanNumber($row['harga'] ?? 0),
            'stock'   => $this->cleanNumber($row['jumlah_stok'] ?? 0), 
            'status'  => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ]);
    }
}