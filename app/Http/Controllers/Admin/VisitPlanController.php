<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitPlan;
use App\Models\Employee;
use App\Models\Customer;
use Illuminate\Http\Request;

class VisitPlanController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->input('status');
        $employeeFilter = $request->input('employee_id');
        $customerFilter = $request->input('customer_id');

        $plans = VisitPlan::with('employee', 'customer')
            ->when($statusFilter, function($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($employeeFilter, function($query) use ($employeeFilter) {
                $query->where('employee_id', $employeeFilter);
            })
            ->when($customerFilter, function($query) use ($customerFilter) {
                $query->where('customer_id', $customerFilter);
            })
            ->orderByRaw("FIELD(status, 'planned', 'skipped', 'completed')")
            ->orderBy('visit_date', 'asc')
            ->paginate(15);

        // Kirim data untuk dropdown filter
        $employees = Employee::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();

        return view('admin.visit-plans.index', compact('plans', 'employees', 'customers'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();
        return view('admin.visit-plans.create', compact('employees', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'customer_id' => 'required|exists:customers,id',
            'visit_date' => 'required|date',
        ]);

        // Cek apakah sudah ada jadwal yang sama untuk menghindari duplikasi
        $exists = VisitPlan::where('employee_id', $validated['employee_id'])
            ->where('customer_id', $validated['customer_id'])
            ->where('visit_date', $validated['visit_date'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Jadwal kunjungan untuk salesman & toko ini sudah ada pada tanggal tersebut.');
        }

        VisitPlan::create($validated);
        return redirect()->route('admin.visit-plans.index')->with('success', 'Jadwal kunjungan berhasil ditugaskan.');
    }

    public function edit(VisitPlan $visitPlan)
    {
        $employees = Employee::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();
        return view('admin.visit-plans.edit', compact('visitPlan', 'employees', 'customers'));
    }

    public function update(Request $request, VisitPlan $visitPlan)
    {
        // Jangan izinkan edit jika kunjungan sudah selesai
        if ($visitPlan->status === 'completed') {
            return back()->with('error', 'Jadwal yang sudah selesai/visit tidak bisa diubah.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'customer_id' => 'required|exists:customers,id',
            'visit_date' => 'required|date',
        ]);

        $visitPlan->update($validated);
        return redirect()->route('admin.visit-plans.index')->with('success', 'Jadwal kunjungan berhasil diperbarui.');
    }

    public function destroy(VisitPlan $visitPlan)
    {
        // Hanya bisa dihapus jika belum di check-in
        if ($visitPlan->status === 'completed') {
            return back()->with('error', 'Jadwal yang sudah selesai/visit tidak bisa dihapus.');
        }
        
        $visitPlan->delete();
        return redirect()->route('admin.visit-plans.index')->with('success', 'Jadwal kunjungan dibatalkan.');
    }
}