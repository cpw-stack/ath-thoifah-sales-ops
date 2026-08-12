@extends('layouts.mobile')

@section('content')
@if ($errors->any())
    <div class="card p-4 mx-4 mt-4" style="background:var(--red-soft); color:var(--red); border:1px solid var(--red);">
        <div class="font-bold text-sm mb-1">Gagal Melakukan Check-in:</div>
        <ul class="text-xs list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Halaman Beranda (Home) -->
<div x-show="tab==='home'" class="p-5 space-y-5">
    <!-- Target ring summary (Dummy) -->
    <div class="card p-4" style="background:var(--ink); border:none;">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-[11px] uppercase tracking-wider" style="color:#9DAEC7;">Progress Target Bulan Ini</div>
                <div class="display mt-1" style="color:#fff; font-size:26px;">78%</div>
                <div class="text-xs mt-1" style="color:#C7D2E3;">Rp 39.000.000 / Rp 50.000.000</div>
            </div>
            <svg width="72" height="72" viewBox="0 0 72 72">
                <circle cx="36" cy="36" r="30" stroke="#334257" stroke-width="8" fill="none"/>
                <circle cx="36" cy="36" r="30" stroke="#E8622C" stroke-width="8" fill="none" stroke-dasharray="188.5" stroke-dashoffset="41.5" stroke-linecap="round" transform="rotate(-90 36 36)"/>
                <text x="36" y="41" text-anchor="middle" fill="#fff" font-family="JetBrains Mono" font-size="14" font-weight="600">78%</text>
            </svg>
        </div>
    </div>

    <!-- Today's stats (Dummy) -->
    <div class="grid grid-cols-3 gap-3">
        <div class="card p-3 text-center">
            <div class="display" style="font-size:20px;">{{ $plans->where('status', 'completed')->count() }}/{{ $plans->count() }}</div>
            <div class="text-[10px] mt-1 uppercase" style="color:var(--slate);">Kunjungan</div>
        </div>
        <div class="card p-3 text-center">
            <div class="display" style="font-size:20px;">3</div>
            <div class="text-[10px] mt-1 uppercase" style="color:var(--slate);">Order</div>
        </div>
        <div class="card p-3 text-center">
            <div class="display" style="font-size:20px; color:var(--green);">2.1jt</div>
            <div class="text-[10px] mt-1 uppercase" style="color:var(--slate);">Tertagih</div>
        </div>
    </div>

    <!-- Next visits (Data dari DB) -->
    <div>
        <div class="flex items-center justify-between mb-2">
            <div class="condensed text-sm uppercase tracking-wide" style="color:var(--ink-soft);">Kunjungan Berikutnya</div>
            <button @click="tab='visits'" class="text-xs font-semibold" style="color:var(--orange);">Lihat semua →</button>
        </div>
        <div class="space-y-3">
            @foreach ($plans->where('status', 'planned')->take(3) as $plan)
            <div class="card p-3 flex items-center gap-3 cursor-pointer" @click="tab='visits'">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--paper-dim);">🏪</div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold truncate">{{ $plan->customer->name }}</div>
                    <div class="text-xs truncate" style="color:var(--slate);">{{ $plan->customer->address }}</div>
                </div>
                <div class="chip chip-pending">Planned</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Halaman Daftar Kunjungan -->
<div x-show="tab==='visits'" style="display:none;" class="p-4 space-y-3">
    <div class="condensed text-sm uppercase tracking-wide" style="color:var(--ink-soft);">Rencana Kunjungan · Hari Ini</div>
    <div class="progress-track"><div class="progress-fill" style="width: {{ $plans->count() ? ($plans->where('status', 'completed')->count() / $plans->count()) * 100 : 0 }}%"></div></div>
    <div class="text-xs" style="color:var(--slate);">{{ $plans->where('status', 'completed')->count() }} dari {{ $plans->count() }} toko selesai dikunjungi</div>

    <div class="space-y-3 pt-2">
        @foreach ($plans as $plan)
            @php $v = $plan->visit; @endphp
            
            <!-- Jika sudah ada data visit (sudah check-in), bungkus dengan tag <a> -->
            @if($v)
                <a href="{{ route('salesman.visits.show', $v) }}" class="card p-3 block mb-3 cursor-pointer hover:bg-gray-50 transition-colors">
            @else
                <div class="card p-3 mb-3">
            @endif

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                             style="background: {{ $plan->status == 'completed' ? 'var(--green-soft)' : 'var(--paper-dim)' }}">
                            <span>{{ $plan->status == 'completed' ? '✅' : '🏪' }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold truncate">{{ $plan->customer->name }}</div>
                            <div class="text-xs truncate" style="color:var(--slate);">{{ $plan->customer->address }}</div>
                        </div>
                    </div>
                    <div class="chip flex-shrink-0 {{ $plan->status == 'completed' ? 'chip-done' : 'chip-pending' }}">
                        {{ $plan->status == 'completed' ? 'Sedang Visit' : 'Belum' }}
                    </div>
                </div>

                <!-- Tombol Aksi Visit (Hanya muncul jika belum check-in ATAU sedang dalam kunjungan) -->
                @if ($plan->status == 'planned')
                    <button onclick="openCheckInModal({{ $plan->id }})" class="btn-primary w-full mt-3 text-sm" style="padding:10px;">
                        📍 Check-in di Sini
                    </button>
                @elseif ($plan->status == 'completed' && $v)
                    @if ($v->check_out_at)
                        <div class="mt-3 text-center text-xs mono" style="color:var(--green);">✓ Selesai pada {{ $v->check_out_at->format('H:i') }}</div>
                    @else
                        <div class="mt-3 text-xs mb-2" style="color:var(--slate);">Jarak Check-in: {{ $v->distance_meters }}m ({{ $v->check_in_status }})</div>
                        <!-- Tombol ini sebenarnya tidak wajib karena card bisa diklik, tapi bagus untuk UX -->
                        <div class="btn-primary w-full mt-3 text-sm text-center" style="padding:10px; background:var(--ink); box-shadow:0 4px 0 #111827;">
                            Lanjutkan Kunjungan →
                        </div>
                    @endif
                @endif

            @if($v)
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>
</div>

<!-- Halaman Tugas -->
<div x-show="tab==='tasks'" style="display:none;" class="p-4 space-y-3">
    <div class="condensed text-sm uppercase tracking-wide" style="color:var(--ink-soft);">Tugas Saya Hari Ini</div>
    @if($tasks->count() > 0)
        @foreach($tasks as $t)
        <div class="card p-3">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-sm font-semibold">{{ $t->title }}</div>
                    <div class="text-xs mt-0.5" style="color:var(--slate);">{{ $t->customer->name ?? 'Kantor' }}</div>
                </div>
                <span class="chip flex-shrink-0 {{ $t->priority == 'high' ? 'chip-late' : 'chip-pending' }}">{{ $t->priority }}</span>
            </div>
            <div class="flex items-center justify-between mt-3">
                <div class="flex flex-col gap-1">
                    <span class="mono text-[11px]" style="color:var(--slate);">Deadline: {{ $t->due_date->format('d M') }}</span>
                    @if($t->attachment)
                        <a href="{{ asset('storage/' . $t->attachment) }}" target="_blank" class="text-xs text-red-600 font-semibold flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                            Lihat Invoice
                        </a>
                    @endif
                </div>
                <!-- UBAH BAGIAN INI -->
                <a href="{{ route('salesman.tasks.show', $t) }}" class="btn-outline-green text-[11px]" style="padding:6px 12px;">Detail</a>
            </div>
        </div>
        @endforeach
    @else
        <div class="card p-6 text-center text-sm" style="color:var(--slate);">Tidak ada tugas hari ini.</div>
    @endif
</div>

<!-- Modal Check-In -->
<div id="checkInModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center z-50" style="padding:20px;">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Konfirmasi Check-In</h3>
        <p class="text-sm text-gray-500 mb-4">Pastikan Anda berada di lokasi toko.</p>
        
        <form id="checkInForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Bukti Foto</label>
                <input type="file" name="photo" accept="image/*" capture="environment" class="w-full border rounded p-2 mt-1" required>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeCheckInModal()" class="btn-outline-green" style="border-color:var(--slate); color:var(--slate);">Batal</button>
                <button type="submit" class="btn-primary">Kirim</button>
            </div>
        </form>
        <p id="gpsStatus" class="text-xs text-blue-500 mt-2"></p>
    </div>
</div>

<script>
    let checkInModal = document.getElementById('checkInModal');
    let checkInForm = document.getElementById('checkInForm');

    function openCheckInModal(planId) {
        checkInForm.action = `/salesman/visits/${planId}/checkin`;
        checkInModal.classList.remove('hidden');
        checkInModal.classList.add('flex');
        
        const gpsStatus = document.getElementById('gpsStatus');
        gpsStatus.textContent = 'Mengambil lokasi GPS...';
        gpsStatus.style.color = 'var(--slate)';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                gpsStatus.textContent = '✓ Lokasi didapat! Silakan ambil foto.';
                gpsStatus.style.color = 'var(--green)';
            }, function(error) {
                gpsStatus.textContent = '❌ Gagal mengambil GPS. Browser memblokir akses lokasi.';
                gpsStatus.style.color = 'var(--red)';
                alert('Akses lokasi diblokir. Untuk uji coba di PC, izinkan lokasi di pengaturan browser atau gunakan http://localhost:8000');
            });
        } else {
            gpsStatus.textContent = 'Browser tidak mendukung GPS.';
            gpsStatus.style.color = 'var(--red)';
        }
    }

    function closeCheckInModal() {
        checkInModal.classList.add('hidden');
        checkInModal.classList.remove('flex');
    }
</script>
@endsection