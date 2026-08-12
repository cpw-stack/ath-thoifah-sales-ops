@extends('layouts.app')

@section('title', 'Tugaskan Kunjungan')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="display text-2xl mb-6">Tugaskan Kunjungan Baru</h2>
    
    <div class="card p-6">
        <form action="{{ route('admin.visit-plans.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-xs mb-1" style="color:var(--slate);">Salesman</label>
                <select id="select-salesman" name="employee_id" class="w-full" required>
                    <option value="">Pilih Salesman...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs mb-1" style="color:var(--slate);">Toko Mitra (Outlet)</label>
                <select id="select-customer" name="customer_id" class="w-full" required>
                    <option value="">Pilih Toko...</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }} ({{ $cust->customer_code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs mb-1" style="color:var(--slate);">Tanggal Kunjungan</label>
                <input type="date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" class="w-full" required>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.visit-plans.index') }}" class="btn-outline mr-2">Batal</a>
                <button type="submit" class="btn">Tugaskan</button>
            </div>
        </form>
    </div>
</div>

<!-- Tom Select CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#select-salesman", {
        create: false,
        placeholder: "Ketik nama salesman...",
        sortField: { field: "text", direction: "asc" }
    });
    
    new TomSelect("#select-customer", {
        create: false,
        placeholder: "Ketik nama toko...",
        sortField: { field: "text", direction: "asc" }
    });
</script>
@endsection