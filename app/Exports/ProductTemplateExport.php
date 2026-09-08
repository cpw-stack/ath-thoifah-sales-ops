<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Ambil semua data produk beserta relasi kategorinya
        return Product::with('category')->get();
    }

    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->category->name ?? 'Uncategorized',
            $product->unit,
            $product->hpp,
            $product->price,
            $product->stock,
            $product->status
        ];
    }

    public function headings(): array
    {
        return [
            "SKU", 
            "Nama Produk", 
            "Kategori", 
            "Satuan", 
            "HPP", 
            "Harga", 
            "Jumlah Stok", 
            "Status"
        ];
    }
}