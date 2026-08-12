<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->input('status');
        $employeeFilter = $request->input('employee_id');
        $customerFilter = $request->input('customer_id');
        
        $orders = Order::with('customer', 'employee', 'items')
            ->when($statusFilter, function($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($employeeFilter, function($query) use ($employeeFilter) {
                $query->where('employee_id', $employeeFilter);
            })
            ->when($customerFilter, function($query) use ($customerFilter) {
                $query->where('customer_id', $customerFilter);
            })
            ->orderByRaw("FIELD(status, 'pending', 'processed', 'delivered', 'cancelled')")
            ->latest()
            ->paginate(15);
            
        // Kirim data untuk dropdown filter
        $employees = \App\Models\Employee::where('status', 'active')->get();
        $customers = \App\Models\Customer::where('status', 'active')->get();

        return view('admin.orders.index', compact('orders', 'employees', 'customers'));
    }

    public function show(Order $order)
    {
        $order->load('customer', 'employee', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processed,delivered,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Status order diperbarui.');
    }
}