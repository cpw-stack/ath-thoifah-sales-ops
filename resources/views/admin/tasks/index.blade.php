@extends('layouts.app')
@section('title', 'Task Management')
@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Task Management</h2>
        <p class="text-sm" style="color:var(--slate);">Kelola tugas dan penagihan khusus untuk salesman.</p>
    </div>
    <a href="{{ route('admin.tasks.create') }}" class="btn w-full sm:w-auto text-center">+ Buat Task</a>
</div>

@if (session('success')) 
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">✅ {{ session('success') }}</div> 
@endif

<!-- HEADER FILTER (Tampil di semua layar) -->
<div class="card p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="condensed w-full md:w-auto">Daftar Tugas</div>
    <form method="GET" action="{{ route('admin.tasks.index') }}" class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
        <select name="status" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
        </select>

        <select name="employee_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Salesman</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
            @endforeach
        </select>

        <select name="customer_id" onchange="this.form.submit()" class="text-xs p-1.5 rounded border w-full sm:w-auto" style="border-color:var(--border);">
            <option value="">Semua Toko Mitra</option>
            @foreach($customers as $cust)
                <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- 1. TAMPILAN DESKTOP (Tabel) - Hanya muncul di layar besar -->
<div class="card overflow-hidden hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Judul</th>
                    <th class="p-4">Salesman</th>
                    <th class="p-4">Toko</th>
                    <th class="p-4">Prioritas</th>
                    <th class="p-4">Deadline</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $t)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 text-sm font-semibold">
                        {{ $t->title }}
                        @if($t->attachment)
                            <a href="{{ asset('storage/' . $t->attachment) }}" target="_blank" class="text-xs text-red-600 font-semibold flex items-center gap-1 mt-1 hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="9" y1="15" x2="15" y2="15"></line>
                                </svg>
                                Lihat Invoice
                            </a>
                        @endif
                    </td>
                    <td class="p-4 text-sm">{{ $t->employee->full_name ?? '-' }}</td>
                    <td class="p-4 text-sm">{{ $t->customer->name ?? '-' }}</td>
                    <td class="p-4">
                        <span class="badge {{ $t->priority == 'high' ? 'badge-red' : ($t->priority == 'medium' ? 'badge-amber' : 'badge-slate') }}">{{ $t->priority }}</span>
                    </td>
                    <td class="p-4 mono text-xs">{{ $t->due_date->format('d M Y') }}</td>
                    <td class="p-4">
                        @if($t->status == 'pending')
                            <span class="badge badge-amber">Pending</span>
                        @elseif($t->status == 'completed')
                            <span class="badge badge-green">Completed</span>
                        @else
                            <span class="badge badge-red">Overdue</span>
                        @endif
                    </td>
                    <td class="p-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.tasks.edit', $t) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                        <form action="{{ route('admin.tasks.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tugas ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs font-bold">Hapus</button>
                        </form>
                    </td>            
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center" style="color:var(--slate);">Belum ada tugas sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t" style="border-color:var(--border);">
        {{ $tasks->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($tasks as $t)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1 mr-2">
                <div class="font-semibold text-base" style="color:var(--ink);">{{ $t->title }}</div>
                <div class="text-xs mt-1" style="color:var(--slate);">Toko: {{ $t->customer->name ?? '-' }}</div>
            </div>
            <span class="badge {{ $t->priority == 'high' ? 'badge-red' : ($t->priority == 'medium' ? 'badge-amber' : 'badge-slate') }}">{{ $t->priority }}</span>
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Salesman:</span>
                <span class="font-semibold text-right">{{ $t->employee->full_name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--slate);">Deadline:</span>
                <span class="font-semibold text-right mono">{{ $t->due_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span style="color:var(--slate);">Status:</span>
                @if($t->status == 'pending')
                    <span class="badge badge-amber">Pending</span>
                @elseif($t->status == 'completed')
                    <span class="badge badge-green">Completed</span>
                @else
                    <span class="badge badge-red">Overdue</span>
                @endif
            </div>
            @if($t->attachment)
            <div class="pt-2">
                <a href="{{ asset('storage/' . $t->attachment) }}" target="_blank" class="text-xs text-red-600 font-semibold flex items-center gap-1 hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="9" y1="15" x2="15" y2="15"></line>
                    </svg>
                    Lihat Lampiran Invoice
                </a>
            </div>
            @endif
        </div>

        <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
            <a href="{{ route('admin.tasks.edit', $t) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Edit</a>
            <form action="{{ route('admin.tasks.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tugas ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Belum ada tugas sesuai filter.
    </div>
    @endforelse
    
    <!-- Pagination Mobile -->
    @if($tasks->hasPages())
    <div class="mt-4">
        {{ $tasks->appends(['status' => request('status'), 'employee_id' => request('employee_id'), 'customer_id' => request('customer_id')])->links() }}
    </div>
    @endif
</div>
@endsection