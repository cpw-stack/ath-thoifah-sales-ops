@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    @if ($errors->any())
        <div class="card p-4" style="background:var(--red-soft); color:var(--red); border:1px solid var(--red);">
            <div class="font-bold text-sm mb-1">Gagal Melakukan Check-in:</div>
            <ul class="text-xs list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="card p-3 text-sm" style="background:var(--green-soft); color:var(--green);">✅ {{ session('success') }}</div>
    @endif

    <!-- Header Info -->
    <div class="card p-4" style="background:var(--ink); border:none;">
        <div class="flex justify-between items-center">
            <div>
                <div class="text-[11px] uppercase tracking-wider" style="color:#9DAEC7;">Progress Target Bulan Ini</div>
                <div class="display text-lg mt-1" style="color:#fff;">78%</div>
                <div class="text-xs" style="color:#C7D2E3;">Rp 39.000.000 / Rp 50.000.000</div>
            </div>
            <div class="text-right">
                <div class="mono text-sm" style="color:#C7D2E3;">{{ now()->translatedFormat('d M Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('salesman.schedule.create') }}" class="card p-4 flex flex-col items-center justify-center text-center" style="border:1px dashed var(--orange);">
            <span class="text-3xl mb-1">📅</span>
            <span class="text-xs font-bold" style="color:var(--orange);">Usulkan Jadwal</span>
        </a>
        <a href="#visits" class="card p-4 flex flex-col items-center justify-center text-center">
            <span class="text-3xl mb-1">📍</span>
            <span class="text-xs font-bold">Mulai Kunjungan</span>
        </a>
    </div>

    <!-- Status Usulan Jadwal -->
    @if($scheduleRequests->count() > 0)
    <div>
        <div class="text-sm font-bold uppercase mb-3" style="color:var(--ink);">Status Usulan Jadwal</div>
        <div class="space-y-3">
            @foreach($scheduleRequests as $req)
            <div class="card p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="font-bold text-base">{{ $req->customer->name }}</div>
                        <div class="text-xs" style="color:var(--slate);">{{ \Carbon\Carbon::parse($req->visit_date)->translatedFormat('l, d M Y') }}</div>
                    </div>
                    @if($req->status == 'pending')
                        <span class="chip chip-pending">Menunggu</span>
                    @elseif($req->status == 'approved')
                        <span class="chip chip-done">Disetujui</span>
                    @elseif($req->status == 'rejected')
                        <span class="chip chip-late">Ditolak</span>
                    @else
                        <span class="chip chip-late">Kedaluwarsa</span>
                    @endif
                </div>
                @if($req->status == 'rejected' && $req->reject_reason)
                <div class="text-xs mt-2 p-2 rounded" style="background:var(--red-soft); color:var(--red);">
                    Alasan: {{ $req->reject_reason }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tugas Hari Ini -->
    @if($tasks->count() > 0)
    <div>
        <div class="flex justify-between items-center mb-3">
            <div class="text-sm font-bold uppercase" style="color:var(--ink);">Tugas Hari Ini</div>
            <span class="badge badge-slate">{{ $tasks->count() }} Tugas</span>
        </div>
        <div class="space-y-3">
            @foreach($tasks as $t)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="min-w-0">
                        <div class="text-sm font-bold">{{ $t->title }}</div>
                        <div class="text-xs mt-1" style="color:var(--slate);">{{ $t->customer->name ?? 'Kantor' }}</div>
                    </div>
                    <span class="chip flex-shrink-0 {{ $t->priority == 'high' ? 'chip-late' : 'chip-pending' }}">{{ $t->priority }}</span>
                </div>
                <div class="flex items-center justify-between mt-3 pt-3 border-t" style="border-color:var(--border);">
                    <div class="flex flex-col gap-1">
                        <span class="mono text-[11px]" style="color:var(--slate);">Deadline: {{ $t->due_date->format('d M') }}</span>
                        @if($t->attachment)
                            <a href="{{ asset('storage/' . $t->attachment) }}" target="_blank" class="text-xs text-red-600 font-semibold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                                Lihat Invoice
                            </a>
                        @endif
                    </div>
                    <a href="{{ route('salesman.tasks.show', $t) }}" class="btn-outline-green text-[11px]" style="padding:6px 12px;">Detail Tugas</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Daftar Kunjungan Hari Ini -->
    <div id="visits">
        <div class="flex justify-between items-center mb-3">
            <div class="text-sm font-bold uppercase" style="color:var(--ink);">Jadwal Kunjungan Hari Ini</div>
            <span class="badge badge-slate">{{ $plans->count() }} Toko</span>
        </div>

        @if($plans->count() > 0)
            <div class="space-y-3">
                @foreach ($plans as $plan)
                    @php $v = $plan->visit; @endphp
                    
                    @if($v)
                        <a href="{{ route('salesman.visits.show', $v) }}" class="card p-4 block active:opacity-80">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-bold text-base">{{ $plan->customer->name }}</div>
                                @if ($v->check_out_at)
                                    <span class="chip chip-done">Selesai</span>
                                @else
                                    <span class="chip" style="background:var(--ink); color:#fff;">Sedang Visit</span>
                                @endif
                            </div>
                            <div class="text-xs flex items-center gap-1" style="color:var(--slate);">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $plan->customer->address }}
                            </div>
                            <div class="mt-3 pt-3 border-t text-xs text-center font-bold" style="border-color:var(--border); color:var(--orange);">
                                Lanjutkan Kunjungan →
                            </div>
                        </a>
                    @else
                        <div class="card p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-bold text-base">{{ $plan->customer->name }}</div>
                                <span class="chip chip-pending">Belum Check-in</span>
                            </div>
                            <div class="text-xs mb-3" style="color:var(--slate);">{{ $plan->customer->address }}</div>
                            <button onclick="openCheckInModal({{ $plan->id }})" class="btn-primary w-full text-sm" style="padding:12px;">
                                📍 Check-in di Sini
                            </button>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="card p-6 text-center text-sm" style="color:var(--slate);">
                Tidak ada jadwal kunjungan hari ini.<br>Silakan usulkan jadwal ke Admin.
            </div>
        @endif
    </div>
</div>

<!-- Modal Check-In (Bottom Sheet Style) -->
<div id="checkInModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-end z-50" style="padding:10px;">
    <div class="bg-white rounded-t-2xl p-5 w-full max-w-md mx-auto">
        <h3 class="font-bold text-lg mb-2">Konfirmasi Check-In</h3>
        <p class="text-xs mb-4" style="color:var(--slate);">Pastikan Anda berada di lokasi toko. Sistem akan mencatat GPS & waktu.</p>
        
        <form id="checkInForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            
            <div class="mb-4">
                <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Bukti Foto Depan Toko</label>
                <input type="file" name="photo" accept="image/*" capture="environment" class="w-full text-sm border rounded p-2" required>
            </div>
            
            <button type="submit" class="btn-primary w-full text-sm" style="padding:14px;">Kirim & Check-in</button>
            <button type="button" onclick="closeCheckInModal()" class="w-full text-xs mt-2 p-2" style="color:var(--slate);">Batal</button>
        </form>
        <p id="gpsStatus" class="text-xs text-center mt-2"></p>
    </div>
</div>

<script>
    function openCheckInModal(planId) {
        document.getElementById('checkInForm').action = `/salesman/visits/${planId}/checkin`;
        document.getElementById('checkInModal').classList.remove('hidden');
        document.getElementById('checkInModal').classList.add('flex');
        
        const gpsStatus = document.getElementById('gpsStatus');
        const submitBtn = document.querySelector('#checkInForm button[type="submit"]');
        
        // Matikan tombol submit sementara sampai GPS didapat atau dilewati
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        gpsStatus.textContent = 'Mengambil lokasi GPS...';
        gpsStatus.style.color = 'var(--slate)';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                gpsStatus.textContent = '✅ Lokasi didapat! Silakan ambil foto.';
                gpsStatus.style.color = 'var(--green)';
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }, function(error) {
                gpsStatus.innerHTML = '❌ Gagal mengambil GPS. <button type="button" onclick="useDummyGPS()" style="color:var(--orange); font-weight:bold; text-decoration:underline;">Gunakan Koordinat Dummy</button>';
                gpsStatus.style.color = 'var(--red)';
            });
        } else {
            gpsStatus.innerHTML = 'Browser tidak mendukung GPS. <button type="button" onclick="useDummyGPS()" style="color:var(--orange); font-weight:bold; text-decoration:underline;">Gunakan Koordinat Dummy</button>';
            gpsStatus.style.color = 'var(--red)';
        }
    }

    function useDummyGPS() {
        // Masukkan koordinat dummy (Jakarta) agar form bisa di-submit
        document.getElementById('latitude').value = -6.200000;
        document.getElementById('longitude').value = 106.816666;
        
        const gpsStatus = document.getElementById('gpsStatus');
        const submitBtn = document.querySelector('#checkInForm button[type="submit"]');
        
        gpsStatus.textContent = '✅ Menggunakan koordinat dummy. Silakan ambil foto.';
        gpsStatus.style.color = 'var(--green)';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
    }

    function closeCheckInModal() {
        document.getElementById('checkInModal').classList.add('hidden');
        document.getElementById('checkInModal').classList.remove('flex');
    }
</script>
@endsection