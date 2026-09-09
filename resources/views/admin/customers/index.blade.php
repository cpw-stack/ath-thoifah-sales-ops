@extends('layouts.app')

@section('title', 'Mitra Management')

@section('content')
<!-- x-data diletakkan di pembungkus utama agar tombol dan modal bisa terbaca -->
<div x-data="{ 
    deleteModal: false, deleteId: null, deleteName: '', forceDelete: false,
    bulkModal: false, bulkForceDelete: false 
}">
    
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
        <div>
            <h2 class="display text-2xl">Mitra Management</h2>
            <p class="text-sm" style="color:var(--slate);">Kelola data toko dan limit kredit.</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="btn w-full sm:w-auto text-center">+ Tambah Mitra</a>
    </div>

    @if (session('success'))
        <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">{{ session('success') }}</div>
    @endif

    <!-- HEADER SEARCH & IMPORT -->
    <div class="card p-5 mb-4 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-5">
        
        <!-- Left Side: Search -->
        <div class="flex flex-col gap-2 w-full md:max-w-xs">
            <label class="text-xs font-bold uppercase tracking-wider" style="color:var(--slate);">Cari Mitra</label>
            <div class="relative">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode toko..." class="w-full pr-9">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center" style="background:none; border:none; padding:0; cursor:pointer;">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--slate);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Side: Stats & Actions -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 w-full md:w-auto">
            
            <!-- Stat: Total Mitra -->
            <div class="flex items-center gap-2 px-4 py-2 rounded-lg" style="background:var(--ink); color:#fff;">
                <span class="text-lg">🏪</span>
                <div class="flex flex-col leading-tight">
                    <span class="font-bold text-base">{{ $customers->total() }}</span>
                    <span class="text-[10px] uppercase tracking-wider opacity-80 hidden sm:inline">Total Mitra</span>
                </div>
            </div>

            <!-- Actions Group -->
            <div class="flex flex-col sm:flex-row items-stretch gap-3 flex-1 md:flex-none">
                
                <!-- Export Button -->
                <a href="{{ route('admin.customers.template') }}" class="btn-outline text-xs flex items-center justify-center gap-2" title="Download semua data mitra untuk di-edit/update">
                    <span>📥</span> Export Data
                </a>

                <!-- Import Form -->
                <div class="flex flex-col gap-1 flex-1">
                    <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data" class="flex items-stretch gap-2 w-full">
                        @csrf
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="text-xs border rounded p-1.5 w-full" style="border-color:var(--border);" required onchange="document.getElementById('uploadBtn').disabled = !this.files.length">
                        <button type="submit" id="uploadBtn" class="btn text-xs whitespace-nowrap" disabled>Upload & Update</button>
                    </form>
                    <p class="text-[10px] text-left sm:text-right w-full" style="color:var(--slate);">
                        *Jika Kode Toko sudah ada, data akan diupdate.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Tombol Bulk Delete (Akan muncul jika ada yang dicentang) -->
    <div class="mb-4 flex justify-end">
        <!-- Tombol ini menggunakan @click Alpine.js untuk membuka modal bulk delete -->
        <button type="button" id="bulkDeleteBtn" class="btn text-xs hidden" style="background:var(--red);" @click="bulkForceDelete = false; bulkModal = true">
            🗑️ Hapus Mitra Terpilih (<span id="selectedCount">0</span>)
        </button>
    </div>

    <!-- 1. TAMPILAN DESKTOP (Tabel) - Hanya muncul di layar besar -->
    <div class="card overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                        <th class="p-4 w-10"><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th>
                        <th class="p-4">Kode</th>
                        <th class="p-4">Nama Toko</th>
                        <th class="p-4">Pemilik</th>
                        <th class="p-4">Telepon</th>
                        <th class="p-4">Limit Kredit</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                    <tr class="border-b" style="border-color:var(--border);">
                        <td class="p-4"><input type="checkbox" name="ids[]" value="{{ $customer->id }}" class="mitra-checkbox"></td>
                        <td class="p-4 mono text-xs">{{ $customer->customer_code }}</td>
                        <td class="p-4 font-semibold text-sm">{{ $customer->name }}</td>
                        <td class="p-4 text-sm">{{ $customer->owner_name ?? '-' }}</td>
                        <td class="p-4 text-sm">{{ $customer->phone_number ?? '-' }}</td>
                        <td class="p-4 mono text-sm">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</td>
                        <td class="p-4">
                            @if($customer->status == 'active')
                                <span class="badge badge-green">Active</span>
                            @else
                                <span class="badge badge-slate">Inactive</span>
                            @endif
                        </td>
                        <td class="p-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Detail</a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn-outline text-xs mr-2" style="padding:6px 10px;">Edit</a>
                            <button type="button" @click="deleteId = {{ $customer->id }}; deleteName = '{{ addslashes($customer->name) }}'; forceDelete = false; deleteModal = true" class="text-red-600 text-xs font-bold">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="p-8 text-center" style="color:var(--slate);">Mitra tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t" style="border-color:var(--border);">
            {{ $customers->appends(['search' => request('search')])->links() }}
        </div>
    </div>

    <!-- 2. TAMPILAN MOBILE (Card List) - Hanya muncul di layar HP -->
    <div class="md:hidden space-y-4">
        @forelse ($customers as $customer)
        <div class="card p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="ids[]" value="{{ $customer->id }}" class="mitra-checkbox mt-1">
                    <div>
                        <div class="font-semibold text-base" style="color:var(--ink);">{{ $customer->name }}</div>
                        <div class="text-xs mono" style="color:var(--slate);">Kode: {{ $customer->customer_code }}</div>
                    </div>
                </div>
                @if($customer->status == 'active')
                    <span class="badge badge-green">Active</span>
                @else
                    <span class="badge badge-slate">Inactive</span>
                @endif
            </div>
            
            <div class="text-xs space-y-2 mb-4 border-t pt-3" style="border-color:var(--border);">
                <div class="flex justify-between">
                    <span style="color:var(--slate);">Pemilik:</span>
                    <span class="font-semibold text-right">{{ $customer->owner_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--slate);">Telepon:</span>
                    <span class="font-semibold text-right">{{ $customer->phone_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--slate);">Limit Kredit:</span>
                    <span class="font-semibold text-right mono">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex gap-2 border-t pt-3" style="border-color:var(--border);">
                <a href="{{ route('admin.customers.show', $customer) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Detail</a>
                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn-outline text-xs flex-1 text-center" style="padding:6px 12px;">Edit</a>
                <button type="button" @click="deleteId = {{ $customer->id }}; deleteName = '{{ addslashes($customer->name) }}'; forceDelete = false; deleteModal = true" class="text-red-600 hover:text-red-800 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">
                    Hapus
                </button>
            </div>
        </div>
        @empty
        <div class="card p-8 text-center" style="color:var(--slate);">
            Mitra tidak ditemukan.
        </div>
        @endforelse
        
        @if($customers->hasPages())
        <div class="mt-4">
            {{ $customers->appends(['search' => request('search')])->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus Satu Mitra (Alpine.js) -->
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 shadow-xl">
            <h3 class="text-lg font-bold mb-2" style="color:var(--ink);">Konfirmasi Hapus Mitra</h3>
            <p class="text-sm mb-4" style="color:var(--slate);">Apakah Anda yakin ingin menghapus <span x-text="deleteName" class="font-bold text-red-600"></span>?</p>
            
            <div class="mb-6 p-3 rounded-lg border" style="background:var(--paper-dim); border-color:var(--border);">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" x-model="forceDelete" class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded">
                    <span class="text-sm font-semibold" style="color:var(--ink);">Sekalian hapus semua riwayat Order, Piutang, dan Tugas terkait mitra ini.</span>
                </label>
                <p class="text-xs mt-2 ml-6" style="color:var(--slate);">*Jangan centang jika ingin menyimpan riwayat transaksi untuk keperluan audit.</p>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" @click="deleteModal = false" class="btn-outline text-sm">Batal</button>
                <form :action="'/admin/customers/' + deleteId" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="force_delete_relations" :value="forceDelete ? 1 : 0">
                    <button type="submit" class="btn text-sm" style="background:var(--red);">Hapus Sekarang</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Bulk Delete (Alpine.js) -->
    <div x-show="bulkModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 shadow-xl">
            <h3 class="text-lg font-bold mb-2" style="color:var(--ink);">Konfirmasi Hapus Massal</h3>
            <p class="text-sm mb-4" style="color:var(--slate);">Apakah Anda yakin ingin menghapus <span x-text="document.getElementById('selectedCount').textContent" class="font-bold text-red-600"></span> mitra terpilih?</p>
            
            <div class="mb-6 p-3 rounded-lg border" style="background:var(--paper-dim); border-color:var(--border);">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" x-model="bulkForceDelete" class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded">
                    <span class="text-sm font-semibold" style="color:var(--ink);">Sekalian hapus semua riwayat Order, Piutang, dan Tugas terkait mitra-mitra ini.</span>
                </label>
                <p class="text-xs mt-2 ml-6" style="color:var(--slate);">*Jangan centang jika ingin menyimpan riwayat transaksi untuk keperluan audit.</p>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" @click="bulkModal = false" class="btn-outline text-sm">Batal</button>
                <button type="button" @click="submitBulkDelete(bulkForceDelete)" class="btn text-sm" style="background:var(--red);">Hapus Sekarang</button>
            </div>
        </div>
    </div>

    <!-- Script untuk Checkbox & Bulk Delete -->
    <script>
        function toggleAll(selectAllCheckbox) {
            let checkboxes = document.querySelectorAll('.mitra-checkbox');
            checkboxes.forEach(cb => {
                if (cb.offsetParent !== null) {
                    cb.checked = selectAllCheckbox.checked;
                }
            });
            updateBulkButton();
        }

        document.querySelectorAll('.mitra-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkButton);
        });

        function updateBulkButton() {
            let visibleChecked = 0;
            document.querySelectorAll('.mitra-checkbox:checked').forEach(cb => {
                if (cb.offsetParent !== null) visibleChecked++;
            });

            let btn = document.getElementById('bulkDeleteBtn');
            let countSpan = document.getElementById('selectedCount');
            
            if (visibleChecked > 0) {
                btn.classList.remove('hidden');
                countSpan.textContent = visibleChecked;
            } else {
                btn.classList.add('hidden');
            }
        }

        // Fungsi ini dipanggil saat tombol "Hapus Sekarang" di modal bulk delete diklik
        function submitBulkDelete(isForceDelete) {
            let checkedIds = [];
            document.querySelectorAll('.mitra-checkbox:checked').forEach(cb => {
                if (cb.offsetParent !== null) {
                    checkedIds.push(cb.value);
                }
            });

            let uniqueIds = [...new Set(checkedIds)];
            if (uniqueIds.length === 0) return;

            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.customers.bulk-destroy") }}';

            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            let methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            // Sertakan input force_delete_relations dari parameter isForceDelete
            let forceInput = document.createElement('input');
            forceInput.type = 'hidden';
            forceInput.name = 'force_delete_relations';
            forceInput.value = isForceDelete ? 1 : 0;
            form.appendChild(forceInput);

            uniqueIds.forEach(id => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    </script>

</div> <!-- Penutup div x-data -->
@endsection