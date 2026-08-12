<?php

namespace App\Http\Controllers;

use App\Models\VisitPlan;
use App\Models\Visit;
use App\Models\Order;
use App\Models\Collection;
use App\Models\Task;
use App\Models\Target;
use App\Models\Employee;
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
        $maxVisit = max(array_column($weekVisits, 'val') ?: [1]);

        // 3. Salesman Berisiko (Real Data: Cari yang targetnya di bawah 50%)
        $atRisk = [];
        $employees = Employee::where('status', 'active')->has('target')->get();
        foreach ($employees as $emp) {
            $target = $emp->target()->where('period_month', $period)->first();
            if ($target) {
                $actualVisits = Visit::where('employee_id', $emp->id)->whereMonth('check_in_at', $today->month)->count();
                $pct = $target->visit_target > 0 ? round(($actualVisits / $target->visit_target) * 100) : 0;
                if ($pct < 50) {
                    $atRisk[] = ['inisial' => strtoupper(substr($emp->full_name, 0, 2)), 'nama' => $emp->full_name, 'alasan' => 'Target kunjungan tertinggal', 'pct' => $pct];
                }
            }
        }

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

        return view('dashboard', compact('stats', 'weekVisits', 'maxVisit', 'atRisk', 'orgMetrics', 'recentActivities'));
    }

    private function calcPct($actual, $target) {
        if ($target == 0) return 0;
        $pct = round(($actual / $target) * 100);
        return min($pct, 100); // Maksimal 100%
    }
}