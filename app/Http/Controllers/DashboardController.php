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
        $startOfDay = $today->copy()->startOfDay();
        $endOfDay = $today->copy()->endOfDay();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        $period = $today->format('Y-m');

        // 1. KPI Cards (Optimized using whereBetween untuk Indexing)
        $totalVisitsToday = Visit::whereBetween('check_in_at', [$startOfDay, $endOfDay])->count();
        $totalOrdersToday = Order::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $salesValueToday = Order::whereBetween('created_at', [$startOfDay, $endOfDay])->sum('total_amount');
        $collectionsToday = Collection::whereBetween('payment_date', [$startOfDay, $endOfDay])->sum('amount');
        $overdueTasks = Task::where('status', '!=', 'completed')->where('due_date', '<', $today)->count();

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

        // 2. Kunjungan 7 Hari Terakhir (Optimized: 7 Queries -> 1 Query)
        $startDate = $today->copy()->subDays(6)->startOfDay();
        $visitsData = Visit::selectRaw('DATE(check_in_at) as date, COUNT(*) as count')
            ->whereBetween('check_in_at', [$startDate, $endOfDay])
            ->groupBy('date')
            ->pluck('count', 'date');

        $weekVisits = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $weekVisits[] = [
                'day' => $date->translatedFormat('D'), 
                'val' => $visitsData[$dateStr] ?? 0
            ];
        }
        $maxVisit = max(1, max(array_column($weekVisits, 'val')));

        // 3. Top Performers (Optimized: Eager Load salesArea untuk hapus N+1 di view)
        $topPerformers = Employee::whereHas('user', fn($q) => $q->role('salesman'))
            ->with('salesArea') // Mencegah query berulang saat memanggil nama area
            ->withSum(['orders' => function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth])->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->take(5)
            ->get();

        // 4. Target Achievement (Optimized using whereBetween)
        $totalTarget = Target::where('period_month', $period)->get();
        $orgMetrics = [
            ['label' => 'Visit Completion', 'pct' => $this->calcPct(Visit::whereBetween('check_in_at', [$startOfMonth, $endOfMonth])->count(), $totalTarget->sum('visit_target'))],
            ['label' => 'Order Conversion', 'pct' => $this->calcPct(Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(), $totalTarget->sum('order_target'))],
            ['label' => 'Sales Achievement', 'pct' => $this->calcPct(Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total_amount'), $totalTarget->sum('sales_target'))],
            ['label' => 'Collection Achievement', 'pct' => $this->calcPct(Collection::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('amount'), $totalTarget->sum('collection_target'))],
        ];

        // 5. Aktivitas Terbaru (Sudah menggunakan eager loading)
        $recentActivities = [];
        $visits = Visit::with('employee', 'customer')->latest()->take(3)->get();
        foreach ($visits as $v) {
            $recentActivities[] = ['time' => $v->check_in_at->format('H:i'), 'who' => $v->employee->full_name, 'what' => 'check-in di ' . $v->customer->name];
        }
        $orders = Order::with('customer')->latest()->take(2)->get();
        foreach ($orders as $o) {
            $recentActivities[] = ['time' => $o->created_at->format('H:i'), 'who' => 'System', 'what' => 'mencatat order Rp ' . number_format($o->total_amount, 0, ',', '.') . ' — ' . $o->customer->name];
        }

        // 6. DATA WIDGET PERINGATAN
        $lowStockThreshold = AppSetting::get('low_stock_threshold', 100);
        
        $dueReceivables = Receivable::where('status', '!=', 'paid')
            ->where('due_date', '<=', $today->copy()->addDays(7)->endOfDay())
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