<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Kosongkan collection, kita hanya butuh header untuk template
        return collect([]);
    }

    public function headings(): array
    {
        return ["SKU", "Nama Produk", "Kategori", "Satuan", "Harga", "Status"];
    }
}