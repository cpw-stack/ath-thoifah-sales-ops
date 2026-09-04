<?php

namespace App\Http\Controllers\Salesman;

use App\Http\Controllers\Controller;
use App\Models\VisitPlan;
use App\Models\Visit;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\VisitProductCheck;
use App\Models\Receivable;
use App\Models\Collection;
use App\Models\Task;
use App\Models\Customer;
use App\Models\VisitScheduleRequest;
use App\Models\CustomerStockDiscount; // Ditambahkan untuk logika diskon
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VisitController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Jika Admin, lihat semua visit hari ini (menggunakan layout app/dashboard)
        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('supervisor')) {
            $statusFilter = request('status');
            $employeeFilter = request('employee_id');
            $customerFilter = request('customer_id');
            
            $plans = VisitPlan::with('customer', 'visit', 'employee')
                ->whereDate('visit_date', today())
                ->when($statusFilter == 'planned', function($q) {
                    $q->where('status', 'planned');
                })
                ->when($statusFilter == 'visited', function($q) {
                    $q->where('status', 'completed')->whereHas('visit', function($q2) {
                        $q2->whereNull('check_out_at');
                    });
                })
                ->when($statusFilter == 'done', function($q) {
                    $q->where('status', 'completed')->whereHas('visit', function($q2) {
                        $q2->whereNotNull('check_out_at');
                    });
                })
                ->when($employeeFilter, function($q) {
                    $q->where('employee_id', request('employee_id'));
                })
                ->when($customerFilter, function($q) {
                    $q->where('customer_id', request('customer_id'));
                })
                ->orderByRaw("FIELD(status, 'planned', 'completed')")
                ->latest()
                ->paginate(15);
                
            // Kirim data untuk dropdown filter
            $employees = \App\Models\Employee::where('status', 'active')->get();
            $customers = \App\Models\Customer::where('status', 'active')->get();

            return view('admin.visits.monitoring', compact('plans', 'employees', 'customers'));
        }

        // Jika Salesman, lihat visit miliknya saja (menggunakan layout mobile)
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Data salesman belum lengkap.');
        }

        $plans = VisitPlan::with('customer', 'visit')
            ->where('employee_id', $user->employee->id)
            ->whereDate('visit_date', today())
            ->get();

        // AMBIL DATA USULAN JADWAL (Hari ini & Besok)
        $scheduleRequests = VisitScheduleRequest::with('customer')
            ->where('employee_id', $user->employee->id)
            ->whereDate('visit_date', '>=', today())
            ->orderBy('visit_date', 'asc')
            ->get();

        // Ambil tugas pending hari ini (dipertahankan agar tidak merusak view)
        $tasks = Task::where('employee_id', $user->employee->id)
            ->where('status', 'pending')
            ->whereDate('due_date', today())
            ->get();

        return view('salesman.visits.index', compact('plans', 'scheduleRequests', 'tasks'));
    }

    public function show(Visit $visit)
    {
        $visit->load('customer', 'productChecks.product', 'order.items.product');
        $products = Product::where('status', 'active')->get();
        
        // Ambil piutang toko
        $receivables = Receivable::where('customer_id', $visit->customer_id)
            ->where('status', '!=', 'paid')
            ->get();
            
        // Ambil tugas yang ada lampirannya (untuk invoice penagihan)
        $tasks = \App\Models\Task::where('customer_id', $visit->customer_id)
            ->whereNotNull('attachment')
            ->get();

        // AMBIL DATA DISKON MITRA
        $discount = CustomerStockDiscount::where('customer_id', $visit->customer_id)
            ->where('is_active', true)
            ->latest()
            ->first();

        // Cek tipe salesman (online/offline)
        $isOnlineSalesman = $visit->employee->type === 'online';

        return view('salesman.visits.show', compact('visit', 'products', 'receivables', 'tasks', 'discount', 'isOnlineSalesman'));
    }

    public function showTask(Task $task)
    {
        // Pastikan salesman hanya bisa melihat tugasnya sendiri
        if (auth()->user()->employee->id !== $task->employee_id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $task->load('customer', 'employee');
        
        return view('salesman.tasks.show', compact('task'));
    }

    public function checkIn(Request $request, VisitPlan $plan)
    {
        // Pastikan hanya salesman yang bersangkutan bisa check-in
        if (!auth()->user()->hasRole('salesman') || auth()->user()->employee->id != $plan->employee_id) {
            abort(403, 'Anda tidak bisa melakukan check-in untuk jadwal orang lain.');
        }

        // Ubah validasi latitude & longitude menjadi nullable untuk testing di PC
        $request->validate([
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'photo' => 'required|image|max:2048'
        ]);

        $customerLat = $plan->customer->latitude ?? -6.200000; // Default dummy
        $customerLng = $plan->customer->longitude ?? 106.816666; // Default dummy
        
        // Gunakan koordinat dummy jika GPS tidak didapat
        $lat = $request->latitude ?? -6.200000;
        $lng = $request->longitude ?? 106.816666;
        
        $distance = $this->calculateDistance($lat, $lng, $customerLat, $customerLng);

        $status = $distance <= 200 ? 'valid' : 'invalid';
        $photoPath = $request->file('photo')->store('visit_photos', 'public');

        Visit::create([
            'visit_plan_id' => $plan->id,
            'employee_id' => auth()->user()->employee->id,
            'customer_id' => $plan->customer_id,
            'check_in_at' => now(),
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
            'distance_meters' => round($distance),
            'check_in_status' => $status,
            'check_in_photo' => $photoPath
        ]);

        $plan->update(['status' => 'completed']);

        return redirect()->route('salesman.visits.index')->with('success', "Check-in berhasil! Jarak: " . round($distance) . "m ($status).");
    }

    public function checkOut(Request $request, Visit $visit)
    {
        if (!auth()->user()->hasRole('salesman') || auth()->user()->employee->id != $visit->employee_id) {
            abort(403, 'Anda tidak bisa melakukan check-out untuk kunjungan orang lain.');
        }

        $request->validate(['notes' => 'nullable|string']);
        $visit->update(['check_out_at' => now(), 'notes' => $request->notes]);

        return redirect()->route('salesman.visits.index')->with('success', 'Check-out berhasil.');
    }

    public function storeProductCheck(Request $request, Visit $visit)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.is_available' => 'required|boolean',
            'products.*.stock_estimate' => 'nullable|integer|min:0',
        ]);

        foreach ($request->products as $p) {
            VisitProductCheck::updateOrCreate(
                ['visit_id' => $visit->id, 'product_id' => $p['id']],
                ['is_available' => $p['is_available'], 'stock_estimate' => $p['stock_estimate'] ?? 0]
            );
        }

        return redirect()->route('salesman.visits.show', $visit)->with('success', 'Cek produk berhasil disimpan.');
    }

    public function storeOrder(Request $request, Visit $visit)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_type' => 'required|in:cash,konsinyasi,piutang',
        ]);

        $totalAmount = 0;
        $orderItems = [];
        $insufficientStock = [];

        // 1. Cek ketersediaan stok gudang pusat terlebih dahulu
        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            if ($product->stock < $item['qty']) {
                $insufficientStock[] = $product->name . " (Sisa stok: {$product->stock} {$product->unit})";
            }
        }

        // Jika ada stok yang kurang, batalkan transaksi dan kasih tau salesman
        if (count($insufficientStock) > 0) {
            return back()->with('error', 'Gagal membuat order. Stok gudang pusat tidak mencukupi untuk: ' . implode(', ', $insufficientStock))->withInput();
        }

        // 2. Jika stok cukup, kurangi stok gudang pusat dan hitung total
        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            
            // Kurangi stok gudang pusat
            $product->decrement('stock', $item['qty']);
            
            $subtotal = $product->price * $item['qty'];
            $totalAmount += $subtotal;
            
            $orderItems[] = new OrderItem([
                'product_id' => $product->id,
                'qty' => $item['qty'],
                'price' => $product->price,
                'subtotal' => $subtotal
            ]);
        }

        // 3. Hitung diskon jika ada yang approved
        $discount = CustomerStockDiscount::where('customer_id', $visit->customer_id)
            ->where('is_active', true)
            ->where('is_approved', true)
            ->latest()->first();

        $discountAmount = 0;
        if ($discount && $discount->discount_value > 0) {
            $discountAmount = ($totalAmount * $discount->discount_value) / 100;
        }
        $finalAmount = $totalAmount - $discountAmount;

        // 4. Simpan Order
        $order = Order::create([
            'order_code' => 'ORD-' . date('ymd') . '-' . Str::random(4),
            'visit_id' => $visit->id,
            'customer_id' => $visit->customer_id,
            'employee_id' => $visit->employee_id,
            'total_amount' => $finalAmount,
            'payment_type' => $request->payment_type,
            'status' => 'pending'
        ]);

        $order->items()->saveMany($orderItems);

        return back()->with('success', "Order berhasil! Total: Rp " . number_format($finalAmount, 0, ',', '.') . " (" . $request->payment_type . "). Stok gudang telah diperbarui.");
    }

    public function storeCollection(Request $request, Visit $visit)
    {
        $request->validate([
            'receivable_id' => 'required|exists:receivables,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,transfer,qris,other',
            'payment_proof' => 'nullable|image|max:2048',
            'notes' => 'nullable|string'
        ]);

        $receivable = Receivable::findOrFail($request->receivable_id);
        
        // Pastikan tidak bayar lebih dari sisa tagihan
        if ($request->amount > $receivable->remaining_amount) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan (Rp ' . number_format($receivable->remaining_amount, 0, ',', '.') . ')');
        }

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // Catat transaksi collection
        Collection::create([
            'receivable_id' => $receivable->id,
            'visit_id' => $visit->id,
            'employee_id' => auth()->user()->employee->id,
            'amount' => $request->amount,
            'payment_date' => today(),
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'payment_proof' => $proofPath,
            'notes' => $request->notes
        ]);

        // Update piutang
        $receivable->paid_amount += $request->amount;
        if ($receivable->paid_amount >= $receivable->total_amount) {
            $receivable->status = 'paid';
        } else {
            $receivable->status = 'partial';
        }
        $receivable->save();

        return back()->with('success', "Penagihan berhasil! Diterima Rp " . number_format($request->amount, 0, ',', '.'));
    }

    // =========================================================
    // FITUR BARU: Usulan Jadwal Kunjungan oleh Salesman
    // =========================================================

    public function createSchedule()
    {
        $employee = auth()->user()->employee;
        // Ambil toko yang aktif
        $customers = Customer::where('status', 'active')->get();
        return view('salesman.schedule.create', compact('customers'));
    }

    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'visit_date' => 'required|date|in:' . today()->format('Y-m-d') . ',' . today()->copy()->addDay()->format('Y-m-d'),
        ]);

        VisitScheduleRequest::firstOrCreate(
            [
                'employee_id' => auth()->user()->employee->id,
                'customer_id' => $validated['customer_id'],
                'visit_date' => $validated['visit_date'],
            ],
            ['status' => 'pending']
        );

        return redirect()->route('salesman.home')->with('success', 'Usulan jadwal berhasil dikirim, menunggu approval Admin.');
    }

    // =========================================================
    // FITUR BARU: Pengajuan Diskon oleh Salesman
    // =========================================================

    public function proposeDiscount(Request $request, Visit $visit)
    {
        $request->validate([
            'discount_value' => 'required|numeric|min:0|max:100',
        ]);

        // Gunakan updateOrCreate agar data diskon lama ditimpa, tidak menumpuk data baru
        CustomerStockDiscount::updateOrCreate(
            ['customer_id' => $visit->customer_id],
            [
                'type' => 'percentage',
                'value' => $request->discount_value,
                'discount_value' => $request->discount_value,
                'is_active' => true,
                'is_approved' => false, // Set kembali ke menunggu approval
            ]
        );

        return back()->with('success', 'Pengajuan diskon ' . $request->discount_value . '% terkirim ke Admin.');
    }

    // =========================================================

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) return 0;
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344 * 1000);
    }
}