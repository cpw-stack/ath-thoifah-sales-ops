<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $category = ProductCategory::firstOrCreate(['name' => $row['kategori']], ['code' => 'CAT-' . rand(100,999)]);

        return new Product([
            'sku' => $row['sku'],
            'name' => $row['nama_produk'],
            'product_category_id' => $category->id,
            'unit' => $row['satuan'],
            'price' => $row['harga'],
            'status' => strtolower($row['status']) === 'inactive' ? 'inactive' : 'active',
        ]);
    }
}