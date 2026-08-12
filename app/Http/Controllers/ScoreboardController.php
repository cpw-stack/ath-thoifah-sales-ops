<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Visit;
use App\Models\Order;
use App\Models\Collection;
use App\Models\OrderItem;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreboardController extends Controller
{
    public function index()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $period = now()->format('Y-m');

        // 1. Ranking Salesmen (Bulan Ini) - Compatible dengan MySQL Strict Mode
        $salesmen = Employee::whereHas('user', function($q) {
                $q->role('salesman');
            })
            ->with(['orders' => function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                  ->where('status', '!=', 'cancelled')
                  ->selectRaw('employee_id, SUM(total_amount) as total_sales')
                  ->groupBy('employee_id');
            }])
            ->with('target', 'user')
            ->get()
            ->map(function($emp) {
                $emp->total_sales = $emp->orders->isNotEmpty() ? $emp->orders->first()->total_sales : 0;
                return $emp;
            })
            ->sortByDesc('total_sales')
            ->take(10)
            ->values(); // Reset keys agar berurutan (0, 1, 2, 3, 4, 5)

        $salesmen->each(function($s) {
            $s->total_sales = $s->total_sales ?? 0;
        });

        $top3 = $salesmen->take(3)->values(); // Reset keys (0, 1, 2)
        $rest = $salesmen->slice(3)->values(); // Reset keys (0, 1, 2, dst)
        $maxSales = $salesmen->first() ? $salesmen->first()->total_sales : 1;

        // 2. Target Tim (Gauges)
        $targets = Target::where('period_month', $period)->get();
        $visitTarget = $targets->sum('visit_target') ?: 1;
        $orderTarget = $targets->sum('order_target') ?: 1;
        $salesTarget = $targets->sum('sales_target') ?: 1;
        $collectionTarget = $targets->sum('collection_target') ?: 1;

        $visitActual = Visit::whereMonth('check_in_at', now()->month)->count();
        $orderActual = Order::whereMonth('created_at', now()->month)->where('status', '!=', 'cancelled')->count();
        $salesActual = Order::whereMonth('created_at', now()->month)->where('status', '!=', 'cancelled')->sum('total_amount');
        $collectionActual = Collection::whereMonth('payment_date', now()->month)->where('status', 'verified')->sum('amount');

        $gauges = [
            'visit' => ['pct' => min(100, round(($visitActual / $visitTarget) * 100)), 'title' => 'Kunjungan'],
            'order' => ['pct' => min(100, round(($orderActual / $orderTarget) * 100)), 'title' => 'Order'],
            'sales' => ['pct' => min(100, round(($salesActual / $salesTarget) * 100)), 'title' => 'Sales Value'],
            'collection' => ['pct' => min(100, round(($collectionActual / $collectionTarget) * 100)), 'title' => 'Collection'],
        ];

        foreach ($gauges as $key => $g) {
            $gauges[$key]['offset'] = 477.5 * (1 - ($g['pct'] / 100));
            $gauges[$key]['color'] = $g['pct'] >= 70 ? '#3EA579' : ($g['pct'] >= 50 ? '#E8622C' : '#E14F3A');
        }

        // 3. Top Performer
        $topPerformer = $salesmen->first();
        $topPerformerPct = 0;
        if ($topPerformer && $topPerformer->target && $topPerformer->target->sales_target > 0) {
            $topPerformerPct = min(999, round(($topPerformer->total_sales / $topPerformer->target->sales_target) * 100));
        }

        // 4. PRODUK TERLARIS (Bulan Ini)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('order', function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                  ->where('status', '!=', 'cancelled');
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
            
        $maxQty = $topProducts->first() ? $topProducts->first()->total_qty : 1;

        // 5. Ticker Activities
        $activities = collect();
        $visits = Visit::with('employee', 'customer')->latest()->take(3)->get();
        foreach($visits as $v) {
            if($v->employee && $v->customer)
                $activities->push(['text' => "✅ <b>{$v->employee->full_name}</b> check-in di {$v->customer->name}"]);
        }
        $orders = Order::with('employee', 'customer')->latest()->take(3)->get();
        foreach($orders as $o) {
            if($o->employee && $o->customer)
                $activities->push(['text' => "🧾 <b>{$o->employee->full_name}</b> membuat order Rp " . number_format($o->total_amount,0,',','.') . " di {$o->customer->name}"]);
        }
        $collections = Collection::with('employee', 'receivable.customer')->where('status','verified')->latest()->take(3)->get();
        foreach($collections as $c) {
            if($c->employee && $c->receivable && $c->receivable->customer)
                $activities->push(['text' => "💵 <b>{$c->employee->full_name}</b> menagih Rp " . number_format($c->amount,0,',','.') . " dari {$c->receivable->customer->name}"]);
        }
        $tickerItems = $activities->take(5);

        // Format data untuk grafik bar (Target vs Actual)
        $metricBars = [
            ['label' => 'Kunjungan', 'actual' => $visitActual, 'target' => $visitTarget],
            ['label' => 'Order', 'actual' => $orderActual, 'target' => $orderTarget],
            ['label' => 'Sales (Jt)', 'actual' => round($salesActual / 1000000, 1), 'target' => round($salesTarget / 1000000, 1)],
            ['label' => 'Collection (Jt)', 'actual' => round($collectionActual / 1000000, 1), 'target' => round($collectionTarget / 1000000, 1)],
        ];
        
        foreach ($metricBars as &$mb) {
            $mb['pct'] = $mb['target'] > 0 ? min(100, round(($mb['actual'] / $mb['target']) * 100)) : 0;
        }

        return view('public.scoreboard', compact('top3', 'rest', 'maxSales', 'gauges', 'topPerformer', 'topPerformerPct', 'tickerItems', 'period', 'topProducts', 'maxQty', 'metricBars'));
    }
}