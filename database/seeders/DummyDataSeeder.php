<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SalesArea;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Area Penjualan
        $area1 = SalesArea::firstOrCreate(['code' => 'BDG-TMR'], ['name' => 'Bandung Timur', 'description' => 'Coblong, Cibeunying']);
        $area2 = SalesArea::firstOrCreate(['code' => 'BDG-BRT'], ['name' => 'Bandung Barat', 'description' => 'Cimahi, Padalarang']);

        // 2. Buat Data User (1 Supervisor, 2 Salesman)
        $spvUser = User::firstOrCreate(
            ['email' => 'supervisor@ath-thoifah.com'],
            ['name' => 'Pak Supervisor', 'password' => Hash::make('password')]
        );
        $spvUser->assignRole('supervisor');

        $slmUser1 = User::firstOrCreate(
            ['email' => 'budi.sales@ath-thoifah.com'],
            ['name' => 'Budi Salesman', 'password' => Hash::make('password')]
        );
        $slmUser1->assignRole('salesman');

        $slmUser2 = User::firstOrCreate(
            ['email' => 'citra.sales@ath-thoifah.com'],
            ['name' => 'Citra Salesman', 'password' => Hash::make('password')]
        );
        $slmUser2->assignRole('salesman');

        // 3. Buat Data Employee (Hubungkan User ke Employee)
        $spv = Employee::firstOrCreate(
            ['user_id' => $spvUser->id],
            [
                'employee_code' => 'SPV-001',
                'full_name' => 'Pak Supervisor',
                'phone_number' => '081111111111',
                'sales_area_id' => $area1->id,
                'supervisor_id' => null,
                'status' => 'active'
            ]
        );

        Employee::firstOrCreate(
            ['user_id' => $slmUser1->id],
            [
                'employee_code' => 'SLM-001',
                'full_name' => 'Budi Salesman',
                'phone_number' => '082222222222',
                'sales_area_id' => $area1->id,
                'supervisor_id' => $spv->id,
                'status' => 'active'
            ]
        );

        Employee::firstOrCreate(
            ['user_id' => $slmUser2->id],
            [
                'employee_code' => 'SLM-002',
                'full_name' => 'Citra Salesman',
                'phone_number' => '083333333333',
                'sales_area_id' => $area2->id,
                'supervisor_id' => $spv->id,
                'status' => 'active'
            ]
        );

        // 4. Buat Data Mitra / Toko / Outlet
        $customers = [
            [
                'customer_code' => 'TOK-001',
                'name' => 'Toko Makmur Jaya',
                'owner_name' => 'Pak Makmur',
                'phone_number' => '081234500001',
                'address' => 'Jl. Merdeka No. 10, Bandung',
                'latitude' => '-6.9175',
                'longitude' => '107.6191',
                'credit_limit' => 5000000,
                'credit_terms_days' => 30,
                'status' => 'active'
            ],
            [
                'customer_code' => 'TOK-002',
                'name' => 'Minimarket Sentosa',
                'owner_name' => 'Bu Sentosa',
                'phone_number' => '081234500002',
                'address' => 'Jl. Asia Afrika No. 45, Bandung',
                'latitude' => '-6.9212',
                'longitude' => '107.6021',
                'credit_limit' => 3000000,
                'credit_terms_days' => 15,
                'status' => 'active'
            ],
            [
                'customer_code' => 'TOK-003',
                'name' => 'Apotek Klinik Sehat',
                'owner_name' => 'Dr. Andi',
                'phone_number' => '081234500003',
                'address' => 'Jl. Dipatiukur No. 5, Bandung',
                'latitude' => '-6.8920',
                'longitude' => '107.6105',
                'credit_limit' => 10000000,
                'credit_terms_days' => 45,
                'status' => 'active'
            ],
            [
                'customer_code' => 'TOK-004',
                'name' => 'Toko Herbal Berkah',
                'owner_name' => 'Hj. Berkah',
                'phone_number' => '081234500004',
                'address' => 'Jl. Cibadak No. 78, Bandung',
                'latitude' => '-6.9350',
                'longitude' => '107.5950',
                'credit_limit' => 0, // Cash only
                'credit_terms_days' => 0,
                'status' => 'inactive'
            ]
        ];

        foreach ($customers as $cust) {
            Customer::firstOrCreate(['customer_code' => $cust['customer_code']], $cust);
        }

        // 5. Buat Data Kategori Produk (Sesuai Website Ath-Thoifah)
        $cat1 = ProductCategory::firstOrCreate(['code' => 'MDH'], ['name' => 'Madu']);
        $cat2 = ProductCategory::firstOrCreate(['code' => 'KRM'], ['name' => 'Kurma']);
        $cat3 = ProductCategory::firstOrCreate(['code' => 'MZO'], ['name' => 'Minyak Zaitun']);
        $cat4 = ProductCategory::firstOrCreate(['code' => 'KPS'], ['name' => 'Kapsul Tunggal']);

        // 6. Buat Data Produk (Data Asli ath-thoifah.co.id)
        $products = [
            // Madu
            ['sku' => 'MDH-RWY-Karet-250', 'name' => 'Rawney Bees Madu Raw Karet 250ml', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 75000, 'status' => 'active'],
            ['sku' => 'MDH-RWY-Rambutan-250', 'name' => 'Rawney Bees Madu Raw Rambutan 250 ml', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 75000, 'status' => 'active'],
            ['sku' => 'MDH-RWY-Kelengkeng-250', 'name' => 'Rawney Bees Madu Raw Kelengkeng 250 ml', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 75000, 'status' => 'active'],
            ['sku' => 'MDH-RWY-Kaliandra-250', 'name' => 'RAWNEY BEES Raw Honey Nektar Kaliandra 250ml', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 75000, 'status' => 'active'],
            ['sku' => 'MDH-RWY-Randu-250', 'name' => 'RAWNEY BEES Raw Honey Nektar Randu 250ml', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 75000, 'status' => 'active'],
            ['sku' => 'MDH-RWY-Hutan-250', 'name' => 'RAWNEY BEES Raw Honey Nektar Hutan 250ml', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 75000, 'status' => 'active'],
            ['sku' => 'MDH-Femmabe-250', 'name' => 'Madu Promil Femabee 250gr', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 65000, 'status' => 'active'],
            ['sku' => 'MDH-Probapil-Kids', 'name' => 'Madu Probapil Kids Batuk Pilek Anak Ath-Thoifah', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 60000, 'status' => 'active'],
            ['sku' => 'MDH-Solucer', 'name' => 'Madu Solucer Tumor dan Kanker Ath-Thoifah', 'product_category_id' => $cat1->id, 'unit' => 'pcs', 'price' => 140000, 'status' => 'active'],
            
            // Minyak Zaitun
            ['sku' => 'MZO-Zolive-Virgin', 'name' => 'Zolive Minyak Zaitun Virgin Olive Oil', 'product_category_id' => $cat3->id, 'unit' => 'pcs', 'price' => 75000, 'status' => 'active'],
            
            // Kapsul Tunggal
            ['sku' => 'KPS-BuahMerah', 'name' => 'Kapsul Buah Merah Papua Ath-Thoifah', 'product_category_id' => $cat4->id, 'unit' => 'pcs', 'price' => 95000, 'status' => 'active'],
            
            // Kurma
            ['sku' => 'KRM-BK-Ajwa-500', 'name' => 'Burj Khalifa Kurma Ajwa 500gr', 'product_category_id' => $cat2->id, 'unit' => 'pcs', 'price' => 150000, 'status' => 'active'],
            ['sku' => 'KRM-BK-Ajwa-1000', 'name' => 'Burj Khalifa Kurma Ajwa 1000gr', 'product_category_id' => $cat2->id, 'unit' => 'pcs', 'price' => 250000, 'status' => 'active'],
            ['sku' => 'KRM-BK-Khalas-500', 'name' => 'Burj Khalifa Kurma Khalas 500gr', 'product_category_id' => $cat2->id, 'unit' => 'pcs', 'price' => 35000, 'status' => 'active'],
            ['sku' => 'KRM-BK-Khalas-1000', 'name' => 'Burj Khalifa Kurma Khalas 1000gr', 'product_category_id' => $cat2->id, 'unit' => 'pcs', 'price' => 50000, 'status' => 'active'],
        ];

        foreach ($products as $prd) {
            Product::firstOrCreate(['sku' => $prd['sku']], $prd);
        }

        // 7. Buat Jadwal Visit untuk Hari Ini
        $today = now()->format('Y-m-d');
        $budi = Employee::where('employee_code', 'SLM-001')->first();

        $visitCustomers = Customer::where('status', 'active')->limit(2)->get();
        foreach ($visitCustomers as $cust) {
            \App\Models\VisitPlan::firstOrCreate(
                ['employee_id' => $budi->id, 'customer_id' => $cust->id, 'visit_date' => $today],
                ['status' => 'planned']
            );
        }

        // Buat Admin utama jika belum ada (User ID 1)
        $adminUser = User::find(1);
        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }

        // 8. Buat Data Piutang (Receivables)
        $receivables = [
            ['customer_id' => 1, 'reference_code' => 'INV-2026-001', 'total_amount' => 1500000, 'paid_amount' => 500000, 'due_date' => now()->subDays(5)->format('Y-m-d'), 'status' => 'partial'],
            ['customer_id' => 1, 'reference_code' => 'INV-2026-002', 'total_amount' => 800000, 'paid_amount' => 0, 'due_date' => now()->addDays(10)->format('Y-m-d'), 'status' => 'unpaid'],
            ['customer_id' => 2, 'reference_code' => 'INV-2026-003', 'total_amount' => 2000000, 'paid_amount' => 0, 'due_date' => now()->subDays(2)->format('Y-m-d'), 'status' => 'overdue'],
        ];

        foreach ($receivables as $r) {
            \App\Models\Receivable::firstOrCreate(['reference_code' => $r['reference_code']], $r);
        }

        // 9. Buat Data Target Bulan Ini
        $period = now()->format('Y-m');
        foreach (Employee::all() as $emp) {
            \App\Models\Target::firstOrCreate(
                ['employee_id' => $emp->id, 'period_month' => $period],
                ['visit_target' => 90, 'order_target' => 50, 'sales_target' => 50000000, 'collection_target' => 25000000]
            );
        }

        // 10. Buat Data Tugas (Tasks)
        \App\Models\Task::firstOrCreate(
            ['title' => 'Tagih piutang jatuh tempo', 'employee_id' => 2],
            ['customer_id' => 1, 'priority' => 'high', 'due_date' => today(), 'status' => 'pending', 'description' => 'Tagih sisa piutang Toko Makmur']
        );
        \App\Models\Task::firstOrCreate(
            ['title' => 'Pasang POP display promo', 'employee_id' => 2],
            ['customer_id' => 2, 'priority' => 'high', 'due_date' => today(), 'status' => 'pending', 'description' => 'Pasang banner di etalase']
        );

        // 11. Tambah 5 Toko Mitra di Area Bekasi
        $bekasiCustomers = [
            [
                'customer_code' => 'BKS-001',
                'name' => 'Toko Berkah Jaya',
                'owner_name' => 'Pak Jaya',
                'phone_number' => '081234570001',
                'address' => 'Jl. Raya Bekasi Timur No. 12, Bekasi',
                'latitude' => '-6.2382',
                'longitude' => '106.9950',
                'credit_limit' => 5000000,
                'credit_terms_days' => 30,
                'status' => 'active'
            ],
            [
                'customer_code' => 'BKS-002',
                'name' => 'Toko Sumber Rejeki',
                'owner_name' => 'Bu Rejeki',
                'phone_number' => '081234570002',
                'address' => 'Jl. Pondok Gede Raya No. 45, Bekasi',
                'latitude' => '-6.2654',
                'longitude' => '106.8995',
                'credit_limit' => 4000000,
                'credit_terms_days' => 15,
                'status' => 'active'
            ],
            [
                'customer_code' => 'BKS-003',
                'name' => 'Minimarket Amanah',
                'owner_name' => 'Pak Aman',
                'phone_number' => '081234570003',
                'address' => 'Jl. Kemang Pratama No. 8, Bekasi Barat',
                'latitude' => '-6.2240',
                'longitude' => '106.9670',
                'credit_limit' => 7500000,
                'credit_terms_days' => 30,
                'status' => 'active'
            ],
            [
                'customer_code' => 'BKS-004',
                'name' => 'Warung Bu Siti',
                'owner_name' => 'Bu Siti',
                'phone_number' => '081234570004',
                'address' => 'Jl. Raya Tambun No. 78, Bekasi',
                'latitude' => '-6.1715',
                'longitude' => '107.0933',
                'credit_limit' => 2000000,
                'credit_terms_days' => 0,
                'status' => 'active'
            ],
            [
                'customer_code' => 'BKS-005',
                'name' => 'Toko Maju Bersama',
                'owner_name' => 'Pak Maju',
                'phone_number' => '081234570005',
                'address' => 'Jl. Jatiasih No. 88, Bekasi',
                'latitude' => '-6.2615',
                'longitude' => '107.1558',
                'credit_limit' => 6000000,
                'credit_terms_days' => 45,
                'status' => 'active'
            ]
        ];

        $bekasiIds = [];
        foreach ($bekasiCustomers as $cust) {
            $newCust = \App\Models\Customer::firstOrCreate(['customer_code' => $cust['customer_code']], $cust);
            $bekasiIds[] = $newCust->id;
        }

        // 12. Buat Jadwal Visit Hari Ini untuk 5 Toko Bekasi (Ditugaskan ke Budi / SLm-001)
        $budi = Employee::where('employee_code', 'SLM-001')->first();
        $today = now()->format('Y-m-d');
        foreach ($bekasiIds as $cid) {
            \App\Models\VisitPlan::firstOrCreate(
                ['employee_id' => $budi->id, 'customer_id' => $cid, 'visit_date' => $today],
                ['status' => 'planned']
            );
        }

        // 13. Buat Data Piutang (Receivables) untuk Toko Bekasi (Jatuh Tempo Agustus & September 2026)
        $receivableData = [
            ['cust_idx' => 0, 'ref' => 'INV-BKS-001', 'total' => 1500000, 'paid' => 0, 'due' => '2026-08-15', 'status' => 'unpaid'],
            ['cust_idx' => 1, 'ref' => 'INV-BKS-002', 'total' => 2000000, 'paid' => 500000, 'due' => '2026-08-20', 'status' => 'partial'],
            ['cust_idx' => 2, 'ref' => 'INV-BKS-003', 'total' => 3000000, 'paid' => 0, 'due' => '2026-09-10', 'status' => 'unpaid'],
            ['cust_idx' => 4, 'ref' => 'INV-BKS-005', 'total' => 1800000, 'paid' => 0, 'due' => '2026-09-25', 'status' => 'unpaid'],
        ];

        $receivableIds = [];
        foreach ($receivableData as $r) {
            $rec = \App\Models\Receivable::firstOrCreate(
                ['reference_code' => $r['ref']],
                [
                    'customer_id' => $bekasiIds[$r['cust_idx']],
                    'total_amount' => $r['total'],
                    'paid_amount' => $r['paid'],
                    'due_date' => $r['due'],
                    'status' => $r['status']
                ]
            );
            $receivableIds[] = $rec->id;
        }

        // 14. Buat Task Penagihan Piutang untuk Bulan Agustus & September 2026
        $taskData = [
            ['cust_idx' => 0, 'title' => 'Tagih piutang INV-BKS-001', 'due' => '2026-08-15', 'priority' => 'high'],
            ['cust_idx' => 1, 'title' => 'Tagih pelunasan piutang INV-BKS-002', 'due' => '2026-08-20', 'priority' => 'high'],
            ['cust_idx' => 2, 'title' => 'Tagih piutang jatuh tempo September', 'due' => '2026-09-10', 'priority' => 'medium'],
            ['cust_idx' => 4, 'title' => 'Tagih piutang jatuh tempo akhir bulan', 'due' => '2026-09-25', 'priority' => 'medium'],
        ];

        foreach ($taskData as $t) {
            \App\Models\Task::firstOrCreate(
                ['title' => $t['title'], 'employee_id' => $budi->id],
                [
                    'customer_id' => $bekasiIds[$t['cust_idx']],
                    'description' => 'Lakukan penagihan sesuai invoice yang jatuh tempo.',
                    'priority' => $t['priority'],
                    'due_date' => $t['due'],
                    'status' => 'pending'
                ]
            );
        }

        // 15. Buat Dummy Orders (Transaksi Penjualan)
        $salesmen = Employee::where('employee_code', 'LIKE', 'SLM-%')->get();
        $customers = Customer::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        $statuses = ['pending', 'processed', 'delivered', 'cancelled'];

        for ($i = 1; $i <= 8; $i++) {
            $salesman = $salesmen->random();
            $customer = $customers->random();
            $orderItems = [];
            $totalAmount = 0;
            
            // Ambil 2 sampai 4 produk acak untuk setiap order
            $numItems = rand(2, 4);
            $selectedProducts = $products->random($numItems);
            
            foreach ($selectedProducts as $p) {
                $qty = rand(1, 10);
                $subtotal = $p->price * $qty;
                $totalAmount += $subtotal;
                
                $orderItems[] = new \App\Models\OrderItem([
                    'product_id' => $p->id,
                    'qty' => $qty,
                    'price' => $p->price,
                    'subtotal' => $subtotal
                ]);
            }
            
            // Buat Order
            $order = \App\Models\Order::create([
                'order_code' => 'ORD-' . date('ymd') . '-' . rand(1000, 9999) . '-' . $i,
                'visit_id' => null, // Tidak dikaitkan ke visit khusus agar mudah
                'customer_id' => $customer->id,
                'employee_id' => $salesman->id,
                'total_amount' => $totalAmount,
                'status' => $statuses[array_rand($statuses)]
            ]);
            
            // Simpan Item Order
            $order->items()->saveMany($orderItems);
        }

        // 16. Tambah Salesman Extra agar kompetitif
        $extraSalesmen = [
            ['code' => 'SLM-003', 'name' => 'Rizal Pratama', 'area' => $area1->id],
            ['code' => 'SLM-004', 'name' => 'Siti Aminah', 'area' => $area2->id],
            ['code' => 'SLM-005', 'name' => 'Ahmad Fauzi', 'area' => $area1->id],
            ['code' => 'SLM-006', 'name' => 'Dedi Kurniawan', 'area' => $area2->id],
            ['code' => 'SLM-007', 'name' => 'Rina Marlina', 'area' => $area1->id],
        ];
        
        foreach ($extraSalesmen as $es) {
            $user = User::firstOrCreate(
                ['email' => strtolower(str_replace(' ', '.', $es['name'])) . '@ath-thoifah.com'],
                ['name' => $es['name'], 'password' => Hash::make('password')]
            );
            $user->assignRole('salesman');
            
            Employee::firstOrCreate(
                ['employee_code' => $es['code']],
                [
                    'user_id' => $user->id,
                    'full_name' => $es['name'],
                    'phone_number' => '08' . rand(1000000000, 9999999999),
                    'sales_area_id' => $es['area'],
                    'supervisor_id' => $spv->id,
                    'status' => 'active'
                ]
            );
        }

        // 17. Target untuk semua salesman
        $period = now()->format('Y-m');
        foreach (Employee::where('employee_code', 'LIKE', 'SLM-%')->get() as $emp) {
            \App\Models\Target::firstOrCreate(
                ['employee_id' => $emp->id, 'period_month' => $period],
                ['visit_target' => 90, 'order_target' => 50, 'sales_target' => 50000000, 'collection_target' => 25000000]
            );
        }

        // 18. Generate Banyak Order & Visit Bulan Ini
        $salesmen = Employee::where('employee_code', 'LIKE', 'SLM-%')->get();
        $customers = Customer::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        
        for ($i = 1; $i <= 25; $i++) {
            $salesman = $salesmen->random();
            $customer = $customers->random();
            $orderItems = [];
            $totalAmount = 0;
            $numItems = rand(1, 5);
            $selectedProducts = $products->random($numItems);
            
            foreach ($selectedProducts as $p) {
                $qty = rand(1, 15);
                $subtotal = $p->price * $qty;
                $totalAmount += $subtotal;
                $orderItems[] = new \App\Models\OrderItem([
                    'product_id' => $p->id, 'qty' => $qty, 'price' => $p->price, 'subtotal' => $subtotal
                ]);
            }
            
            $orderDate = now()->subDays(rand(0, 20));
            $order = \App\Models\Order::create([
                'order_code' => 'ORD-' . date('ymd') . '-' . rand(1000, 9999) . '-' . $i,
                'visit_id' => null,
                'customer_id' => $customer->id,
                'employee_id' => $salesman->id,
                'total_amount' => $totalAmount,
                'status' => 'delivered',
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);
            $order->items()->saveMany($orderItems);

            // Generate Visit untuk order ini
            $plan = \App\Models\VisitPlan::create([
                'employee_id' => $salesman->id,
                'customer_id' => $customer->id,
                'visit_date' => $orderDate->format('Y-m-d'),
                'status' => 'completed'
            ]);
            \App\Models\Visit::create([
                'visit_plan_id' => $plan->id,
                'employee_id' => $salesman->id,
                'customer_id' => $customer->id,
                'check_in_at' => $orderDate,
                'check_in_lat' => '-6.2', 'check_in_lng' => '106.8',
                'distance_meters' => rand(10, 100), 'check_in_status' => 'valid',
                'check_in_photo' => 'dummy.jpg'
            ]);
        }

        // 19. Generate Banyak Collection (Verified) Bulan Ini
        for ($i = 1; $i <= 10; $i++) {
            $salesman = $salesmen->random();
            $customer = $customers->random();
            $receivable = \App\Models\Receivable::firstOrCreate(
                ['customer_id' => $customer->id, 'reference_code' => 'INV-RAND-' . $customer->id],
                ['total_amount' => rand(1000000, 5000000), 'paid_amount' => 0, 'due_date' => now()->addDays(30), 'status' => 'unpaid']
            );

            $amount = rand(500000, 2000000);
            \App\Models\Collection::create([
                'receivable_id' => $receivable->id, 'visit_id' => null, 'employee_id' => $salesman->id,
                'amount' => $amount, 'payment_date' => now()->subDays(rand(0, 20)),
                'payment_method' => 'transfer', 'status' => 'verified', 'notes' => 'Transfer dummy'
            ]);
            $receivable->paid_amount += $amount;
            $receivable->status = $receivable->paid_amount >= $receivable->total_amount ? 'paid' : 'partial';
            $receivable->save();
        }

        // 20. Generate Data Massal untuk Grafik Target Achievement Bulan Ini
        $salesmen = Employee::where('employee_code', 'LIKE', 'SLM-%')->get();
        $customers = Customer::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        
        // A. Generate Kunjungan (Target Tim: 90 x 7 sales = 630. Kita buat ~450)
        for ($i = 1; $i <= 450; $i++) {
            $salesman = $salesmen->random();
            $customer = $customers->random();
            $visitDate = now()->subDays(rand(0, 20));
            
            $plan = \App\Models\VisitPlan::firstOrCreate(
                ['employee_id' => $salesman->id, 'customer_id' => $customer->id, 'visit_date' => $visitDate->format('Y-m-d')],
                ['status' => 'completed']
            );
            
            \App\Models\Visit::create([
                'visit_plan_id' => $plan->id,
                'employee_id' => $salesman->id,
                'customer_id' => $customer->id,
                'check_in_at' => $visitDate,
                'check_in_lat' => '-6.2', 'check_in_lng' => '106.8',
                'distance_meters' => rand(10, 100), 'check_in_status' => 'valid',
                'check_in_photo' => 'dummy.jpg'
            ]);
        }

        // B. Generate Order & Sales Value (Target Tim: 50 x 7 = 350 Order, 350jt Sales. Kita buat ~250 Order)
        for ($i = 1; $i <= 250; $i++) {
            $salesman = $salesmen->random();
            $customer = $customers->random();
            $orderItems = [];
            $totalAmount = 0;
            $numItems = rand(1, 4);
            $selectedProducts = $products->random($numItems);
            
            foreach ($selectedProducts as $p) {
                $qty = rand(1, 12);
                $subtotal = $p->price * $qty;
                $totalAmount += $subtotal;
                $orderItems[] = new \App\Models\OrderItem([
                    'product_id' => $p->id, 'qty' => $qty, 'price' => $p->price, 'subtotal' => $subtotal
                ]);
            }
            
            $orderDate = now()->subDays(rand(0, 20));
            $order = \App\Models\Order::create([
                'order_code' => 'ORD-GRAF-' . $i,
                'visit_id' => null,
                'customer_id' => $customer->id,
                'employee_id' => $salesman->id,
                'total_amount' => $totalAmount,
                'status' => 'delivered',
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);
            $order->items()->saveMany($orderItems);
        }

        // C. Generate Collection (Target Tim: 25jt x 7 = 175jt. Kita buat ~120jt)
        for ($i = 1; $i <= 60; $i++) {
            $salesman = $salesmen->random();
            $customer = $customers->random();
            $receivable = \App\Models\Receivable::firstOrCreate(
                ['customer_id' => $customer->id, 'reference_code' => 'INV-GRAF-' . $customer->id],
                ['total_amount' => rand(2000000, 10000000), 'paid_amount' => 0, 'due_date' => now()->addDays(30), 'status' => 'unpaid']
            );

            $amount = rand(1000000, 3000000);
            \App\Models\Collection::create([
                'receivable_id' => $receivable->id, 'visit_id' => null, 'employee_id' => $salesman->id,
                'amount' => $amount, 'payment_date' => now()->subDays(rand(0, 20)),
                'payment_method' => ['cash', 'transfer', 'qris'][array_rand(['cash', 'transfer', 'qris'])], 
                'status' => 'verified', 'notes' => 'Pembayaran dummy grafik'
            ]);
            $receivable->paid_amount += $amount;
            $receivable->status = $receivable->paid_amount >= $receivable->total_amount ? 'paid' : 'partial';
            $receivable->save();
        }

    }
}