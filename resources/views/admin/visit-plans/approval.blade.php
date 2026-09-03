@extends('layouts.app')

@section('title', 'Approval Jadwal Kunjungan')

@section('content')
<div class="mb-6">
    <h2 class="display text-2xl">Approval Jadwal Kunjungan</h2>
    <p class="text-sm" style="color:var(--slate);">Batas waktu approval harian: <span class="badge badge-amber">{{ $deadlineTime }} WIB</span></p>
</div>

@if (session('success'))
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">✅ {{ session('success') }}</div>
@endif

<div class="card overflow-hidden">
    <div class="p-4 border-b condensed" style="border-color:var(--border); background:var(--paper-dim);">
        Daftar Usulan Jadwal Menunggu Approval ({{ $pendingRequests->count() }})
    </div>
    
    @if($pendingRequests->count() > 0)
    <div class="divide-y" style="border-color:var(--border);">
        @foreach($pendingRequests as $req)
        <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="font-semibold text-sm">{{ $req->employee->full_name ?? '-' }}</div>
                <div class="text-xs mt-1" style="color:var(--slate);">
                    Ingin mengunjungi <span class="font-bold" style="color:var(--ink);">{{ $req->customer->name }}</span> pada <span class="mono">{{ \Carbon\Carbon::parse($req->visit_date)->format('d M Y') }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('admin.schedule-approvals.approve', $req) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn text-xs" style="background:var(--green);">Approve</button>
                </form>
                <form action="{{ route('admin.schedule-approvals.reject', $req) }}" method="POST">
                    @csrf
                    <input type="hidden" name="reject_reason" value="Tidak sesuai rute harian" required>
                    <button type="submit" class="btn-outline text-xs" style="border-color:var(--red); color:var(--red);">Reject</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-8 text-center text-sm" style="color:var(--slate);">
        Tidak ada usulan jadwal yang menunggu approval.
    </div>
    @endif
</div>
@endsection