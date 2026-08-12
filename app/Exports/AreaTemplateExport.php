<?php

namespace App\Exports;

use App\Models\SalesArea;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AreaTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return ["Kode Area", "Nama Area", "Deskripsi"];
    }
}