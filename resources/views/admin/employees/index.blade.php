@extends('layouts.app')

@section('title', 'Salesman Management')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Salesman Management</h2>
        <p class="text-sm" style="color:var(--slate);">Kelola data karyawan lapangan, area penjualan, dan akun login.</p>
    </div>
    <a href="{{ route('admin.employees.create') }}" class="btn w-full sm:w-auto text-center">+ Tambah Salesman</a>
</div>

@if (session('success'))
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">
        ✅ {{ session('success') }}
    </div>
@endif

<!-- HEADER SEARCH & FILTER (Tampil di semua layar) -->
<div class="card p-4 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.employees.index') }}" class="relative w-full md:max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..." class="w-full pr-9">
        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--slate);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </form>
    <div class="flex items-center gap-2 text-xs" style="color:var(--slate);">
        <span>Total Salesman:</span>
        <span class="badge badge-slate">{{ $employees->total() }} Orang</span>
    </div>
</div>

<!-- 1. TAMPILAN DESKTOP (Tabel) - Hanya muncul di layar besar -->
<div class="card overflow-hidden hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[640px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4">Salesman</th>
                    <th class="p-4">Area Penjualan</th>
                    <th class="p-4">Kontak</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                <tr class="border-b hover:bg-gray-50" style="border-color:var(--border);">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="avatar w-10 h-10 bg-gray-200">
                                @if($employee->user && $employee->user->photo)
                                    <img src="{{ asset('storage/' . $employee->user->photo) }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="font-semibold text-sm" style="color:var(--ink);">{{ $employee->full_name }}</div>
                                <div class="text-xs mono" style="color:var(--slate);">{{ $employee->employee_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-sm">
                        @if($employee->salesArea) {{ $employee->salesArea->name }} @else <span class="text-gray-400">Belum diassign</span> @endif
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col gap-1 text-xs">
                            @if($employee->user && ($employee->user->whatsapp || $employee->phone_number))
                                @php
                                    $wa = $employee->user->whatsapp ?? $employee->phone_number;
                                    $waClean = preg_replace('/[^0-9]/', '', $wa);
                                    if (substr($waClean, 0, 1) === '0') { $waClean = '62' . substr($waClean, 1); }
                                @endphp
                                <a href="https://wa.me/{{ $waClean }}" target="_blank" class="flex items-center gap-1.5 font-medium hover:underline" style="color:var(--green);">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span class="truncate">{{ $wa }}</span>
                                </a>
                            @endif
                            @if($employee->user && $employee->user->email)
                                <a href="mailto:{{ $employee->user->email }}" class="flex items-center gap-1.5 hover:underline" style="color:var(--slate);">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span class="truncate">{{ $employee->user->email }}</span>
                                </a>
                            @endif
                            @if($employee->user && $employee->user->address)
                                <div class="flex items-start gap-1.5" style="color:var(--slate);">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $employee->user->address }}</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="p-4">
                        @if($employee->status == 'active')
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-slate">Inactive</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-outline text-xs" style="padding:6px 12px;">Edit</a>
                            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data salesman ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold p-2">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center" style="color:var(--slate);">Tidak ada data ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
<div class="md:hidden space-y-4">
    @forelse ($employees as $employee)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="avatar w-10 h-10 bg-gray-200">
                    @if($employee->user && $employee->user->photo)
                        <img src="{{ asset('storage/' . $employee->user->photo) }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="font-semibold text-sm" style="color:var(--ink);">{{ $employee->full_name }}</div>
                    <div class="text-xs mono" style="color:var(--slate);">{{ $employee->employee_code }}</div>
                </div>
            </div>
            @if($employee->status == 'active')
                <span class="badge badge-green">Active</span>
            @else
                <span class="badge badge-slate">Inactive</span>
            @endif
        </div>
        
        <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
            <div class="flex justify-between">
                <span style="color:var(--slate);">Area:</span>
                <span class="font-semibold text-right">@if($employee->salesArea) {{ $employee->salesArea->name }} @else Belum diassign @endif</span>
            </div>
            @if($employee->user && ($employee->user->whatsapp || $employee->phone_number))
                @php
                    $wa = $employee->user->whatsapp ?? $employee->phone_number;
                    $waClean = preg_replace('/[^0-9]/', '', $wa);
                    if (substr($waClean, 0, 1) === '0') { $waClean = '62' . substr($waClean, 1); }
                @endphp
                <div class="flex justify-between items-center">
                    <span style="color:var(--slate);">WhatsApp:</span>
                    <a href="https://wa.me/{{ $waClean }}" target="_blank" class="font-medium hover:underline" style="color:var(--green);">{{ $wa }}</a>
                </div>
            @endif
            @if($employee->user && $employee->user->email)
                <div class="flex justify-between items-center">
                    <span style="color:var(--slate);">Email:</span>
                    <a href="mailto:{{ $employee->user->email }}" class="hover:underline text-right truncate ml-2" style="color:var(--slate);">{{ $employee->user->email }}</a>
                </div>
            @endif
        </div>

        <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Edit</a>
            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data salesman ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card p-8 text-center" style="color:var(--slate);">
        Tidak ada data salesman ditemukan.
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($employees->hasPages())
<div class="mt-4">
    {{ $employees->appends(['search' => request('search')])->links() }}
</div>
@endif
@endsection