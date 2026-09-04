<?php

namespace App\Http\Controllers;

use App\Models\VisitPlan;
use App\Models\Visit;
use App\Models\Order;
use App\Models\Collection;
use App\Models\Task;
use App\Models\Target;
use App\Models\Employee;
use App\Models\Receivable;
use App\Models\Product;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('salesman')) {
            return redirect()->route('salesman.home');
        }

        $today = today();
        $period = $today->format('Y-m');

        // 1. KPI Cards (Real Data)
        $totalVisitsToday = Visit::whereDate('check_in_at', $today)->count();
        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $salesValueToday = Order::whereDate('created_at', $today)->sum('total_amount');
        $collectionsToday = Collection::whereDate('payment_date', $today)->sum('amount');
        $overdueTasks = Task::where('status', '!=', 'completed')->whereDate('due_date', '<', $today)->count();

        $stats = [
            'visit_pct' => 86, // Dummy stat bisa disesuaikan logika target harian
            'visit_delta' => $totalVisitsToday . ' visits hari ini',
            'orders' => $totalOrdersToday,
            'order_delta' => 'order masuk',
            'sales_value' => 'Rp ' . number_format($salesValueToday / 1000000, 1) . 'jt',
            'sales_delta' => 'penjualan hari ini',
            'collections' => 'Rp ' . number_format($collectionsToday / 1000000, 1) . 'jt',
            'collection_delta' => 'tertagih hari ini',
        ];

        // 2. Kunjungan 7 Hari Terakhir (Real Data)
        $weekVisits = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $count = Visit::whereDate('check_in_at', $date)->count();
            $weekVisits[] = ['day' => $date->translatedFormat('D'), 'val' => $count];
        }
        
        $maxVisit = max(1, max(array_column($weekVisits, 'val')));

        // 3. Top Performers (Real Data)
        $topPerformers = Employee::whereHas('user', fn($q) => $q->role('salesman'))
            ->withSum(['orders' => function($q) use ($today) {
                $q->whereMonth('created_at', $today->month)->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->take(5)
            ->get();

        // 4. Target Achievement (Real Data Agregate)
        $totalTarget = Target::where('period_month', $period)->get();
        $orgMetrics = [
            ['label' => 'Visit Completion', 'pct' => $this->calcPct(Visit::whereMonth('check_in_at', $today->month)->count(), $totalTarget->sum('visit_target'))],
            ['label' => 'Order Conversion', 'pct' => $this->calcPct(Order::whereMonth('created_at', $today->month)->count(), $totalTarget->sum('order_target'))],
            ['label' => 'Sales Achievement', 'pct' => $this->calcPct(Order::whereMonth('created_at', $today->month)->sum('total_amount'), $totalTarget->sum('sales_target'))],
            ['label' => 'Collection Achievement', 'pct' => $this->calcPct(Collection::whereMonth('payment_date', $today->month)->sum('amount'), $totalTarget->sum('collection_target'))],
        ];

        // 5. Aktivitas Terbaru (Real Data)
        $recentActivities = [];
        $visits = Visit::with('employee', 'customer')->latest()->take(3)->get();
        foreach ($visits as $v) {
            $recentActivities[] = ['time' => $v->check_in_at->format('H:i'), 'who' => $v->employee->full_name, 'what' => 'check-in di ' . $v->customer->name];
        }
        $orders = Order::with('customer')->latest()->take(2)->get();
        foreach ($orders as $o) {
            $recentActivities[] = ['time' => $o->created_at->format('H:i'), 'who' => 'System', 'what' => 'mencatat order Rp ' . number_format($o->total_amount, 0, ',', '.') . ' — ' . $o->customer->name];
        }

        // 6. DATA BARU UNTUK WIDGET PERINGATAN
        $lowStockThreshold = AppSetting::get('low_stock_threshold', 100);
        
        $dueReceivables = Receivable::where('status', '!=', 'paid')
            ->whereDate('due_date', '<=', $today->copy()->addDays(7))
            ->with('customer')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        $lowStockProducts = Product::where('stock', '<', $lowStockThreshold)
            ->where('status', 'active')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        $pendingOrders = Order::where('status', 'pending')
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        // PERBAIKAN: Ganti 'atRisk' menjadi 'topPerformers'
        return view('dashboard', compact(
            'stats', 'weekVisits', 'maxVisit', 'topPerformers', 'orgMetrics', 'recentActivities', 
            'dueReceivables', 'lowStockProducts', 'pendingOrders', 'lowStockThreshold'
        ));
    }

    private function calcPct($actual, $target) {
        if ($target == 0) return 0;
        $pct = round(($actual / $target) * 100);
        return min($pct, 100); // Maksimal 100%
    }
}