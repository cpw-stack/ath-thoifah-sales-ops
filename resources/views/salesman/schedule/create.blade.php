@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    <a href="{{ route('salesman.home') }}" class="text-sm font-semibold flex items-center gap-1" style="color:var(--slate);">← Kembali</a>

    <div>
        <h2 class="display text-xl">Usulkan Jadwal</h2>
        <p class="text-xs mt-1" style="color:var(--slate);">Pilih toko dan tanggal kunjungan. Admin akan menyetujui sebelum jam 10 pagi.</p>
    </div>

    @if ($errors->any())
        <div class="card p-4 text-sm" style="background:var(--red-soft); color:var(--red);">
            @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    <div class="card p-5">
        <form action="{{ route('salesman.schedule.store') }}" method="POST" class="space-y-5" id="scheduleForm">
            @csrf
            
            <!-- KOMPONEN PENCARIAN TOKO (ALPINE.JS) -->
            <div x-data="customerSearch()" class="relative">
                <label class="block text-xs font-bold mb-2" style="color:var(--slate);">PILIH TOKO MITRA</label>
                
                <!-- Hidden input untuk mengirim ID toko ke server (required dihapus agar tidak macet) -->
                <input type="hidden" name="customer_id" x-model="selectedId">
                @error('customer_id') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
                
                <!-- Tampilan saat toko sudah dipilih -->
                <div x-show="selectedId" x-cloak class="w-full p-3 rounded-lg border flex items-center justify-between" style="border-color:var(--border); background:var(--paper-dim);">
                    <span class="font-semibold text-sm" style="color:var(--ink);" x-text="selectedName"></span>
                    <button type="button" @click="clear()" class="text-xs font-bold" style="color:var(--red);">Ubah</button>
                </div>
                
                <!-- Tampilan saat mencari toko -->
                <div x-show="!selectedId">
                    <input 
                        type="text" 
                        x-model="search" 
                        @focus="isOpen = true" 
                        @blur="setTimeout(() => isOpen = false, 200)"
                        placeholder="Ketik nama atau kode toko..." 
                        class="w-full text-sm p-3 rounded-lg border" 
                        style="border-color:var(--border);"
                    >
                    
                    <!-- Dropdown Hasil Pencarian -->
                    <div x-show="isOpen && search.length > 0" 
                         x-cloak 
                         class="absolute left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white border rounded-lg shadow-lg z-50" 
                         style="border-color:var(--border);">
                        
                        <template x-for="c in filtered" :key="c.id">
                            <div @mousedown="select(c)" class="p-3 hover:bg-gray-100 cursor-pointer border-b text-sm" style="border-color:var(--border);">
                                <div class="font-semibold" style="color:var(--ink);" x-text="c.name"></div>
                                <div class="text-xs" style="color:var(--slate);" x-text="c.code"></div>
                            </div>
                        </template>
                        
                        <!-- Pesan jika tidak ada hasil -->
                        <div x-show="filtered.length === 0" class="p-3 text-sm text-center" style="color:var(--slate);">
                            Toko tidak ditemukan.
                        </div>
                    </div>
                </div>
            </div>

            <!-- TANGGAL KUNJUNGAN -->
            <div>
                <label class="block text-xs font-bold mb-2" style="color:var(--slate);">TANGGAL KUNJUNGAN</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <!-- Menggunakan sr-only agar tetap bisa difokuskan browser saat validasi -->
                        <input type="radio" name="visit_date" value="{{ today()->format('Y-m-d') }}" class="sr-only peer" required>
                        <div class="p-3 text-center rounded-lg border text-sm font-semibold peer-checked:bg-[#1B2A41] peer-checked:text-white transition" style="border-color:var(--border);">
                            Hari Ini<br><span class="text-xs font-normal">{{ today()->translatedFormat('d M') }}</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="visit_date" value="{{ today()->addDay()->format('Y-m-d') }}" class="sr-only peer">
                        <div class="p-3 text-center rounded-lg border text-sm font-semibold peer-checked:bg-[#1B2A41] peer-checked:text-white transition" style="border-color:var(--border);">
                            Besok<br><span class="text-xs font-normal">{{ today()->addDay()->translatedFormat('d M') }}</span>
                        </div>
                    </label>
                </div>
                @error('visit_date') 
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p> 
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full text-base" style="padding:14px;">
                Kirim Usulan Jadwal
            </button>
        </form>
    </div>
</div>

<!-- CSS dan Script dipisah ke bawah agar HTML structure clean dan tidak tersangkut -->
<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    function customerSearch() {
        return {
            search: '',
            selectedId: null,
            selectedName: '',
            isOpen: false,
            // Data dari Laravel di-encode menjadi JSON dan ditempel aman di sini
            customers: @json($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'code' => $c->customer_code])),
            get filtered() {
                if (this.search.length < 1) return [];
                var q = this.search.toLowerCase();
                return this.customers.filter(c => 
                    c.name.toLowerCase().includes(q) || 
                    c.code.toLowerCase().includes(q)
                );
            },
            select(c) {
                this.selectedId = c.id;
                this.selectedName = c.name + ' (' + c.code + ')';
                this.search = '';
                this.isOpen = false;
            },
            clear() {
                this.selectedId = null;
                this.selectedName = '';
                this.search = '';
                this.isOpen = true;
            }
        }
    }
</script>
@endsection