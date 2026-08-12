@extends('layouts.app')

@section('title', 'Target Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="display text-2xl">Target Salesman</h2>
    <a href="{{ route('admin.targets.create') }}" class="btn">+ Tetapkan Target</a>
</div>
@if (session('success')) <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green);">{{ session('success') }}</div> @endif
<div class="card overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px]">
        <thead><tr class="bg-gray-50 border-b" style="border-color:var(--border);">
            <th class="p-4">Salesman</th><th class="p-4">Periode</th><th class="p-4">Visit</th><th class="p-4">Order</th><th class="p-4">Sales (Rp)</th><th class="p-4">Collection (Rp)</th><th class="p-4">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($targets as $t)
            <tr class="border-b" style="border-color:var(--border);">
                <td class="p-4 font-semibold">{{ $t->employee->full_name }}</td>
                <td class="p-4 mono">{{ $t->period_month }}</td>
                <td class="p-4">{{ $t->visit_target }}</td>
                <td class="p-4">{{ $t->order_target }}</td>
                <td class="p-4 mono">{{ number_format($t->sales_target, 0, ',', '.') }}</td>
                <td class="p-4 mono">{{ number_format($t->collection_target, 0, ',', '.') }}</td>
                <td class="p-4">
                    <a href="{{ route('admin.targets.edit', $t) }}" class="btn-outline text-xs" style="padding:6px 10px;">Edit</a>
                    <form action="{{ route('admin.targets.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus target?')">@csrf @method('DELETE') <button class="text-red-600 text-xs font-bold">Hapus</button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="p-8 text-center" style="color:var(--slate);">Belum ada target ditetapkan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection