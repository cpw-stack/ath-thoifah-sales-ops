@extends('layouts.app')
@section('title', 'Tetapkan Target')
@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="display text-2xl mb-6">Tetapkan Target Bulanan</h2>
    <div class="card p-6">
        <form action="{{ route('admin.targets.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Salesman</label>
                    <select name="employee_id" class="w-full" required>
                        @foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Periode (Tahun-Bulan)</label>
                    <input type="month" name="period_month" value="{{ old('period_month', now()->format('Y-m')) }}" class="w-full" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Visit</label><input type="number" name="visit_target" value="90" class="w-full" required></div>
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Order</label><input type="number" name="order_target" value="50" class="w-full" required></div>
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Sales (Rp)</label><input type="number" name="sales_target" value="5000000" class="w-full" required></div>
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Collection (Rp)</label><input type="number" name="collection_target" value="2500000" class="w-full" required></div>
            </div>
            <div class="flex justify-end"><a href="{{ route('admin.targets.index') }}" class="btn-outline mr-2">Batal</a><button type="submit" class="btn">Simpan</button></div>
        </form>
    </div>
</div>
@endsection