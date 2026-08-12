<?php

namespace App\Imports;

use App\Models\SalesArea;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AreaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new SalesArea([
            'code' => $row['kode_area'],
            'name' => $row['nama_area'],
            'description' => $row['deskripsi'] ?? null,
        ]);
    }
}