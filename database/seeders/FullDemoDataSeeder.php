<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\SalesArea;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Target;
use App\Models\VisitPlan;
use App\Models\VisitScheduleRequest;
use App\Models\Visit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Receivable;
use App\Models\Collection;
use App\Models\Task;
use App\Models\OnlineReport;
use App\Models\OnlineReportItem;
use App\Models\CustomerStockDiscount;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FullDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CLEAN UP TRANSAKSI DUMMY SAJA (TIDAK MENGHAPUS MASTER DATA)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OnlineReportItem::truncate();
        OnlineReport::truncate();
        Collection::truncate();
        Receivable::truncate();
        OrderItem::truncate();
        Order::truncate();
        Visit::truncate();
        VisitPlan::truncate();
        VisitScheduleRequest::truncate();
        Task::truncate();
        CustomerStockDiscount::truncate();
        Target::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. APP SETTINGS (Jika belum ada)
        AppSetting::firstOrCreate(['key' => 'approval_deadline_time'], ['value' => '10:00']);
        AppSetting::firstOrCreate(['key' => 'low_stock_threshold'], ['value' => '50']);

        // 3. AMBIL DATA YANG SUDAH ADA (TIDAK MENDELETE)
        $salesmen = Employee::whereHas('user', fn($q) => $q->role('salesman'))->get();
        $customers = Customer::all();
        $products = Product::all();

        // Jika belum ada salesman sama sekali, buat 1 dummy
        if ($salesmen->isEmpty()) {
            $slmUser = User::create(['name' => 'Budi Salesman', 'email' => 'budi@ath-thoifah.com', 'password' => Hash::make('password')]);
            $slmUser->assignRole('salesman');
            $budi = Employee::create(['user_id' => $slmUser->id, 'employee_code' => 'SLM-99', 'full_name' => 'Budi Salesman', 'type' => 'offline', 'status' => 'active']);
            $salesmen = collect([$budi]);
        }

        // Jika belum ada customer sama sekali, buat 1 dummy
        if ($customers->isEmpty()) {
            $cust1 = Customer::create(['customer_code' => 'TOK-99', 'name' => 'Toko Demo', 'status' => 'active', 'credit_limit' => 5000000, 'credit_terms_days' => 30]);
            $customers = collect([$cust1]);
        }

        // Jika belum ada produk sama sekali, buat 1 dummy
        if ($products->isEmpty()) {
            $cat = ProductCategory::create(['name' => 'Umum', 'code' => 'UMM']);
            $p1 = Product::create(['sku' => 'PRD-99', 'name' => 'Produk Demo', 'product_category_id' => $cat->id, 'unit' => 'pcs', 'hpp' => 5000, 'price' => 10000, 'stock' => 100, 'status' => 'active']);
            $products = collect([$p1]);
        }

        // 4. BUAT TARGET UNTUK SEMUA SALESMAN
        $period = now()->format('Y-m');
        foreach ($salesmen as $emp) {
            Target::firstOrCreate(
                ['employee_id' => $emp->id, 'period_month' => $period],
                ['visit_target' => 90, 'order_target' => 50, 'sales_target' => 50000000, 'collection_target' => 25000000]
            );
        }

        $offlineSalesmen = $salesmen->where('type', 'offline');
        $onlineSalesmen = $salesmen->where('type', 'online');

        // Helper function untuk mengambil produk acak secara amn
        $getRandomProducts = function($count) use ($products) {
            $count = min($count, $products->count());
            $selected = $products->random($count);
            return ($selected instanceof \Illuminate\Support\Collection) ? $selected->all() : [$selected];
        };

        // ==========================================
        // FITUR: APPROVAL JADWAL (Visit Schedule Request)
        // ==========================================
        foreach ($offlineSalesmen as $slm) {
            for ($i = 0; $i < 3; $i++) {
                $cust = $customers->random();
                $date = now()->addDays(rand(0, 1))->format('Y-m-d');
                VisitScheduleRequest::firstOrCreate([
                    'employee_id' => $slm->id,
                    'customer_id' => $cust->id,
                    'visit_date' => $date,
                    'status' => 'pending'
                ]);
            }
        }

        // ==========================================
        // FITUR: VISIT PLANNING (Planned Visits)
        // ==========================================
        foreach ($offlineSalesmen as $slm) {
            for ($i = 0; $i < 5; $i++) {
                $cust = $customers->random();
                $date = now()->addDays(rand(0, 1))->format('Y-m-d');
                VisitPlan::firstOrCreate([
                    'employee_id' => $slm->id,
                    'customer_id' => $cust->id,
                    'visit_date' => $date,
                    'status' => 'planned'
                ]);
            }
        }

        // ==========================================
        // FITUR: MONITORING KUNJUNGAN, ORDER & COLLECTION (Completed Visits)
        // ==========================================
        foreach ($offlineSalesmen as $slm) {
            for ($i = 0; $i < 5; $i++) {
                $visitDate = now()->subDays(rand(0, 6));
                $cust = $customers->random();

                $plan = VisitPlan::create([
                    'employee_id' => $slm->id, 'customer_id' => $cust->id, 'visit_date' => $visitDate->format('Y-m-d'), 'status' => 'completed', 'created_at' => $visitDate, 'updated_at' => $visitDate
                ]);

                $checkIn = $visitDate->copy()->setTime(9, 0);
                $visit = Visit::create([
                    'visit_plan_id' => $plan->id, 'employee_id' => $slm->id, 'customer_id' => $cust->id, 'check_in_at' => $checkIn, 'check_in_lat' => $cust->latitude, 'check_in_lng' => $cust->longitude, 'distance_meters' => rand(10, 150), 'check_in_status' => 'valid', 'check_in_photo' => 'dummy.jpg', 'check_out_at' => $checkIn->copy()->addMinutes(45), 'created_at' => $checkIn, 'updated_at' => $checkIn
                ]);

                if (rand(1, 10) > 3) {
                    $totalAmount = 0;
                    $orderItems = [];
                    $numItems = rand(1, 3);
                    $selectedProducts = $getRandomProducts($numItems);

                    $paymentType = ['cash', 'konsinyasi', 'piutang'][array_rand(['cash', 'konsinyasi', 'piutang'])];

                    foreach ($selectedProducts as $p) {
                        $qty = rand(1, 5);
                        $subtotal = $p->price * $qty;
                        $totalAmount += $subtotal;
                        $orderItems[] = new OrderItem(['product_id' => $p->id, 'qty' => $qty, 'price' => $p->price, 'subtotal' => $subtotal]);
                    }

                    $order = Order::create([
                        'order_code' => 'ORD-' . $visitDate->format('ymd') . '-' . Str::random(4), 'visit_id' => $visit->id, 'customer_id' => $cust->id, 'employee_id' => $slm->id, 'total_amount' => $totalAmount, 'payment_type' => $paymentType, 'status' => 'delivered', 'created_at' => $checkIn->copy()->addMinutes(10), 'updated_at' => $checkIn->copy()->addMinutes(10)
                    ]);
                    $order->items()->saveMany($orderItems);

                    if ($paymentType === 'piutang') {
                        $receivable = Receivable::create([
                            'customer_id' => $cust->id, 'reference_code' => 'INV-' . $order->order_code, 'total_amount' => $totalAmount, 'paid_amount' => 0, 'due_date' => $visitDate->copy()->addDays($cust->credit_terms_days), 'status' => 'unpaid', 'created_at' => $checkIn, 'updated_at' => $checkIn
                        ]);

                        if (rand(1, 10) > 5) {
                            $payAmount = round($totalAmount * 0.5);
                            $colStatus = ['pending', 'verified'][array_rand(['pending', 'verified'])];
                            Collection::create([
                                'receivable_id' => $receivable->id, 'visit_id' => $visit->id, 'employee_id' => $slm->id, 'amount' => $payAmount, 'payment_date' => $visitDate, 'payment_method' => 'cash', 'status' => $colStatus, 'created_at' => $checkIn, 'updated_at' => $checkIn
                            ]);
                            if ($colStatus === 'verified') {
                                $receivable->update(['paid_amount' => $payAmount, 'status' => 'partial']);
                            }
                        } else {
                            if ($i === 0) {
                                $receivable->update(['due_date' => now()->subDay(), 'status' => 'overdue']);
                            }
                        }
                    }
                }
            }
        }

        // ==========================================
        // FITUR: LAPORAN ONLINE (Untuk Salesman Online)
        // ==========================================
        foreach ($onlineSalesmen as $slm) {
            for ($i = 0; $i < 3; $i++) {
                $reportDate = now()->subDays($i);
                $totalAmount = 0;
                $reportItems = [];
                $numItems = rand(1, 4);
                $selectedProducts = $getRandomProducts($numItems);

                foreach ($selectedProducts as $p) {
                    $qty = rand(1, 10);
                    $subtotal = $p->price * $qty;
                    $totalAmount += $subtotal;
                    $reportItems[] = ['product_id' => $p->id, 'qty' => $qty, 'price' => $p->price, 'subtotal' => $subtotal];
                }

                $report = OnlineReport::create([
                    'employee_id' => $slm->id, 'report_date' => $reportDate->format('Y-m-d'), 'start_time' => '08:00', 'end_time' => '16:00', 'total_amount' => $totalAmount, 'notes' => 'Promosi via Instagram dan WA Group', 'created_at' => $reportDate, 'updated_at' => $reportDate
                ]);
                $report->items()->createMany($reportItems);
            }
        }

        // ==========================================
        // FITUR: TASK MANAGEMENT
        // ==========================================
        foreach ($salesmen as $slm) {
            if ($customers->isNotEmpty()) {
                Task::create(['employee_id' => $slm->id, 'customer_id' => $customers->random()->id, 'title' => 'Tagih piutang jatuh tempo', 'description' => 'Tagih sisa piutang', 'priority' => 'high', 'due_date' => now()->addDays(2), 'status' => 'pending']);
                Task::create(['employee_id' => $slm->id, 'customer_id' => $customers->random()->id, 'title' => 'Pasang POP Display', 'description' => 'Pasang standing banner', 'priority' => 'medium', 'due_date' => now()->subDay(), 'status' => 'overdue']);
                Task::create(['employee_id' => $slm->id, 'customer_id' => $customers->random()->id, 'title' => 'Survey harga kompetitor', 'description' => 'Cek harga di toko sebelah', 'priority' => 'low', 'due_date' => now()->subDays(2), 'status' => 'completed']);
            }
        }

        $this->command->info('Data Aktivitas Lapangan (Approval, Visit, Order, Collection, Task) berhasil dibuat tanpa error!');
    }
}