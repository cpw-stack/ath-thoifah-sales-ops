<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Receivable;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->input('status');
        $employeeFilter = $request->input('employee_id');
        $customerFilter = $request->input('customer_id');
        
        $collections = Collection::with('receivable.customer', 'employee')
            ->when($statusFilter, function($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            }, function($query) {
                // Jika tidak ada filter (default), urutkan pending di atas
                $query->orderByRaw("FIELD(status, 'pending', 'rejected', 'verified')");
            })
            ->when($employeeFilter, function($query) use ($employeeFilter) {
                $query->where('employee_id', $employeeFilter);
            })
            ->when($customerFilter, function($query) use ($customerFilter) {
                // Filter berdasarkan customer_id melalui relasi receivable
                $query->whereHas('receivable', function($q) use ($customerFilter) {
                    $q->where('customer_id', $customerFilter);
                });
            })
            ->latest()
            ->paginate(15);
            
        $overdues = Receivable::where('status', 'overdue')
            ->orWhere('status', 'partial')
            ->where('due_date', '<', today())
            ->with('customer')
            ->get();
            
        // Kirim data untuk dropdown filter
        $employees = \App\Models\Employee::where('status', 'active')->get();
        $customers = \App\Models\Customer::where('status', 'active')->get();
        
        return view('admin.collections.index', compact('collections', 'overdues', 'employees', 'customers'));
    }

    public function verify(Request $request, Collection $collection)
    {
        $request->validate(['action' => 'required|in:verify,reject']);
        
        if ($request->action == 'verify') {
            $collection->status = 'verified';
            $collection->save();
            return back()->with('success', 'Pembayaran berhasil diverifikasi.');
        } else {
            // Jika ditolak, kembalikan jumlah piutang ke semula
            $receivable = $collection->receivable;
            $receivable->paid_amount -= $collection->amount;
            
            if ($receivable->paid_amount <= 0) {
                $receivable->paid_amount = 0;
                $receivable->status = $receivable->due_date < today() ? 'overdue' : 'unpaid';
            } else {
                $receivable->status = 'partial';
            }
            $receivable->save();
            
            $collection->status = 'rejected';
            $collection->save();
            return back()->with('success', 'Pembayaran ditolak, piutang dikembalikan.');
        }
    }
}