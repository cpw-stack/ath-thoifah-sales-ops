@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    <a href="{{ route('salesman.visits.index') }}" class="text-xs font-semibold flex items-center gap-1" style="color:var(--slate);">← Kembali</a>

    @if(session('success'))
        <div class="card p-3 text-sm" style="background:var(--green-soft); color:var(--green);">✅ {{ session('success') }}</div>
    @endif

    <!-- Info Toko -->
    <div class="card p-4">
        <div class="text-[11px] uppercase tracking-wider" style="color:var(--slate);">Mitra</div>
        <div class="display" style="font-size:19px;">{{ $visit->customer->name }}</div>
        <div class="text-xs mt-1" style="color:var(--slate);">{{ $visit->customer->address }}</div>
        <div class="flex gap-3 mt-3">
            <span class="chip chip-pending">Termin {{ $visit->customer->credit_terms_days }} hari</span>
            <span class="chip chip-dark" style="color:var(--ink); background:var(--paper-dim);">Limit Rp {{ number_format($visit->customer->credit_limit, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Langkah 1: Check-in -->
    <div class="card overflow-hidden">
        <div class="p-3 flex items-center justify-between" style="{{ $visit->check_in_at ? 'background:var(--green-soft)' : '' }}">
            <div class="flex items-center gap-3">
                <span class="mono text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center" style="{{ $visit->check_in_at ? 'background:var(--green);color:#fff' : 'background:var(--paper-dim)' }}">1</span>
                <span class="text-sm font-semibold">Check-in GPS</span>
            </div>
            @if(!$visit->check_in_at)
                <button onclick="openCheckInModal({{ $visit->visit_plan_id }})" class="btn-primary text-xs" style="padding:8px 14px;">Check-in</button>
            @else
                <span class="mono text-[11px]" style="color:var(--green);">✓ {{ $visit->check_in_at->format('H:i') }} · {{ $visit->distance_meters }}m dari toko</span>
            @endif
        </div>
        <div class="perf"></div>

        <!-- Langkah 2: Cek Produk -->
        <div class="p-3" style="{{ $visit->productChecks->count() > 0 ? 'background:var(--green-soft)' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="mono text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center" style="{{ $visit->productChecks->count() > 0 ? 'background:var(--green);color:#fff' : 'background:var(--paper-dim)' }}">2</span>
                    <span class="text-sm font-semibold">Cek Produk</span>
                </div>
                @if($visit->productChecks->count() == 0 && $visit->check_in_at)
                    <button onclick="toggleSection('product-section')" class="btn-primary text-xs" style="padding:8px 14px;">Isi</button>
                @elseif($visit->productChecks->count() > 0)
                    <span class="mono text-[11px]" style="color:var(--green);">✓ {{ $visit->productChecks->count() }} produk dicek</span>
                @endif
            </div>

            <div id="product-section" style="display: none;" class="mt-4">
                <form action="{{ route('salesman.visits.product_check', $visit) }}" method="POST">
                    @csrf
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                        @foreach($products as $p)
                        <div class="flex items-center justify-between text-sm border-b pb-2" style="border-color:var(--border);">
                            <span class="flex-1 pr-2">{{ $p->name }}</span>
                            <input type="hidden" name="products[{{ $p->id }}][id]" value="{{ $p->id }}">
                            <div class="flex gap-1 items-center">
                                <input type="number" name="products[{{ $p->id }}][stock_estimate]" placeholder="Stok" class="w-16 text-xs p-1" min="0">
                                <select name="products[{{ $p->id }}][is_available]" class="text-xs p-1 w-20">
                                    <option value="1">Ada</option>
                                    <option value="0">Habis</option>
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn-primary w-full text-xs mt-3" style="padding:10px;">Simpan Cek Produk</button>
                </form>
            </div>
        </div>
        <div class="perf"></div>

        <!-- Langkah 3: Buat Order -->
        <div class="p-3" style="{{ $visit->order ? 'background:var(--green-soft)' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="mono text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center" style="{{ $visit->order ? 'background:var(--green);color:#fff' : 'background:var(--paper-dim)' }}">3</span>
                    <span class="text-sm font-semibold">Buat Order</span>
                </div>
                @if(!$visit->order && $visit->check_in_at)
                    <button onclick="toggleSection('order-section')" class="btn-primary text-xs" style="padding:8px 14px;">Buat</button>
                @elseif($visit->order)
                    <span class="mono text-[11px]" style="color:var(--green);">✓ Rp {{ number_format($visit->order->total_amount, 0, ',', '.') }}</span>
                @endif
            </div>

            <div id="order-section" style="display: none;" class="mt-4" x-data="{ total: 0, items: {} }">
                <form action="{{ route('salesman.visits.order', $visit) }}" method="POST">
                    @csrf
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                        @foreach($products as $p)
                        <div class="flex items-center justify-between text-sm border-b pb-2" style="border-color:var(--border);">
                            <span class="flex-1 pr-2">{{ $p->name }}<br><span class="text-[10px] mono" style="color:var(--slate);">Rp {{ number_format($p->price, 0, ',', '.') }}</span></span>
                            <input type="hidden" name="items[{{ $p->id }}][id]" value="{{ $p->id }}">
                            <input type="number" name="items[{{ $p->id }}][qty]" placeholder="Qty" class="w-16 text-xs p-1" min="0" x-model.number="items[{{ $p->id }}]" @input="total = Object.values(items).reduce((sum, val) => sum + (val ? val * {{ $p->price }} : 0), 0)">
                        </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center mt-3 font-bold">
                        <span class="text-sm">Total:</span>
                        <span class="mono text-sm" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>
                    <button type="submit" class="btn-primary w-full text-xs mt-3" style="padding:10px;">Simpan Order</button>
                </form>
            </div>
        </div>
        <div class="perf"></div>

        <!-- Langkah 4: Tagih Piutang -->
        <div class="p-3" style="{{ $visit->collections()->count() > 0 ? 'background:var(--green-soft)' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="mono text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center" style="{{ $visit->collections()->count() > 0 ? 'background:var(--green);color:#fff' : 'background:var(--paper-dim)' }}">4</span>
                    <span class="text-sm font-semibold">Tagih Piutang</span>
                </div>
                @if($visit->collections()->count() == 0 && $visit->check_in_at)
                    <button onclick="toggleSection('collection-section')" class="btn-primary text-xs" style="padding:8px 14px;">Tagih</button>
                @elseif($visit->collections()->count() > 0)
                    <span class="mono text-[11px]" style="color:var(--green);">✓ Diterima Rp {{ number_format($visit->collections()->sum('amount'), 0, ',', '.') }}</span>
                @endif
            </div>

            <div id="collection-section" style="display: none;" class="mt-4">
                
                <!-- BLOK DOWNLOAD INVOICE -->
                @if($tasks->count() > 0)
                    <div class="mb-4 p-3 rounded-lg border border-dashed" style="border-color:var(--border); background:var(--paper-dim);">
                        <div class="text-xs font-bold mb-2 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            Dokumen Invoice Penagihan:
                        </div>
                        @foreach($tasks as $t)
                            <a href="{{ asset('storage/' . $t->attachment) }}" target="_blank" class="flex items-center gap-2 text-sm text-red-600 font-semibold mb-1 hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                                {{ $t->title }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($receivables->count() > 0)
                    <div class="space-y-4 max-h-60 overflow-y-auto pr-2">
                        @foreach($receivables as $r)
                            <div class="border rounded-lg p-3" style="border-color:var(--border);">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-bold">{{ $r->reference_code }}</span>
                                    <span class="badge {{ $r->status == 'overdue' ? 'badge-red' : 'badge-amber' }}">{{ $r->status == 'overdue' ? 'Overdue' : 'Tagihan' }}</span>
                                </div>
                                <div class="text-xs mb-3" style="color:var(--slate);">Jatuh Tempo: {{ \Carbon\Carbon::parse($r->due_date)->format('d M Y') }}</div>
                                
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span>Total: <span class="mono font-bold">{{ number_format($r->total_amount, 0, ',', '.') }}</span></span>
                                    <span>Sisa: <span class="mono font-bold" style="color:var(--red);">{{ number_format($r->remaining_amount, 0, ',', '.') }}</span></span>
                                </div>

                                <form action="{{ route('salesman.visits.collection', $visit) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="receivable_id" value="{{ $r->id }}">
                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                        <input type="number" name="amount" placeholder="Jumlah Bayar" class="w-full text-xs p-2" max="{{ $r->remaining_amount }}" required>
                                        <select name="payment_method" class="text-xs p-2">
                                            <option value="cash">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="qris">QRIS</option>
                                        </select>
                                    </div>
                                    <input type="file" name="payment_proof" accept="image/*" class="text-xs mb-2 w-full">
                                    <button type="submit" class="btn-primary w-full text-xs" style="padding:8px;">Bayar Tagihan Ini</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-xs py-4" style="color:var(--slate);">
                        Toko ini tidak memiliki tagihan berjalan (Lunas).
                    </div>
                @endif
            </div>
        </div>
        <div class="perf"></div>

        <!-- Langkah 5: Check-out -->
        <div class="p-3 flex items-center justify-between" style="{{ $visit->check_out_at ? 'background:var(--green-soft)' : '' }}">
            <div class="flex items-center gap-3">
                <span class="mono text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center" style="{{ $visit->check_out_at ? 'background:var(--green);color:#fff' : 'background:var(--paper-dim)' }}">5</span>
                <span class="text-sm font-semibold">Check-out</span>
            </div>
            @if(!$visit->check_out_at && $visit->check_in_at)
                <form action="{{ route('salesman.visits.checkout', $visit) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary text-xs" style="padding:8px 14px;">Check-out</button>
                </form>
            @elseif($visit->check_out_at)
                <span class="stamp">Visit Selesai</span>
            @endif
        </div>
    </div>
</div>

<!-- Modal Check-In (Dari halaman sebelumnya) -->
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
    function toggleSection(id) {
        let el = document.getElementById(id);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    let checkInModal = document.getElementById('checkInModal');
    let checkInForm = document.getElementById('checkInForm');

    function openCheckInModal(planId) {
        checkInForm.action = `/salesman/visits/${planId}/checkin`;
        checkInModal.classList.remove('hidden');
        checkInModal.classList.add('flex');
        const gpsStatus = document.getElementById('gpsStatus');
        gpsStatus.textContent = 'Mengambil lokasi GPS...';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                gpsStatus.textContent = 'Lokasi didapat! Silakan ambil foto.';
            }, function(error) {
                gpsStatus.textContent = 'Gagal mengambil GPS.';
            });
        }
    }

    function closeCheckInModal() {
        checkInModal.classList.add('hidden');
        checkInModal.classList.remove('flex');
    }
</script>
@endsection