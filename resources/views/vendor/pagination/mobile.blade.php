@if ($paginator->hasPages())
<div class="flex justify-between items-center mt-6 gap-3">
    <!-- Tombol Sebelumnya -->
    <a href="{{ $paginator->previousPageUrl() }}" 
       class="flex-1 text-center text-sm font-bold p-3 rounded-lg border {{ $paginator->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}" 
       style="border-color:var(--border); color:var(--ink); background:#fff;">
        ← Sebelumnya
    </a>

    <!-- Info Halaman -->
    <span class="text-xs font-semibold mono" style="color:var(--slate);">
        Hal {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
    </span>

    <!-- Tombor Berikutnya -->
    <a href="{{ $paginator->nextPageUrl() }}" 
       class="flex-1 text-center text-sm font-bold p-3 rounded-lg border {{ $paginator->hasMorePages() ? '' : 'opacity-50 pointer-events-none' }}" 
       style="border-color:var(--border); color:#fff; background:var(--ink);">
        Berikutnya →
    </a>
</div>
@endif