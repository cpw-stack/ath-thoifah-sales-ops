<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Ambil semua data mitra
        return Customer::all();
    }

    public function map($customer): array
    {
        return [
            $customer->customer_code,
            $customer->name,
            $customer->owner_name,
            $customer->phone_number,
            $customer->address,
            $customer->latitude,
            $customer->longitude,
            $customer->credit_limit,
            $customer->credit_terms_days,
            $customer->status
        ];
    }

    public function headings(): array
    {
        return [
            "Kode Toko", 
            "Nama Toko", 
            "Pemilik", 
            "Telepon", 
            "Alamat", 
            "Latitude", 
            "Longitude", 
            "Limit Kredit", 
            "Term Hari", 
            "Status"
        ];
    }
}