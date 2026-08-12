@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    <a href="{{ route('salesman.home') }}" class="text-xs font-semibold flex items-center gap-1" style="color:var(--slate);">← Kembali</a>

    <!-- Header Tugas -->
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="text-xs uppercase tracking-wider" style="color:var(--slate);">Judul Tugas</div>
            <h2 class="display text-xl mt-1">{{ $task->title }}</h2>
        </div>
        <span class="chip flex-shrink-0 {{ $task->priority == 'high' ? 'chip-late' : 'chip-pending' }}">{{ $task->priority }}</span>
    </div>

    <!-- Info Toko & Deadline -->
    <div class="card p-4 grid grid-cols-2 gap-4">
        <div>
            <div class="text-xs uppercase tracking-wider" style="color:var(--slate);">Toko Mitra</div>
            <div class="font-semibold mt-1">{{ $task->customer->name ?? 'Kantor' }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wider" style="color:var(--slate);">Deadline</div>
            <div class="font-semibold mt-1 mono">{{ $task->due_date->format('d M Y') }}</div>
        </div>
    </div>

    <!-- Deskripsi -->
    <div class="card p-4">
        <div class="text-xs uppercase tracking-wider mb-2" style="color:var(--slate);">Deskripsi Tugas</div>
        <p class="text-sm" style="color:var(--ink);">{{ $task->description ?? 'Tidak ada deskripsi tambahan.' }}</p>
    </div>

    <!-- Lampiran -->
    @if($task->attachment)
    <div class="card p-4">
        <div class="text-xs uppercase tracking-wider mb-2" style="color:var(--slate);">Lampiran Dokumen</div>
        <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="flex items-center justify-between p-3 rounded-lg border border-dashed" style="border-color:var(--border); background:var(--paper-dim);">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C23B22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
                <div>
                    <div class="text-sm font-semibold">Invoice Penagihan</div>
                    <div class="text-xs" style="color:var(--slate);">Klik untuk membuka file</div>
                </div>
            </div>
            <span class="btn-primary text-xs" style="padding:6px 10px;">Buka</span>
        </a>
    </div>
    @endif

    <!-- Status Tugas -->
    <div class="text-center text-xs mt-4" style="color:var(--slate);">
        Status: <span class="badge {{ $task->status == 'completed' ? 'badge-done' : 'badge-pending' }}">{{ $task->status }}</span>
    </div>
</div>
@endsection