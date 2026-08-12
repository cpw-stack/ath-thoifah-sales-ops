@extends('layouts.app')

@section('title', 'Visit Planning')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Visit Planning</h2>
        <p class="text-sm" style="color:var(--slate);">Tugaskan salesman untuk mengunjungi mitra tertentu.</p>
    </div>
    <a href="{{ route('admin.visit-plans.create') }}" class="btn">+ Tugaskan Kunjungan</a>
</div>

@if (session('success'))
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">✅ {{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="card p-4 mb-4" style="background:var(--red-soft); color:var(--red); border:1px solid var(--red);">⚠️ {{ session('error') }}</div>
@endif

<div class="card overflow-hidden">
    <!-- Header: Filter Data -->
    <div class="p-4 border-b flex flex-col md:flex-row justify-between items-center gap-4" style="border-color:var(--border); background:var(--paper-dim);">
        <div class="condensed">Daftar Jadwal Kunjungan</div>
        <form method="GET" action="{{ route('admin.visit-plans.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select name="status" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full md:w-auto" style="border-color:var(--border);">
                <option value="">Semua Status</option>
                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <select name="employee_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full md:w-auto" style="border-color:var(--border);">
                <option value="">Semua Salesman</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                @endforeach
            </select>

            <select name="customer_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full md:w-auto" style="border-color:var(--border);">
                <option value="">Semua Toko Mitra</option>
                @foreach($customers as $cust)
                    <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Salesman</th>
                    <th class="p-4">Toko Mitra</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plans as $plan)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 mono text-xs">{{ \Carbon\Carbon::parse($plan->visit_date)->format('d M Y') }}</td>
                    <td class="p-4 text-sm font-semibold">{{ $plan->employee->full_name ?? '-' }}</td>
                    <td class="p-4 text-sm">{{ $plan->customer->name ?? '-' }}</td>
                    <td class="p-4">
                        @if($plan->status == 'completed')
                            <span class="badge badge-green">Selesai/Visit</span>
                        @else
                            <span class="badge badge-amber">Planned</span>
                        @endif
                    </td>
                    <td class="p-4 text-right whitespace-nowrap">
                        @if($plan->status !== 'completed')
                            <a href="{{ route('admin.visit-plans.edit', $plan) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                            <form action="{{ route('admin.visit-plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan jadwal ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-xs font-bold">Batalkan</button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center" style="color:var(--slate);">Belum ada jadwal kunjungan sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">
    {{ $plans->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
</div>
@endsection