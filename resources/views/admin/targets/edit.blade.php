@extends('layouts.app')
@section('title', 'Edit Target')
@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="display text-2xl mb-6">Edit Target</h2>
    <div class="card p-6">
        <form action="{{ route('admin.targets.update', $target) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Salesman</label><select name="employee_id" class="w-full" required>@foreach($employees as $emp)<option value="{{ $emp->id }}" {{ $target->employee_id == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>@endforeach</select></div>
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Periode</label><input type="month" name="period_month" value="{{ old('period_month', $target->period_month) }}" class="w-full" required></div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Visit</label><input type="number" name="visit_target" value="{{ old('visit_target', $target->visit_target) }}" class="w-full" required></div>
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Order</label><input type="number" name="order_target" value="{{ old('order_target', $target->order_target) }}" class="w-full" required></div>
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Sales (Rp)</label><input type="number" name="sales_target" value="{{ old('sales_target', $target->sales_target) }}" class="w-full" required></div>
                <div><label class="block text-xs mb-1" style="color:var(--slate);">Target Collection (Rp)</label><input type="number" name="collection_target" value="{{ old('collection_target', $target->collection_target) }}" class="w-full" required></div>
            </div>
            <div class="flex justify-end"><a href="{{ route('admin.targets.index') }}" class="btn-outline mr-2">Batal</a><button type="submit" class="btn">Update</button></div>
        </form>
    </div>
</div>
@endsection