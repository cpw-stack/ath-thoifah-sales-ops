<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Employee;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index()
    {
        // Ambil semua salesman, beserta target mereka untuk bulan ini
        $employees = Employee::whereHas('user', fn($q) => $q->role('salesman'))
            ->with(['targets' => function($query) {
                $query->where('period_month', now()->format('Y-m'));
            }])
            ->get();

        return view('admin.targets.index', compact('employees'));
    }

    public function create(Request $request)
    {
        $employees = Employee::whereHas('user', fn($q) => $q->role('salesman'))->get();
        // Ambil employee_id dari URL (?employee_id=X) agar langsung terpilih di form
        $selectedEmployee = $request->input('employee_id'); 
        return view('admin.targets.create', compact('employees', 'selectedEmployee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_month' => 'required|date_format:Y-m',
            'visit_target' => 'required|integer|min:0',
            'order_target' => 'required|integer|min:0',
            'sales_target' => 'required|numeric|min:0',
            'collection_target' => 'required|numeric|min:0',
        ]);

        // Gunakan updateOrCreate agar tidak ada duplikat target untuk salesman & periode yang sama
        Target::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'period_month' => $validated['period_month']],
            $validated
        );

        return redirect()->route('admin.targets.index')->with('success', 'Target berhasil ditetapkan.');
    }

    public function edit(Target $target)
    {
        $employees = Employee::whereHas('user', fn($q) => $q->role('salesman'))->get();
        return view('admin.targets.edit', compact('target', 'employees'));
    }

    public function update(Request $request, Target $target)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_month' => 'required|date_format:Y-m',
            'visit_target' => 'required|integer|min:0',
            'order_target' => 'required|integer|min:0',
            'sales_target' => 'required|numeric|min:0',
            'collection_target' => 'required|numeric|min:0',
        ]);

        $target->update($validated);
        return redirect()->route('admin.targets.index')->with('success', 'Target berhasil diperbarui.');
    }

    public function destroy(Target $target)
    {
        $target->delete();
        return redirect()->route('admin.targets.index')->with('success', 'Target dihapus.');
    }
}