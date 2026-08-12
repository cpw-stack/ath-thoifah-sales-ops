<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->input('status');
        $employeeFilter = $request->input('employee_id');
        $customerFilter = $request->input('customer_id');

        $tasks = Task::with('employee', 'customer')
            ->when($statusFilter, function($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($employeeFilter, function($query) use ($employeeFilter) {
                $query->where('employee_id', $employeeFilter);
            })
            ->when($customerFilter, function($query) use ($customerFilter) {
                $query->where('customer_id', $customerFilter);
            })
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        // Kirim data untuk dropdown filter
        $employees = \App\Models\Employee::where('status', 'active')->get();
        $customers = \App\Models\Customer::where('status', 'active')->get();

        return view('admin.tasks.index', compact('tasks', 'employees', 'customers'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();
        return view('admin.tasks.create', compact('employees', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'customer_id' => 'nullable|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Validasi PDF/Gambar
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('task_attachments', 'public');
        }

        $validated['status'] = 'pending';
        Task::create($validated);
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(Task $task)
    {
        $employees = Employee::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();
        return view('admin.tasks.edit', compact('task', 'employees', 'customers'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'customer_id' => 'nullable|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,completed,overdue'
        ]);

        if ($request->hasFile('attachment')) {
            if ($task->attachment) Storage::disk('public')->delete($task->attachment);
            $validated['attachment'] = $request->file('attachment')->store('task_attachments', 'public');
        }

        $task->update($validated);
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas dihapus.');
    }
}