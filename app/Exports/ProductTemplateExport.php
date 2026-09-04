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
        // Kita return collection kosong, tapi kita akan mapping manual di bawah agar tidak bingung
        return collect([1]);
    }

    public function map($row): array
    {
        return [
            'A-1', 
            'Kapsul Daun Kelor 60', 
            'Kapsul', 
            'Botol', 
            50000, 
            60000, 
            100, // Tambahan Jumlah Stok
            'active'
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
            "Jumlah Stok", // Tambahan
            "Status"
        ];
    }
}