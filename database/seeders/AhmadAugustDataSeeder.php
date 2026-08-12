<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Receivable;
use App\Models\Collection;
use App\Models\Task;

class AhmadAugustDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'ahmad.sales@ath-thoifah.com')->first();

        if (!$user) {
            $this->command->error("User dengan email ahmad.sales@ath-thoifah.com tidak ditemukan!");
            return;
        }

        $ahmad = $user->employee;

        if (!$ahmad) {
            $this->command->error("Data employee untuk user tersebut tidak ditemukan!");
            return;
        }

        // Ambil 3 toko acak yang aktif
        $customers = Customer::where('status', 'active')->get();
        
        if ($customers->count() < 3) {
            $this->command->error("Data customer aktif kurang dari 3. Tambahkan customer dulu!");
            return;
        }

        $cust1 = $customers->get(0);
        $cust2 = $customers->get(1);
        $cust3 = $customers->get(2);

        // 1. Buat Data Piutang (Tagihan) untuk Agustus 2026
        $rec1 = Receivable::firstOrCreate(
            ['reference_code' => 'INV-AHM-001'], 
            [
                'customer_id' => $cust1->id, 
                'total_amount' => 1500000, 
                'paid_amount' => 0, 
                'due_date' => '2026-08-10', 
                'status' => 'unpaid'
            ]
        );

        $rec2 = Receivable::firstOrCreate(
            ['reference_code' => 'INV-AHM-002'], 
            [
                'customer_id' => $cust2->id, 
                'total_amount' => 2500000, 
                'paid_amount' => 1000000, // Sudah dibayar sebagian
                'due_date' => '2026-08-20', 
                'status' => 'partial'
            ]
        );

        // 2. Buat Riwayat Pembayaran (Collection) untuk piutang di atas
        Collection::firstOrCreate(
            ['receivable_id' => $rec2->id, 'amount' => 1000000],
            [
                'employee_id' => $ahmad->id, 
                'payment_date' => '2026-08-05', 
                'payment_method' => 'cash', 
                'status' => 'verified', 
                'notes' => 'Angsuran pembayaran'
            ]
        );

        // 3. Buat Tugas (Tasks) khusus untuk Ahmad di Agustus 2026
        Task::firstOrCreate(
            ['title' => 'Tagih piutang INV-AHM-001', 'employee_id' => $ahmad->id],
            [
                'customer_id' => $cust1->id, 
                'priority' => 'high', 
                'due_date' => '2026-08-10', 
                'status' => 'pending', 
                'description' => 'Lakukan penagihan penuh atas invoice jatuh tempo.'
            ]
        );

        Task::firstOrCreate(
            ['title' => 'Pasang display promo Kurma', 'employee_id' => $ahmad->id],
            [
                'customer_id' => $cust3->id, 
                'priority' => 'medium', 
                'due_date' => '2026-08-15', 
                'status' => 'pending', 
                'description' => 'Pasang standing banner promo Kurma di etalase toko.'
            ]
        );

        Task::firstOrCreate(
            ['title' => 'Survey stok produk Madu', 'employee_id' => $ahmad->id],
            [
                'customer_id' => $cust2->id, 
                'priority' => 'low', 
                'due_date' => '2026-08-25', 
                'status' => 'pending', 
                'description' => 'Cek estimasi sisa stok madu variant baru.'
            ]
        );

        $this->command->info("Data dummy tagihan & tugas Agustus 2026 untuk Ahmad Fauzi berhasil dibuat!");
    }
}