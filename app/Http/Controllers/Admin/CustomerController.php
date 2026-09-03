<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerStockDiscount;
use App\Models\Order;
use App\Models\Receivable;
use App\Models\MitraStock;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomerImport;
use App\Exports\CustomerTemplateExport;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $customers = Customer::where('name', 'like', "%{$search}%")
            ->orWhere('customer_code', 'like', "%{$search}%")
            ->latest()->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'required|unique:customers,customer_code',
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'credit_limit' => 'required|integer|min:0',
            'credit_terms_days' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Data mitra berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_code' => 'required|unique:customers,customer_code,' . $customer->id,
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'credit_limit' => 'required|integer|min:0',
            'credit_terms_days' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Data mitra berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Data mitra berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new CustomerImport, $request->file('file'));
        return back()->with('success', 'Data mitra berhasil diimpor!');
    }

    public function template()
    {
        return Excel::download(new CustomerTemplateExport, 'Template_Import_Mitra.xlsx');
    }

    // =========================================================
    // FITUR BARU: Detail Mitra & Manajemen Diskon
    // =========================================================

    public function show(Customer $customer)
    {
        // Ambil data order beserta item dan status pembayaran
        $orders = Order::with('items.product', 'employee')
            ->where('customer_id', $customer->id)
            ->latest()->paginate(10);

        // Ambil data piutang dan riwayat pembayarannya
        $receivables = Receivable::with('collections.employee')
            ->where('customer_id', $customer->id)
            ->latest()->get();

        // Ambil data diskon aktif/pending
        $discount = CustomerStockDiscount::where('customer_id', $customer->id)->latest()->first();

        // AMBIL DATA STOK PRODUK DI MITRA
        $stocks = MitraStock::with('product')
            ->where('customer_id', $customer->id)
            ->where(function($q) {
                $q->where('qty_konsinyasi', '>', 0)
                  ->orWhere('qty_lunas', '>', 0)
                  ->orWhere('qty_piutang', '>', 0);
            })->get();

        // STATISTIK PAYMENT TYPE UNTUK PIE CHART
        $paymentStats = Order::where('customer_id', $customer->id)
            ->select('payment_type', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_type')
            ->pluck('total', 'payment_type');

        // STATISTIK PRODUK TERBELI (TOP 5)
        $topProducts = OrderItem::whereHas('order', function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('admin.customers.show', compact('customer', 'orders', 'receivables', 'discount', 'stocks', 'paymentStats', 'topProducts'));
    }

    public function approveDiscount(Customer $customer, CustomerStockDiscount $discount)
    {
        // Set diskon lama menjadi tidak aktif jika ada
        CustomerStockDiscount::where('customer_id', $customer->id)
            ->where('id', '!=', $discount->id)
            ->update(['is_active' => false]);

        $discount->update([
            'is_approved' => true,
            'is_active' => true,
        ]);

        return back()->with('success', 'Diskon untuk mitra ' . $customer->name . ' telah disetujui.');
    }

    public function rejectDiscount(Customer $customer, CustomerStockDiscount $discount)
    {
        $discount->update(['is_active' => false]);
        return back()->with('error', 'Pengajuan diskon telah ditolak.');
    }

    public function storeDiscount(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'discount_value' => 'required|numeric|min:0|max:100',
        ]);

        // Gunakan updateOrCreate: Jika ada, update. Jika belum, buat.
        // Admin yang set langsung diset is_approved = true
        CustomerStockDiscount::updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'type' => 'percentage',
                'value' => $validated['discount_value'],
                'discount_value' => $validated['discount_value'],
                'is_active' => true,
                'is_approved' => true, 
            ]
        );

        return back()->with('success', 'Diskon untuk mitra ' . $customer->name . ' berhasil disimpan & disetujui.');
    }

    public function destroyDiscount(Customer $customer, CustomerStockDiscount $discount)
    {
        $discount->delete();
        return back()->with('success', 'Diskon untuk mitra ' . $customer->name . ' berhasil dihapus permanen.');
    }
}