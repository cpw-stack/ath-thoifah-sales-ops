<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Product;
use App\Models\VisitPlan;
use App\Models\Visit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Receivable;
use App\Models\Collection;
use App\Models\Task;
use App\Models\Target;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $salesmanEmails = [
            'budi.sales@ath-thoifah.com',
            'citra.sales@ath-thoifah.com',
            'rizal.pratama@ath-thoifah.com',
            'siti.aminah@ath-thoifah.com',
            'ahmad.fauzi@ath-thoifah.com',
            'dedi.kurniawan@ath-thoifah.com',
            'rina.marlina@ath-thoifah.com'
        ];

        $salesmen = Employee::whereHas('user', function($q) use ($salesmanEmails) {
            $q->whereIn('email', $salesmanEmails);
        })->get();

        if ($salesmen->isEmpty()) {
            $this->command->error("Salesman tidak ditemukan! Jalankan DummyDataSeeder terlebih dahulu.");
            return;
        }

        $customers = Customer::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->error("Customer atau Produk kosong! Jalankan DummyDataSeeder terlebih dahulu.");
            return;
        }

        $period = '2026-08';
        
        // 1. Pastikan semua salesman punya target di Agustus 2026
        foreach ($salesmen as $emp) {
            Target::firstOrCreate(
                ['employee_id' => $emp->id, 'period_month' => $period],
                ['visit_target' => 90, 'order_target' => 50, 'sales_target' => 50000000, 'collection_target' => 25000000]
            );
        }

        // 2. Generate Data Aktivitas (Visit, Order, Collection) dari 20 hingga 25 Agustus 2026
        $startDate = Carbon::create(2026, 8, 20);
        $endDate = Carbon::create(2026, 8, 25);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            foreach ($salesmen as $emp) {
                // 80% kemungkinan salesman visit di hari itu
                if (rand(1, 10) > 2) {
                    $customer = $customers->random();
                    
                    // Buat Visit Plan (Completed)
                    $plan = VisitPlan::firstOrCreate(
                        ['employee_id' => $emp->id, 'customer_id' => $customer->id, 'visit_date' => $date->format('Y-m-d')],
                        ['status' => 'completed', 'created_at' => $date, 'updated_at' => $date]
                    );

                    // Buat Kunjungan (Check-in & Check-out)
                    $checkInAt = $date->copy()->setTime(rand(8, 15), rand(0, 59));
                    $visit = Visit::firstOrCreate(
                        ['visit_plan_id' => $plan->id],
                        [
                            'employee_id' => $emp->id,
                            'customer_id' => $customer->id,
                            'check_in_at' => $checkInAt,
                            'check_in_lat' => $customer->latitude ?? '-6.200000',
                            'check_in_lng' => $customer->longitude ?? '106.816666',
                            'distance_meters' => rand(10, 150),
                            'check_in_status' => 'valid',
                            'check_in_photo' => 'dummy_photos/visit.jpg',
                            'check_out_at' => $checkInAt->copy()->addMinutes(rand(15, 60)),
                            'created_at' => $checkInAt,
                            'updated_at' => $checkInAt
                        ]
                    );

                    // 60% kemungkinan salesman membuat Order saat visit
                    if (rand(1, 10) > 4) {
                        $orderItems = [];
                        $totalAmount = 0;
                        $numItems = rand(1, 4);
                        $selectedProducts = $products->random($numItems);

                        foreach ($selectedProducts as $p) {
                            $qty = rand(1, 10);
                            $subtotal = $p->price * $qty;
                            $totalAmount += $subtotal;
                            $orderItems[] = new OrderItem([
                                'product_id' => $p->id,
                                'qty' => $qty,
                                'price' => $p->price,
                                'subtotal' => $subtotal
                            ]);
                        }

                        $orderCode = 'ORD-' . $date->format('ymd') . '-' . strtoupper(Str::random(4));
                        $order = Order::create([
                            'order_code' => $orderCode,
                            'visit_id' => $visit->id,
                            'customer_id' => $customer->id,
                            'employee_id' => $emp->id,
                            'total_amount' => $totalAmount,
                            'status' => 'delivered',
                            'created_at' => $checkInAt->copy()->addMinutes(10),
                            'updated_at' => $checkInAt->copy()->addMinutes(10)
                        ]);
                        $order->items()->saveMany($orderItems);

                        // 40% kemungkinan ada penagihan (Collection) saat order
                        if (rand(1, 10) > 6) {
                            $receivable = Receivable::firstOrCreate(
                                ['customer_id' => $customer->id, 'reference_code' => 'INV-DEMO-' . $customer->id],
                                ['total_amount' => $totalAmount, 'paid_amount' => 0, 'due_date' => $date->copy()->addDays(30), 'status' => 'unpaid']
                            );

                            $amount = rand(100000, (int) min(1500000, $totalAmount));
                            Collection::create([
                                'receivable_id' => $receivable->id,
                                'visit_id' => $visit->id,
                                'employee_id' => $emp->id,
                                'amount' => $amount,
                                'payment_date' => $date,
                                'payment_method' => ['cash', 'transfer', 'qris'][array_rand(['cash', 'transfer', 'qris'])],
                                'status' => 'verified',
                                'created_at' => $checkInAt->copy()->addMinutes(20),
                                'updated_at' => $checkInAt->copy()->addMinutes(20)
                            ]);

                            $receivable->paid_amount += $amount;
                            $receivable->status = $receivable->paid_amount >= $receivable->total_amount ? 'paid' : 'partial';
                            $receivable->save();
                        }
                    }
                }
            }
        }

        // 3. Generate Visit Planning (Jadwal Kunjungan) untuk 26 - 31 Agustus 2026 (Status: Planned)
        $planStartDate = Carbon::create(2026, 8, 26);
        $planEndDate = Carbon::create(2026, 8, 31);

        for ($date = $planStartDate; $date->lte($planEndDate); $date->addDay()) {
            foreach ($salesmen as $emp) {
                // Beri 2-3 jadwal visit per salesman per hari
                $numPlans = rand(2, 3);
                $visitedCustomers = [];
                
                for ($i = 0; $i < $numPlans; $i++) {
                    $customer = $customers->random();
                    
                    // Pastikan tidak ada jadwal ganda untuk toko yang sama di tanggal yang sama
                    if (!in_array($customer->id, $visitedCustomers)) {
                        $visitedCustomers[] = $customer->id;
                        
                        VisitPlan::firstOrCreate(
                            [
                                'employee_id' => $emp->id, 
                                'customer_id' => $customer->id, 
                                'visit_date' => $date->format('Y-m-d')
                            ],
                            [
                                'status' => 'planned',
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]
                        );
                    }
                }
            }
        }

        // 4. Buat Tugas (Tasks) dengan deadline 26 - 31 Agustus 2026
        $taskTitles = [
            'Tagih piutang jatuh tempo', 'Pasang banner promo bulanan', 'Cek stok produk madu',
            'Survey harga kompetitor', 'Ambil order Kurma Ajwa', 'Retur barang rusak',
            'Konfirmasi pesanan event lebaran', 'Jemput pembayaran tunai'
        ];

        foreach ($salesmen as $emp) {
            // Beri 2 tugas untuk masing-masing salesman
            for ($i = 0; $i < 2; $i++) {
                $customer = $customers->random();
                $dueDate = Carbon::create(2026, 8, rand(26, 31));
                
                Task::firstOrCreate(
                    [
                        'title' => $taskTitles[array_rand($taskTitles)], 
                        'employee_id' => $emp->id, 
                        'customer_id' => $customer->id, 
                        'due_date' => $dueDate->format('Y-m-d')
                    ],
                    [
                        'priority' => ['high', 'medium', 'low'][array_rand(['high', 'medium', 'low'])],
                        'status' => 'pending',
                        'description' => 'Tugas demo untuk salesman ' . $emp->full_name
                    ]
                );
            }
        }

        $this->command->info("Data demo transaksi (20-25 Agustus), Visit Planning (26-31 Agustus), & Tugas berhasil dibuat!");
    }
}