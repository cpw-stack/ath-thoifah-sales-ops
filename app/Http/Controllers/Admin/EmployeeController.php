<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\SalesArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $employees = Employee::with('user', 'salesArea')
            ->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('employee_code', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->latest()
            ->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $areas = SalesArea::all();
        $supervisors = Employee::whereHas('user', fn($q) => $q->role('supervisor'))->get();
        return view('admin.employees.create', compact('areas', 'supervisors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'whatsapp' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            'employee_code' => 'required|unique:employees,employee_code',
            'phone_number' => 'nullable|string|max:15',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'id_card_number' => 'nullable|string|max:30',
            'sales_area_id' => 'nullable|exists:sales_areas,id',
            'supervisor_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive',
            'type' => 'required|in:online,offline', // TAMBAHAN
        ]);

        // Upload Foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user_photos', 'public');
        }

        // Buat User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'whatsapp' => $validated['whatsapp'] ?? null,
            'address' => $validated['address'] ?? null,
            'photo' => $photoPath,
        ]);

        // Assign Role Salesman default
        $user->assignRole('salesman');

        // Buat Employee
        Employee::create([
            'user_id' => $user->id,
            'employee_code' => $validated['employee_code'],
            'full_name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'id_card_number' => $validated['id_card_number'] ?? null,
            'sales_area_id' => $validated['sales_area_id'] ?? null,
            'supervisor_id' => $validated['supervisor_id'] ?? null,
            'status' => $validated['status'],
            'type' => $validated['type'], // TAMBAHAN
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Data salesman & akun berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $areas = SalesArea::all();
        $supervisors = Employee::whereHas('user', fn($q) => $q->role('supervisor'))->get();
        $employee->load('user');
        return view('admin.employees.edit', compact('employee', 'areas', 'supervisors'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'password' => 'nullable|string|min:8',
            'whatsapp' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            'employee_code' => 'required|unique:employees,employee_code,' . $employee->id,
            'phone_number' => 'nullable|string|max:15',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'id_card_number' => 'nullable|string|max:30',
            'sales_area_id' => 'nullable|exists:sales_areas,id',
            'supervisor_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive',
            'type' => 'required|in:online,offline', // TAMBAHAN
        ]);

        $user = $employee->user;
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $userData['photo'] = $request->file('photo')->store('user_photos', 'public');
        }

        $user->update($userData);

        $employee->update([
            'employee_code' => $validated['employee_code'],
            'full_name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'id_card_number' => $validated['id_card_number'] ?? null,
            'sales_area_id' => $validated['sales_area_id'] ?? null,
            'supervisor_id' => $validated['supervisor_id'] ?? null,
            'status' => $validated['status'],
            'type' => $validated['type'], // TAMBAHAN
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Data salesman berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Data salesman berhasil dihapus.');
    }

    public function show(Employee $employee)
    {
        $employee->load('user', 'salesArea', 'supervisor');
        
        $today = today();
        $period = $today->format('Y-m');

        // Ambil target bulan ini
        $target = $employee->target()->where('period_month', $period)->first();

        // Statistik Real Bulan Ini
        $stats = [
            'visits' => \App\Models\Visit::where('employee_id', $employee->id)->whereMonth('check_in_at', $today->month)->count(),
            'orders' => \App\Models\Order::where('employee_id', $employee->id)->whereMonth('created_at', $today->month)->count(),
            'sales' => \App\Models\Order::where('employee_id', $employee->id)->whereMonth('created_at', $today->month)->sum('total_amount'),
            'collections' => \App\Models\Collection::where('employee_id', $employee->id)->whereMonth('payment_date', $today->month)->sum('amount'),
        ];

        // Hitung Persentase Target
        $metrics = [
            'visit' => $target ? min(100, round(($stats['visits'] / $target->visit_target) * 100)) : 0,
            'order' => $target ? min(100, round(($stats['orders'] / $target->order_target) * 100)) : 0,
            'sales' => $target ? min(100, round(($stats['sales'] / $target->sales_target) * 100)) : 0,
            'collection' => $target ? min(100, round(($stats['collections'] / $target->collection_target) * 100)) : 0,
        ];

        // Riwayat SEMUA Transaksi Bulan Ini
        $monthlyOrders = \App\Models\Order::with('customer')
            ->where('employee_id', $employee->id)
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->latest()->get();

        $monthlyVisits = \App\Models\Visit::with('customer')
            ->where('employee_id', $employee->id)
            ->whereMonth('check_in_at', $today->month)
            ->whereYear('check_in_at', $today->year)
            ->latest()->get();

        return view('admin.employees.show', compact('employee', 'target', 'stats', 'metrics', 'monthlyOrders', 'monthlyVisits'));

        }

    public function downloadPdf(Employee $employee)
    {
        $employee->load('user', 'salesArea', 'supervisor');
        
        $today = today();
        $period = $today->format('Y-m');

        $target = $employee->target()->where('period_month', $period)->first();

        $stats = [
            'visits' => \App\Models\Visit::where('employee_id', $employee->id)->whereMonth('check_in_at', $today->month)->count(),
            'orders' => \App\Models\Order::where('employee_id', $employee->id)->whereMonth('created_at', $today->month)->count(),
            'sales' => \App\Models\Order::where('employee_id', $employee->id)->whereMonth('created_at', $today->month)->sum('total_amount'),
            'collections' => \App\Models\Collection::where('employee_id', $employee->id)->whereMonth('payment_date', $today->month)->sum('amount'),
        ];

        $metrics = [
            'visit' => $target ? min(100, round(($stats['visits'] / $target->visit_target) * 100)) : 0,
            'order' => $target ? min(100, round(($stats['orders'] / $target->order_target) * 100)) : 0,
            'sales' => $target ? min(100, round(($stats['sales'] / $target->sales_target) * 100)) : 0,
            'collection' => $target ? min(100, round(($stats['collections'] / $target->collection_target) * 100)) : 0,
        ];

        $monthlyOrders = \App\Models\Order::with('customer')
            ->where('employee_id', $employee->id)
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->latest()->get();

        $monthlyVisits = \App\Models\Visit::with('customer')
            ->where('employee_id', $employee->id)
            ->whereMonth('check_in_at', $today->month)
            ->whereYear('check_in_at', $today->year)
            ->latest()->get();

        // Load view khusus untuk PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.employees.pdf', compact('employee', 'target', 'stats', 'metrics', 'monthlyOrders', 'monthlyVisits'));
        
        // Set paper size ke A4 dan orientasi Portrait
        $pdf->setPaper('A4', 'portrait');

        // Download file PDF
        return $pdf->download('Laporan-Performa-' . $employee->full_name . '-' . $today->format('F-Y') . '.pdf');
    }

}