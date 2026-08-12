<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Customer([
            'customer_code' => $row['kode_toko'],
            'name' => $row['nama_toko'],
            'owner_name' => $row['pemilik'] ?? null,
            'phone_number' => $row['telepon'] ?? null,
            'address' => $row['alamat'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'credit_limit' => $row['limit_kredit'] ?? 0,
            'credit_terms_days' => $row['term_hari'] ?? 0,
            'status' => strtolower($row['status']) === 'inactive' ? 'inactive' : 'active',
        ]);
    }
}