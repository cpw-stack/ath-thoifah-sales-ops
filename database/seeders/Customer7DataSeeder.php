<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Receivable;
use App\Models\Collection;
use App\Models\MitraStock;
use App\Models\MitraStockTransaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Customer7DataSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::find(7);

        if (!$customer) {
            $this->command->error("Customer dengan ID 7 tidak ditemukan!");
            return;
        }

        $products = Product::where('status', 'active')->get();
        $salesman = Employee::whereHas('user', function($q) {
            $q->role('salesman');
        })->first();

        if ($products->isEmpty() || !$salesman) {
            $this->command->error("Pastikan sudah ada data Product dan Salesman!");
            return;
        }

        // Ambil 4 produk acak untuk dummy
        $p1 = $products->get(0);
        $p2 = $products->get(1);
        $p3 = $products->get(2);
        $p4 = $products->count() > 3 ? $products->get(3) : $products->get(0);

        // 1. BUAT DATA STOK MITRA (Konsinyasi, Lunas, Piutang)
        MitraStock::updateOrCreate(
            ['customer_id' => $customer->id, 'product_id' => $p1->id],
            ['qty_konsinyasi' => 10, 'qty_lunas' => 5, 'qty_piutang' => 2]
        );
        MitraStock::updateOrCreate(
            ['customer_id' => $customer->id, 'product_id' => $p2->id],
            ['qty_konsinyasi' => 0, 'qty_lunas' => 15, 'qty_piutang' => 0]
        );
        MitraStock::updateOrCreate(
            ['customer_id' => $customer->id, 'product_id' => $p3->id],
            ['qty_konsinyasi' => 8, 'qty_lunas' => 0, 'qty_piutang' => 5]
        );

        // 2. BUAT RIWAYAT ORDER (Cash, Konsinyasi, Piutang)
        // Order 1: CASH (Lunas)
        $order1 = Order::create([
            'order_code' => 'ORD-C7-001',
            'visit_id' => null,
            'customer_id' => $customer->id,
            'employee_id' => $salesman->id,
            'total_amount' => $p2->price * 15,
            'payment_type' => 'cash',
            'status' => 'delivered',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10)
        ]);
        OrderItem::create([
            'order_id' => $order1->id, 'product_id' => $p2->id, 'qty' => 15, 'price' => $p2->price, 'subtotal' => $p2->price * 15
        ]);

        // Order 2: KONSINYASI
        $order2 = Order::create([
            'order_code' => 'ORD-C7-002',
            'visit_id' => null,
            'customer_id' => $customer->id,
            'employee_id' => $salesman->id,
            'total_amount' => $p1->price * 10,
            'payment_type' => 'konsinyasi',
            'status' => 'delivered',
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5)
        ]);
        OrderItem::create([
            'order_id' => $order2->id, 'product_id' => $p1->id, 'qty' => 10, 'price' => $p1->price, 'subtotal' => $p1->price * 10
        ]);

        // Order 3: PIUTANG
        $order3 = Order::create([
            'order_code' => 'ORD-C7-003',
            'visit_id' => null,
            'customer_id' => $customer->id,
            'employee_id' => $salesman->id,
            'total_amount' => $p3->price * 5,
            'payment_type' => 'piutang',
            'status' => 'delivered',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2)
        ]);
        OrderItem::create([
            'order_id' => $order3->id, 'product_id' => $p3->id, 'qty' => 5, 'price' => $p3->price, 'subtotal' => $p3->price * 5
        ]);

        // 3. BUAT DATA PIUTANG & RIWAYAT PEMBAYARAN (Collection)
        // Piutang 1: Sebagian lunas (Partial) - Terkait Order 3
        $receivable1 = Receivable::create([
            'customer_id' => $customer->id,
            'reference_code' => 'INV-C7-001',
            'total_amount' => $p3->price * 5,
            'paid_amount' => $p3->price * 2, // Sudah bayar 2 pcs
            'due_date' => Carbon::now()->addDays(10),
            'status' => 'partial',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(1)
        ]);

        // Riwayat pembayaran untuk Piutang 1
        Collection::create([
            'receivable_id' => $receivable1->id,
            'visit_id' => null,
            'employee_id' => $salesman->id,
            'amount' => $p3->price * 2,
            'payment_date' => Carbon::now()->subDay(),
            'payment_method' => 'cash',
            'status' => 'verified',
            'notes' => 'Angsuran 1',
            'created_at' => Carbon::now()->subDay(),
            'updated_at' => Carbon::now()->subDay()
        ]);

        // Piutang 2: Overdue (Belum bayar sama sekali)
        $receivable2 = Receivable::create([
            'customer_id' => $customer->id,
            'reference_code' => 'INV-C7-002',
            'total_amount' => $p1->price * 2,
            'paid_amount' => 0,
            'due_date' => Carbon::now()->subDays(5), // Jatuh tempo 5 hari lalu
            'status' => 'overdue',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10)
        ]);

        // Riwayat pembayaran untuk Piutang 2 (Pending - menunggu verifikasi admin)
        Collection::create([
            'receivable_id' => $receivable2->id,
            'visit_id' => null,
            'employee_id' => $salesman->id,
            'amount' => $p1->price * 1, // Bayar 1 pcs dulu
            'payment_date' => Carbon::today(),
            'payment_method' => 'transfer',
            'status' => 'pending',
            'notes' => 'Bukti transfer diupload',
            'created_at' => Carbon::today(),
            'updated_at' => Carbon::today()
        ]);

        $this->command->info("Data dummy lengkap untuk Customer ID 7 berhasil dibuat!");
    }
}