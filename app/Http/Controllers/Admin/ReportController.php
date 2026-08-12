<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Visit;
use App\Models\Order;
use App\Models\Collection;
use App\Models\Target;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', now()->format('Y-m'));
        
        $employees = Employee::whereHas('user', function($q) {
            $q->role('salesman');
        })->get();

        $reportData = [];

        foreach ($employees as $emp) {
            $target = $emp->target()->where('period_month', $period)->first();
            
            $actualVisits = Visit::where('employee_id', $emp->id)
                ->whereYear('check_in_at', substr($period, 0, 4))
                ->whereMonth('check_in_at', substr($period, 5, 2))
                ->count();

            $actualOrders = Order::where('employee_id', $emp->id)
                ->whereYear('created_at', substr($period, 0, 4))
                ->whereMonth('created_at', substr($period, 5, 2))
                ->count();

            $actualSales = Order::where('employee_id', $emp->id)
                ->whereYear('created_at', substr($period, 0, 4))
                ->whereMonth('created_at', substr($period, 5, 2))
                ->sum('total_amount');

            $actualCollections = Collection::where('employee_id', $emp->id)
                ->whereYear('payment_date', substr($period, 0, 4))
                ->whereMonth('payment_date', substr($period, 5, 2))
                ->sum('amount');

            $reportData[] = [
                'employee' => $emp,
                'target' => $target,
                'visits' => ['target' => $target->visit_target ?? 0, 'actual' => $actualVisits],
                'orders' => ['target' => $target->order_target ?? 0, 'actual' => $actualOrders],
                'sales' => ['target' => $target->sales_target ?? 0, 'actual' => $actualSales],
                'collections' => ['target' => $target->collection_target ?? 0, 'actual' => $actualCollections],
            ];
        }

        return view('admin.reports.index', compact('reportData', 'period'));
    }
}