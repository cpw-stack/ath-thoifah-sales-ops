<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return ["Kode Toko", "Nama Toko", "Pemilik", "Telepon", "Alamat", "Latitude", "Longitude", "Limit Kredit", "Term Hari", "Status"];
    }
}