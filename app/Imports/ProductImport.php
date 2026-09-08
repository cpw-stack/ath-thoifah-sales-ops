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
        $value = str_replace(['Rp', ' ', '.', ','], '', $value);
        return is_numeric($value) ? $value : 0;
    }

    public function model(array $row)
    {
        // Skip jika baris kosong atau SKU kosong/bintang
        if (empty($row['sku']) || $row['sku'] === '*') {
            return null;
        }

        $categoryName = $row['kategori'] ?? 'Uncategorized';
        $category = ProductCategory::firstOrCreate(['name' => $categoryName], ['code' => 'CAT-' . rand(100, 999)]);

        // Cari produk berdasarkan SKU. Jika ada, Update. Jika tidak ada, Create.
        return Product::updateOrCreate(
            ['sku' => $row['sku']],
            [
                'name' => $row['nama_produk'],
                'product_category_id' => $category->id,
                'unit' => $row['satuan'] ?? 'pcs',
                'hpp' => $this->cleanNumber($row['hpp'] ?? 0),
                'price' => $this->cleanNumber($row['harga'] ?? 0),
                'stock' => $this->cleanNumber($row['jumlah_stok'] ?? 0),
                'status' => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            ]
        );
    }
}