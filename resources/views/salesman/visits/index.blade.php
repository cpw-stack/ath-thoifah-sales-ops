@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    @if ($errors->any())
        <div class="card p-4" style="background:var(--red-soft); color:var(--red); border:1px solid var(--red);">
            <div class="font-bold text-sm mb-1">Terjadi Kesalahan:</div>
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

    @if($isOnlineSalesman ?? false)
        <!-- ============ MODE ONLINE ============ -->
        <div class="card p-4" style="background:var(--ink); border:none;">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-[11px] uppercase tracking-wider" style="color:#9DAEC7;">Mode Online</div>
                    <div class="display text-lg mt-1" style="color:#fff;">Laporan Penjualan Online</div>
                </div>
                <div class="text-right">
                    <div class="mono text-sm" style="color:#C7D2E3;">{{ now()->translatedFormat('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Form Laporan Online -->
        <div class="card p-4">
            <form action="{{ route('salesman.online.store') }}" method="POST" id="onlineForm">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Jam Mulai</label>
                        <input type="time" name="start_time" value="{{ old('start_time', now()->format('H:i')) }}" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Jam Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time', now()->addHour()->format('H:i')) }}" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Catatan / Aktivitas (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" placeholder="Misal: Promosi via live IG, dll..."></textarea>
                </div>

                <!-- ==== Search-to-add produk ==== -->
                <div class="mb-2 relative">
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Tambah Produk</label>
                    <div class="relative">
                        <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" id="productSearchInput" placeholder="Ketik nama produk..." autocomplete="off"
                               onkeydown="return event.key != 'Enter';"
                               oninput="renderSearchResults(this.value)"
                               onfocus="renderSearchResults(this.value)"
                               class="w-full pl-10 pr-3 py-3 text-sm border rounded-lg" style="border-color:var(--border);">
                    </div>

                    <!-- Dropdown hasil pencarian -->
                    <div id="searchResults" class="hidden absolute left-0 right-0 mt-1 z-20 rounded-lg border shadow-lg max-h-64 overflow-y-auto"
                         style="background:#fff; border-color:var(--border);"></div>
                </div>

                <!-- ==== Keranjang / produk terpilih ==== -->
                <div id="cartEmpty" class="card p-6 text-center text-sm mb-4" style="color:var(--slate); background:var(--paper-dim); border:1px dashed var(--border);">
                    Belum ada produk dipilih.<br>Cari & tap produk di atas untuk menambahkan.
                </div>

                <div id="cartList" class="space-y-3 mb-4 hidden"></div>

                <div class="flex justify-between items-center mb-4 p-3 rounded-xl" style="background:var(--paper-dim);">
                    <span class="font-bold text-base" style="color:var(--ink);">Total Penjualan:</span>
                    <span class="font-bold text-base" id="onlineTotal" style="color:var(--orange);">Rp 0</span>
                </div>

                <div id="cartInputs"></div>

                <button type="submit" id="onlineSubmitBtn" class="btn-primary w-full text-sm" style="padding:14px;" disabled>Kirim Laporan Online</button>
            </form>
        </div>

        <!-- Riwayat Laporan Hari Ini -->
        @if($todayReports->count() > 0)
        <div>
            <div class="text-sm font-bold uppercase mb-3" style="color:var(--ink);">Riwayat Laporan Hari Ini</div>
            <div class="space-y-3">
                @foreach($todayReports as $rep)
                <div class="card p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="font-bold text-base">{{ $rep->start_time }} - {{ $rep->end_time }}</div>
                            <div class="text-xs mt-1" style="color:var(--slate);">{{ $rep->notes ?? 'Tanpa catatan' }}</div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="chip chip-done">Rp {{ number_format($rep->total_amount, 0, ',', '.') }}</span>
                            <a href="{{ route('salesman.online.edit', $rep) }}" class="text-xs text-blue-600 font-bold hover:underline">Ubah</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Riwayat Laporan 7 Hari Terakhir -->
        @if($pastReports->count() > 0)
        <div>
            <div class="text-sm font-bold uppercase mb-3" style="color:var(--ink);">Riwayat Laporan (7 Hari Terakhir)</div>
            <div class="space-y-3">
                @foreach($pastReports as $rep)
                <div class="card p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="font-bold text-base">{{ \Carbon\Carbon::parse($rep->report_date)->translatedFormat('l, d M Y') }}</div>
                            <div class="text-xs mt-1" style="color:var(--slate);">Jam: {{ $rep->start_time }} - {{ $rep->end_time }}</div>
                            @if($rep->notes)
                            <div class="text-xs mt-1 italic" style="color:var(--slate);">"{{ $rep->notes }}"</div>
                            @endif
                        </div>
                        <span class="chip chip-done">Rp {{ number_format($rep->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex gap-2 mt-3 pt-3 border-t" style="border-color:var(--border);">
                        <a href="{{ route('salesman.online.edit', $rep) }}" class="btn-outline text-xs flex-1 text-center" style="padding:8px;">Ubah Laporan</a>
                        <form action="{{ route('salesman.online.destroy', $rep) }}" method="POST" onsubmit="return confirm('Hapus laporan ini permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 text-xs font-bold p-2 border rounded" style="border-color:var(--border);">Hapus</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @else
        <!-- ============ MODE OFFLINE ============ -->
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
    @endif

    <!-- Tugas Hari Ini (Tampil untuk semua tipe salesman) -->
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
</div>

@if(!($isOnlineSalesman ?? false))
<!-- Modal Check-In (Bottom Sheet Style) - Only for Offline -->
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
@endif

<script>
    // === Script Mode Offline (Check-In) ===
    function openCheckInModal(planId) {
        document.getElementById('checkInForm').action = `/salesman/visits/${planId}/checkin`;
        document.getElementById('checkInModal').classList.remove('hidden');
        document.getElementById('checkInModal').classList.add('flex');

        const gpsStatus = document.getElementById('gpsStatus');
        const submitBtn = document.querySelector('#checkInForm button[type="submit"]');

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

    // === Script Mode Online (Search-to-add cart) ===
    @if($isOnlineSalesman ?? false)
    const ALL_PRODUCTS = [
        @foreach($products as $p)
        { id: {{ $p->id }}, name: @json($p->name), price: {{ $p->price }} },
        @endforeach
    ];

    let cart = {};

    function renderSearchResults(query) {
        const box = document.getElementById('searchResults');
        const q = (query || '').trim().toLowerCase();

        if (q.length === 0) {
            box.classList.add('hidden');
            box.innerHTML = '';
            return;
        }

        const matches = ALL_PRODUCTS.filter(p => p.name.toLowerCase().includes(q)).slice(0, 8);

        if (matches.length === 0) {
            box.innerHTML = `<div class="p-3 text-xs" style="color:var(--slate);">Produk tidak ditemukan.</div>`;
            box.classList.remove('hidden');
            return;
        }

        box.innerHTML = matches.map(p => `
            <button type="button" onclick="addToCart(${p.id})"
                    class="w-full text-left p-3 text-sm border-b flex justify-between items-center active:bg-gray-50"
                    style="border-color:var(--border);">
                <span class="font-semibold">${p.name}</span>
                <span class="text-xs" style="color:var(--slate);">Rp ${p.price.toLocaleString('id-ID')}</span>
            </button>
        `).join('');
        box.classList.remove('hidden');
    }

    function addToCart(productId) {
        const product = ALL_PRODUCTS.find(p => p.id === productId);
        if (!product) return;

        if (cart[productId]) {
            cart[productId].qty += 1;
        } else {
            cart[productId] = { id: product.id, name: product.name, price: product.price, qty: 1 };
        }

        document.getElementById('productSearchInput').value = '';
        document.getElementById('searchResults').classList.add('hidden');

        renderCart();
    }

    function changeQty(productId, delta) {
        if (!cart[productId]) return;
        cart[productId].qty += delta;
        if (cart[productId].qty <= 0) {
            delete cart[productId];
        }
        renderCart();
    }

    function removeFromCart(productId) {
        delete cart[productId];
        renderCart();
    }

    function renderCart() {
        const listEl = document.getElementById('cartList');
        const emptyEl = document.getElementById('cartEmpty');
        const inputsEl = document.getElementById('cartInputs');
        const submitBtn = document.getElementById('onlineSubmitBtn');
        const items = Object.values(cart);

        if (items.length === 0) {
            listEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            inputsEl.innerHTML = '';
            submitBtn.disabled = true;
            document.getElementById('onlineTotal').textContent = 'Rp 0';
            return;
        }

        emptyEl.classList.add('hidden');
        listEl.classList.remove('hidden');
        submitBtn.disabled = false;

        let total = 0;
        listEl.innerHTML = items.map(item => {
            const subtotal = item.qty * item.price;
            total += subtotal;
            return `
                <div class="border-b pb-3" style="border-color:var(--border);">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-semibold text-sm" style="color:var(--ink);">${item.name}</div>
                        <button type="button" onclick="removeFromCart(${item.id})" class="text-xs font-bold" style="color:var(--red);">Hapus</button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="changeQty(${item.id}, -1)" class="w-9 h-9 rounded-lg border font-bold text-lg" style="border-color:var(--border); background:var(--paper-dim);">−</button>
                            <span class="w-8 text-center font-bold text-sm">${item.qty}</span>
                            <button type="button" onclick="changeQty(${item.id}, 1)" class="w-9 h-9 rounded-lg border font-bold text-lg" style="border-color:var(--border); background:var(--paper-dim);">+</button>
                        </div>
                        <span class="font-bold text-sm" style="color:var(--orange);">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('onlineTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');

        inputsEl.innerHTML = items.map(item => `
            <input type="hidden" name="items[${item.id}][id]" value="${item.id}">
            <input type="hidden" name="items[${item.id}][qty]" value="${item.qty}">
        `).join('');
    }

    document.addEventListener('click', function(e) {
        const box = document.getElementById('searchResults');
        const input = document.getElementById('productSearchInput');
        if (box && input && !box.contains(e.target) && e.target !== input) {
            box.classList.add('hidden');
        }
    });
    @endif
</script>
@endsection