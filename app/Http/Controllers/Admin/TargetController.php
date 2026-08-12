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
        $targets = Target::with('employee')->latest()->paginate(10);
        return view('admin.targets.index', compact('targets'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.targets.create', compact('employees'));
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

        Target::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'period_month' => $validated['period_month']],
            $validated
        );

        return redirect()->route('admin.targets.index')->with('success', 'Target berhasil ditetapkan.');
    }

    public function edit(Target $target)
    {
        $employees = Employee::where('status', 'active')->get();
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