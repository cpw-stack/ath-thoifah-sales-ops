@extends('layouts.mobile')

@section('content')

@php
    // Tentukan langkah aktif saat halaman pertama kali dibuka
    $currentStep = 1;
    if ($visit->check_in_at) {
        if (request('edit_product')) {
            // Jika tombol "Ubah Cek Produk" diklik, paksa tetap di langkah 2
            $currentStep = 2;
        } elseif ($visit->productChecks->count() == 0) {
            $currentStep = 2;
        } elseif (!$visit->order) {
            $currentStep = 3;
        } elseif ($visit->collections()->count() == 0) {
            $currentStep = 4;
        } else {
            $currentStep = 5;
        }
    }
@endphp

<style>
    .visit-shell { min-height: 100%; background: var(--paper, #fff); display: flex; flex-direction: column; }
    .visit-head { position: sticky; top: 0; z-index: 30; background: var(--paper, #fff); border-bottom: 1px solid var(--border); box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04); }
    .head-row { display: flex; align-items: center; gap: 12px; padding: 10px 16px 12px; }
    .back-btn { width: 36px; height: 36px; border-radius: 999px; display: flex; align-items: center; justify-content: center; background: var(--paper-dim); color: var(--ink); flex-shrink: 0; text-decoration: none; }
    .head-info { min-width: 0; flex: 1; }
    .head-store { font-size: 17px; font-weight: 800; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--ink); }
    .head-address { font-size: 12.5px; color: var(--slate); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 1px; }
    .head-chip { font-size: 10.5px; font-weight: 700; padding: 5px 9px; border-radius: 999px; white-space: nowrap; background: var(--paper-dim); color: var(--slate); }

    .step-tabs { display: flex; align-items: flex-start; padding: 4px 14px 12px; gap: 0; }
    .step-tab { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px; position: relative; background: none; border: none; padding: 4px 2px 0; color: var(--slate); cursor: pointer; }
    .step-tab::before { content: ''; position: absolute; top: 15px; left: -50%; width: 100%; height: 2px; background: var(--border); z-index: -1; }
    .step-tab:first-child::before { display: none; }
    .step-tab.is-done::before { background: var(--green); }
    .step-dot { width: 30px; height: 30px; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; background: var(--paper-dim); color: var(--slate); border: 2px solid var(--paper-dim); }
    .step-tab.is-current .step-dot { background: var(--green); color: #fff; border-color: var(--green); }
    .step-tab.is-done .step-dot { background: #fff; color: var(--green); border-color: var(--green); }
    .step-tab.is-locked { opacity: 0.4; cursor: not-allowed; }
    .step-label { font-size: 10px; font-weight: 700; text-align: center; line-height: 1.15; max-width: 62px; }
    .step-tab.is-current .step-label { color: var(--ink); }

    .step-panels { flex: 1; }
    .step-panel { display: none; padding: 18px 16px 40px; }
    .step-panel.is-visible { display: block; }

    .panel-heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .panel-title { font-size: 19px; font-weight: 800; color: var(--ink); }
    .panel-status-pill { font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 999px; }
    .panel-status-pill.done { background: var(--green-soft); color: var(--green); }
    .panel-status-pill.pending { background: var(--paper-dim); color: var(--slate); }

    .empty-state { text-align: center; padding: 40px 20px; color: var(--slate); }
    .empty-state svg { margin: 0 auto 12px; opacity: 0.5; }
    .empty-state .empty-title { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
    .empty-state .empty-sub { font-size: 13px; }

    .btn-primary-lg { font-size: 15px; font-weight: 700; padding: 14px 20px; min-height: 48px; border-radius: 12px; }
    .btn-primary-block { width: 100%; font-size: 16px; font-weight: 700; padding: 16px; min-height: 54px; border-radius: 12px; margin-top: 16px; }
    .btn-secondary-block { width: 100%; font-size: 15px; font-weight: 700; padding: 15px; min-height: 50px; border-radius: 12px; background: var(--paper-dim); color: var(--ink); border: none; display: block; text-align: center; text-decoration: none; }

    .product-search { margin-bottom: 10px; position: relative; }
    .product-search svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--slate); }
    .product-search input { width: 100%; font-size: 16px; padding: 14px 16px 14px 42px; border-radius: 12px; border: 1px solid var(--border); background: var(--paper-dim); }
    .product-count { font-size: 12px; color: var(--slate); margin: 2px 2px 10px; }
    .product-empty { text-align: center; font-size: 13.5px; color: var(--slate); padding: 24px 0; display: none; }

    .product-item { border-bottom: 1px solid var(--border); padding: 14px 0; }
    .product-item .p-name { font-weight: 600; color: var(--ink); font-size: 15px; margin-bottom: 4px; }
    .product-item .p-price { font-size: 12.5px; color: var(--slate); }
    .stock-badge { font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px; background: var(--paper-dim); color: var(--slate); }

    .qty-stepper { display: flex; align-items: center; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; flex-shrink: 0; }
    .qty-stepper button { width: 40px; height: 40px; font-size: 20px; font-weight: 700; background: var(--paper-dim); border: none; color: var(--ink); cursor: pointer; }
    .qty-stepper input { width: 46px; height: 40px; text-align: center; font-size: 15px; font-weight: 700; border: none; border-left: 1px solid var(--border); border-right: 1px solid var(--border); background: #fff; }

    .field-lg { font-size: 16px; padding: 14px 16px; border-radius: 10px; min-height: 50px; border: 1px solid var(--border); width: 100%; }
    input[type="file"].file-lg { font-size: 13.5px; padding: 12px; border: 1px dashed var(--border); border-radius: 10px; width: 100%; background: var(--paper-dim); }

    .receivable-card { padding: 16px; border-radius: 12px; border: 1px solid var(--border); }
    .receivable-card + .receivable-card { margin-top: 12px; }
    .summary-list { border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 18px; }
    .summary-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; font-size: 14px; }
    .summary-row + .summary-row { border-top: 1px solid var(--border); }
    .summary-row .label { display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--ink); }
    .summary-row .value.ok { color: var(--green); font-weight: 700; font-size: 13px; }
    .summary-row .value.pending { color: var(--slate); font-weight: 600; font-size: 13px; }

    #checkInModal .modal-box, #revisionModal .modal-box { border-radius: 18px; padding: 22px; }
    #checkInModal h3, #revisionModal h3 { font-size: 19px; font-weight: 800; color: var(--ink); }
    #checkInModal p, #revisionModal p { font-size: 14px; color: var(--slate); }
    #checkInModal label, #revisionModal label { font-size: 14px; font-weight: 600; color: var(--ink); }
    #checkInModal .btn-outline-green, #checkInModal .btn-primary, #revisionModal .btn-primary { font-size: 15px; font-weight: 700; padding: 14px 18px; min-height: 50px; border-radius: 12px; }
</style>

<div class="visit-shell" id="visitShell">

    <!-- Sticky: back + info toko + step tabs -->
    <div class="visit-head" id="visitHead">
        <div class="head-row">
            <a href="{{ route('salesman.home') }}" class="back-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
            </a>
            <div class="head-info">
                <div class="head-store">{{ $visit->customer->name }}</div>
                <div class="head-address">{{ $visit->customer->address }}</div>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <span class="head-chip">Termin {{ $visit->customer->credit_terms_days }} hari</span>
            </div>
        </div>

        <div class="step-tabs" id="stepTabs">
            @php
                $stepMeta = [1 => 'Check-in', 2 => 'Cek Produk', 3 => 'Buat Order', 4 => 'Tagih', 5 => 'Check-out'];
                $stepDone = [
                    1 => (bool) $visit->check_in_at,
                    2 => $visit->productChecks->count() > 0,
                    3 => (bool) $visit->order,
                    4 => $visit->collections()->count() > 0,
                    5 => (bool) $visit->check_out_at,
                ];
            @endphp
            @foreach($stepMeta as $n => $label)
                @php
                    $locked = $n > 1 && !$visit->check_in_at;
                    $classes = ['step-tab'];
                    if ($stepDone[$n]) $classes[] = 'is-done';
                    if ($n === $currentStep) $classes[] = 'is-current';
                    if ($locked) $classes[] = 'is-locked';
                @endphp
                <button type="button" class="{{ implode(' ', $classes) }}" data-step="{{ $n }}" onclick="{{ $locked ? '' : "switchStep($n)" }}" {{ $locked ? 'disabled' : '' }}>
                    <span class="step-dot">{{ $stepDone[$n] ? '✓' : $n }}</span>
                    <span class="step-label">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>

    @if(session('success'))
        <div style="margin:14px 16px 0; background:var(--green-soft); color:var(--green); font-size:14px; padding:12px 14px; border-radius:10px;">✅ {{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="margin:14px 16px 0; background:var(--red-soft); color:var(--red); font-size:14px; padding:12px 14px; border-radius:10px;">⚠️ {{ session('error') }}</div>
    @endif

    <!-- Panels -->
    <div class="step-panels">

        <!-- Panel 1: Check-in -->
        <section id="panel-1" class="step-panel {{ $currentStep === 1 ? 'is-visible' : '' }}">
            <div class="panel-heading">
                <span class="panel-title">Check-in GPS</span>
                @if($visit->check_in_at) <span class="panel-status-pill done">Selesai</span> @else <span class="panel-status-pill pending">Menunggu</span> @endif
            </div>
            @if($visit->check_in_at)
                <div class="summary-list">
                    <div class="summary-row"><span class="label">Waktu check-in</span><span class="mono">{{ $visit->check_in_at->format('H:i, d M Y') }}</span></div>
                    <div class="summary-row"><span class="label">Jarak dari toko</span><span class="mono">{{ $visit->distance_meters }} m</span></div>
                </div>
                <button onclick="switchStep(2)" class="btn-primary btn-primary-block" style="background:var(--ink);">Lanjut ke Cek Produk →</button>
            @else
                <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <div class="empty-title">Belum check-in</div>
                    <div class="empty-sub">Pastikan Anda sudah berada di lokasi toko sebelum check-in.</div>
                </div>
                <button onclick="openCheckInModal({{ $visit->visit_plan_id }})" class="btn-primary btn-primary-block">Check-in Sekarang</button>
            @endif
        </section>

        <!-- Panel 2: Cek Produk -->
        <section id="panel-2" class="step-panel {{ $currentStep === 2 ? 'is-visible' : '' }}">
            <div class="panel-heading">
                <span class="panel-title">Cek Produk</span>
                @if($visit->productChecks->count() > 0 && !request('edit_product')) <span class="panel-status-pill done">{{ $visit->productChecks->count() }} produk</span> @else <span class="panel-status-pill pending">Belum dicek</span> @endif
            </div>

            @if($visit->productChecks->count() > 0 && !request('edit_product'))
                <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6L9 17l-5-5"></path></svg>
                    <div class="empty-title">Cek produk selesai</div>
                    <div class="empty-sub">{{ $visit->productChecks->count() }} produk sudah dicatat untuk kunjungan ini.</div>
                </div>
                <a href="{{ route('salesman.visits.show', $visit) }}?edit_product=1" class="btn-secondary-block" style="margin-top: 20px;">Ubah Cek Produk</a>
                <button onclick="switchStep(3)" class="btn-primary btn-primary-block">Lanjut ke Buat Order →</button>
            @else
                <form action="{{ route('salesman.visits.product_check', $visit) }}" method="POST">
                    @csrf
                    <div class="product-search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" placeholder="Cari nama produk..." onkeydown="return event.key != 'Enter';" oninput="filterList('product-list', this.value)">
                    </div>
                    <div class="product-count" id="product-list-count">{{ $products->count() }} produk</div>
                    <div id="product-list-empty" class="product-empty">Produk tidak ditemukan.</div>
                    <div id="product-list">
                        @foreach($products as $p)
                            @php $existing = $visit->productChecks->where('product_id', $p->id)->first(); @endphp
                            <div class="product-item" data-name="{{ strtolower($p->name) }}">
                                <div class="p-name">{{ $p->name }}</div>
                                <input type="hidden" name="products[{{ $p->id }}][id]" value="{{ $p->id }}">
                                <div class="flex gap-2 mt-2">
                                    <input type="number" name="products[{{ $p->id }}][stock_estimate]" placeholder="Estimasi Stok" value="{{ $existing->stock_estimate ?? '' }}" class="flex-1 p-2 text-sm rounded-lg border w-full" style="border-color:var(--border);" min="0">
                                    <select name="products[{{ $p->id }}][is_available]" class="p-2 text-sm rounded-lg border" style="border-color:var(--border);">
                                        <option value="1" {{ ($existing && $existing->is_available == 1) ? 'selected' : '' }}>Ada</option>
                                        <option value="0" {{ ($existing && $existing->is_available == 0) ? 'selected' : '' }}>Habis</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn-primary btn-primary-block">Simpan Cek Produk</button>
                </form>
            @endif
        </section>

        <!-- Panel 3: Buat Order -->
        <section id="panel-3" class="step-panel {{ $currentStep === 3 ? 'is-visible' : '' }}">
            <div class="panel-heading">
                <span class="panel-title">Form Pemesanan (DP)</span>
                @if($visit->order) <span class="panel-status-pill done">Rp {{ number_format($visit->order->total_amount, 0, ',', '.') }}</span> @else <span class="panel-status-pill pending">Belum ada order</span> @endif
            </div>
            
            @if($visit->order)
                @php $order = $visit->order; @endphp
                
                <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6L9 17l-5-5"></path></svg>
                    <div class="empty-title">Order sudah dibuat</div>
                    <div class="empty-sub">Total order Rp {{ number_format($order->total_amount, 0, ',', '.') }} ({{ $order->payment_type }})</div>
                </div>

                <!-- Tombol Aksi Order -->
                <div class="mt-4 space-y-3">
                    @if(($order->is_editable ?? true) && $order->payment_status == 'unpaid' && $order->created_at->isToday())
                        <!-- SKENARIO 1: BISA EDIT LANGSUNG -->
                        <a href="{{ route('salesman.orders.edit', $order) }}" class="btn-primary btn-primary-block" style="margin-top: 0;">
                            ✏️ Edit Order Langsung
                        </a>
                    @else
                        <!-- SKENARIO 2: WAJIB AJUKAN REVISI -->
                        @if($order->revisions()->where('status', 'pending')->exists())
                            <div class="p-3 text-center text-xs rounded-xl" style="background:var(--paper-dim); color:var(--slate);">
                                ⏳ Pengajuan revisi sedang menunggu persetujuan Admin.
                            </div>
                        @else
                            <button type="button" onclick="openRevisionModal({{ $order->id }})" class="btn-secondary-block">
                                📝 Ajukan Revisi Order
                            </button>
                        @endif
                    @endif
                    
                    <button onclick="switchStep(4)" class="btn-primary btn-primary-block" style="background:var(--ink);">Lanjut ke Penagihan →</button>
                </div>

            @else
                <!-- Info Diskon Mitra -->
                <div class="mb-4 p-3 rounded-xl border-2 border-dashed" style="border-color:var(--border); background:var(--paper-dim);">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-xs font-bold uppercase" style="color:var(--slate);">Diskon Mitra</div>
                            @if($discount && $discount->is_approved)
                                <div class="text-sm font-bold text-green-600">Disetujui: {{ $discount->discount_value }}%</div>
                            @elseif($discount && !$discount->is_approved)
                                <div class="text-sm font-bold text-yellow-600">Menunggu Approval: {{ $discount->discount_value }}%</div>
                            @else
                                <div class="text-sm font-bold text-gray-500">Belum ada diskon</div>
                            @endif
                        </div>
                        
                        <!-- Tombol hanya muncul jika belum ada diskon ATAU jika diskon sudah disetujui (untuk ajukan perubahan) -->
                        @if(!$discount || $discount->is_approved)
                            <button type="button" onclick="document.getElementById('discount-form').classList.toggle('hidden')" class="text-xs font-bold text-white px-3 py-2 rounded-lg" style="background:var(--ink);">
                                {{ $discount ? 'Ajukan Perubahan' : 'Ajukan Diskon' }}
                            </button>
                        @endif
                    </div>
                    
                    <!-- Form Ajukan/Ubah Diskon -->
                    <div id="discount-form" class="hidden mt-3 pt-3 border-t" style="border-color:var(--border);">
                        <form action="{{ route('salesman.visits.propose_discount', $visit) }}" method="POST">
                            @csrf
                            <label class="text-xs font-semibold block mb-1" style="color:var(--slate);">Masukkan % Diskon (0-100)</label>
                            <div class="flex gap-2">
                                <input type="number" name="discount_value" min="0" max="100" value="{{ $discount->discount_value ?? '' }}" placeholder="Contoh: 10" class="flex-1 p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                                <button type="submit" class="px-4 py-2 text-sm font-bold text-white rounded-lg" style="background:var(--orange);">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>

                <form action="{{ route('salesman.visits.order', $visit) }}" method="POST" id="orderForm">
                    @csrf
                    <!-- Header Form DP -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1" style="color:var(--slate);">Tanggal Kirim</label>
                            <input type="date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                        </div>
                        <!-- Pilihan Tipe Pembelian -->
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1" style="color:var(--slate);">Tipe Bayar</label>
                            <select name="payment_type" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                                <option value="cash">Cash / Tunai</option>
                                <option value="konsinyasi">Konsinyasi</option>
                                <option value="piutang">Piutang</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase mb-1" style="color:var(--slate);">Keterangan Order</label>
                        <textarea name="notes" rows="2" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" placeholder="Misal: Kirim jam 3 sore, titip di gudang depan..."></textarea>
                    </div>

                    <div class="product-search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" placeholder="Cari produk untuk dipesan..." onkeydown="return event.key != 'Enter';" oninput="filterList('order-list', this.value)">
                    </div>
                    <div class="product-count" id="order-list-count">{{ $products->count() }} produk tersedia</div>
                    <div id="order-list-empty" class="product-empty">Produk tidak ditemukan.</div>
                    <div id="order-list">
                        @foreach($products as $p)
                        <div class="product-item" data-name="{{ strtolower($p->name) }}">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 pr-2">
                                    <div class="p-name">{{ $p->name }}</div>
                                    <div class="p-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="stock-badge">Stok: {{ $p->stock }}</div>
                            </div>
                            <input type="hidden" name="items[{{ $p->id }}][id]" value="{{ $p->id }}">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="updateQty(this, -1)" class="w-10 h-10 rounded-lg border font-bold text-lg" style="border-color:var(--border); background:var(--paper-dim);">−</button>
                                <input type="number" name="items[{{ $p->id }}][qty]" placeholder="0" min="0" data-price="{{ $p->price }}" oninput="calcTotal()" class="w-16 h-10 text-center font-bold text-sm border rounded-lg" style="border-color:var(--border);">
                                <button type="button" onclick="updateQty(this, 1)" class="w-10 h-10 rounded-lg border font-bold text-lg" style="border-color:var(--border); background:var(--paper-dim);">+</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 p-3 rounded-xl space-y-2" style="background:var(--paper-dim);">
                        <div class="flex justify-between text-sm">
                            <span style="color:var(--slate);">Subtotal</span>
                            <span class="mono font-semibold" id="orderSubtotal">Rp 0</span>
                        </div>
                        @if($discount && $discount->is_approved)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon ({{ $discount->discount_value }}%)</span>
                            <span class="mono font-semibold" id="orderDiscount">- Rp 0</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center pt-2 border-t" style="border-color:var(--border);">
                            <span class="font-bold text-base" style="color:var(--ink);">Total:</span>
                            <span class="font-bold text-base" id="orderTotal" style="color:var(--orange);">Rp 0</span>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary btn-primary-block">Simpan Form DP</button>
                </form>
            @endif
        </section>

        <!-- Panel 4: Tagih Piutang -->
        <section id="panel-4" class="step-panel {{ $currentStep === 4 ? 'is-visible' : '' }}">
            <div class="panel-heading">
                <span class="panel-title">Tagih Piutang</span>
                @if($visit->collections()->count() > 0) <span class="panel-status-pill done">Rp {{ number_format($visit->collections()->sum('amount'), 0, ',', '.') }}</span> @else <span class="panel-status-pill pending">{{ $receivables->count() }} tagihan</span> @endif
            </div>
            @if($visit->collections()->count() > 0)
                <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6L9 17l-5-5"></path></svg>
                    <div class="empty-title">Penagihan tercatat</div>
                    <div class="empty-sub">Total diterima Rp {{ number_format($visit->collections()->sum('amount'), 0, ',', '.') }}</div>
                </div>
                <button onclick="switchStep(5)" class="btn-primary btn-primary-block">Lanjut ke Check-out →</button>
            @else
                @if($tasks->count() > 0)
                    <div class="mb-4 p-3 rounded-xl border-2 border-dashed" style="border-color:var(--border); background:var(--paper-dim);">
                        <div class="text-sm font-bold mb-2 flex items-center gap-1" style="color:var(--ink);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                            Dokumen Invoice Penagihan
                        </div>
                        <div class="space-y-2">
                            @foreach($tasks as $t)
                                <a href="{{ asset('storage/' . $t->attachment) }}" target="_blank" class="flex items-center gap-2 font-semibold mb-2 hover:underline" style="font-size:14.5px; color:#dc2626; min-height:36px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                                    {{ $t->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($receivables->count() > 0)
                    <div class="space-y-4">
                        @foreach($receivables as $r)
                            <div class="receivable-card">
                                <div class="flex justify-between items-center mb-1" style="font-size:13.5px;">
                                    <span class="font-bold" style="color:var(--ink);">{{ $r->reference_code }}</span>
                                    <span class="badge {{ $r->status == 'overdue' ? 'badge-red' : 'badge-amber' }}">{{ $r->status == 'overdue' ? 'Overdue' : 'Tagihan' }}</span>
                                </div>
                                <div class="mb-3" style="font-size:13px; color:var(--slate);">Jatuh Tempo: {{ \Carbon\Carbon::parse($r->due_date)->format('d M Y') }}</div>
                                <div class="flex justify-between items-center mb-3" style="font-size:14.5px;">
                                    <span>Total: <span class="mono font-bold">{{ number_format($r->total_amount, 0, ',', '.') }}</span></span>
                                    <span>Sisa: <span class="mono font-bold" style="color:var(--red);">{{ number_format($r->remaining_amount, 0, ',', '.') }}</span></span>
                                </div>
                                <form action="{{ route('salesman.visits.collection', $visit) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="receivable_id" value="{{ $r->id }}">
                                    <div class="mb-3">
                                        <input type="number" name="amount" placeholder="Jumlah Bayar" class="field-lg" max="{{ $r->remaining_amount }}" inputmode="numeric" required>
                                    </div>
                                    <div class="mb-3">
                                        <select name="payment_method" class="field-lg">
                                            <option value="cash">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="qris">QRIS</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <input type="file" name="payment_proof" accept="image/*" class="file-lg">
                                    </div>
                                    <button type="submit" class="btn-primary btn-primary-block" style="margin-top:0; background:var(--ink);">Bayar Tagihan Ini</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6L9 17l-5-5"></path></svg>
                        <div class="empty-title">Tidak ada tagihan</div>
                        <div class="empty-sub">Toko ini lunas, tidak ada piutang berjalan.</div>
                    </div>
                    <button onclick="switchStep(5)" class="btn-primary btn-primary-block">Lanjut ke Check-out →</button>
                @endif
            @endif
        </section>

        <!-- Panel 5: Check-out -->
        <section id="panel-5" class="step-panel {{ $currentStep === 5 ? 'is-visible' : '' }}">
            <div class="panel-heading">
                <span class="panel-title">Check-out</span>
                @if($visit->check_out_at) <span class="panel-status-pill done">Selesai</span> @else <span class="panel-status-pill pending">Menunggu</span> @endif
            </div>
            <div class="summary-list">
                <div class="summary-row"><span class="label">Cek Produk</span><span class="value {{ $stepDone[2] ? 'ok' : 'pending' }}">{{ $stepDone[2] ? '✓ Selesai' : 'Belum' }}</span></div>
                <div class="summary-row"><span class="label">Order</span><span class="value {{ $stepDone[3] ? 'ok' : 'pending' }}">{{ $stepDone[3] ? '✓ Selesai' : 'Belum' }}</span></div>
                <div class="summary-row"><span class="label">Penagihan</span><span class="value {{ $stepDone[4] ? 'ok' : 'pending' }}">{{ $stepDone[4] ? '✓ Selesai' : 'Belum' }}</span></div>
            </div>
            @if($visit->check_out_at)
                <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6L9 17l-5-5"></path></svg>
                    <div class="empty-title">Kunjungan selesai</div>
                    <div class="empty-sub">Check-out tercatat pukul {{ $visit->check_out_at->format('H:i') }}.</div>
                </div>
            @else
                <form action="{{ route('salesman.visits.checkout', $visit) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary btn-primary-block" style="background:var(--red);">Check-out Sekarang</button>
                </form>
            @endif
        </section>

    </div>
</div>

<!-- Modal Check-In -->
<div id="checkInModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50" style="padding:20px;">
    <div class="bg-white modal-box w-full max-w-md mx-4">
        <h3 class="font-semibold mb-3">Konfirmasi Check-In</h3>
        <p class="text-gray-500 mb-4">Pastikan Anda berada di lokasi toko.</p>
        <form id="checkInForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Bukti Foto</label>
                <input type="file" name="photo" accept="image/*" capture="environment" class="file-lg" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCheckInModal()" class="btn-outline-green" style="border-color:var(--slate); color:var(--slate);">Batal</button>
                <button type="submit" class="btn-primary">Kirim</button>
            </div>
        </form>
        <p id="gpsStatus" class="text-blue-500 mt-3" style="font-size:13px;"></p>
    </div>
</div>

<!-- MODAL PENGAJUAN REVISI -->
<div id="revisionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-end z-50" style="padding:10px;">
    <div class="bg-white rounded-t-2xl p-5 w-full max-w-md mx-auto modal-box">
        <h3 class="font-bold text-lg mb-2">Pengajuan Revisi Order</h3>
        <p class="text-xs mb-4" style="color:var(--slate);">
            Order ini sudah selesai / lewat batas waktu edit. Harap ajukan revisi dengan alasan yang jelas. Admin akan memverifikasi.
        </p>

        <form id="revisionForm" action="" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Alasan Revisi (Wajib)</label>
                <textarea name="reason" rows="4" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" placeholder="Contoh: Salah input qty 1 dus, barang rusak, dll." required></textarea>
            </div>

            <button type="submit" class="btn-primary w-full text-sm" style="padding:14px; border-radius:12px; font-weight:700;">Kirim Pengajuan</button>
            <button type="button" onclick="closeRevisionModal()" class="w-full text-xs mt-2 p-2" style="color:var(--slate);">Batal</button>
        </form>
    </div>
</div>

<script>
    function switchStep(n) {
        document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('is-visible'));
        document.getElementById('panel-' + n).classList.add('is-visible');

        document.querySelectorAll('.step-tab').forEach(t => t.classList.remove('is-current'));
        document.querySelector('.step-tab[data-step="' + n + '"]').classList.add('is-current');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function filterList(listId, query) {
        let list = document.getElementById(listId);
        if(!list) return;
        let items = list.querySelectorAll('.product-item');
        let q = query.toLowerCase();
        let visibleCount = 0;
        
        items.forEach(item => {
            if (item.dataset.name.includes(q)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        let emptyMsg = document.getElementById(listId + '-empty');
        if(emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        
        let countEl = document.getElementById(listId + '-count');
        if(countEl) countEl.textContent = q ? (visibleCount + ' produk cocok') : (items.length + ' produk');
    }

    function updateQty(btn, delta) {
        let input = btn.parentElement.querySelector('input[type="number"]');
        let currentVal = parseInt(input.value) || 0;
        let newVal = currentVal + delta;
        if (newVal < 0) newVal = 0;
        input.value = newVal;
        calcTotal();
    }

    function calcTotal() {
        let subtotal = 0;
        let inputs = document.querySelectorAll('#order-list input[type="number"]');
        inputs.forEach(input => {
            let qty = parseInt(input.value) || 0;
            let price = parseInt(input.dataset.price);
            subtotal += (qty * price);
        });

        document.getElementById('orderSubtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');

        let discountEl = document.getElementById('orderDiscount');
        let discountAmount = 0;
        if (discountEl) {
            let discountText = discountEl.previousElementSibling.textContent;
            let percent = parseFloat(discountText.match(/(\d+)%/)[1]);
            discountAmount = (subtotal * percent) / 100;
            discountEl.textContent = '- Rp ' + discountAmount.toLocaleString('id-ID');
        }

        let total = subtotal - discountAmount;
        document.getElementById('orderTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
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
            navigator.geolocation.getCurrentPosition(function (position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                gpsStatus.textContent = 'Lokasi didapat! Silakan ambil foto.';
            }, function (error) {
                gpsStatus.innerHTML = 'Gagal mengambil GPS. <button type="button" onclick="useDummyGPS()" style="color:var(--orange); font-weight:bold; text-decoration:underline;">Gunakan Dummy</button>';
            });
        }
    }

    function useDummyGPS() {
        document.getElementById('latitude').value = -6.200000;
        document.getElementById('longitude').value = 106.816666;
        document.getElementById('gpsStatus').textContent = 'Menggunakan koordinat dummy. Silakan ambil foto.';
    }

    function closeCheckInModal() {
        checkInModal.classList.add('hidden');
        checkInModal.classList.remove('flex');
    }

    // Fungsi Modal Revisi Order
    function openRevisionModal(orderId) {
        document.getElementById('revisionForm').action = `/salesman/orders/${orderId}/revision`;
        document.getElementById('revisionModal').classList.remove('hidden');
        document.getElementById('revisionModal').classList.add('flex');
    }

    function closeRevisionModal() {
        document.getElementById('revisionModal').classList.add('hidden');
        document.getElementById('revisionModal').classList.remove('flex');
    }

    // Intercept Form Order
    document.getElementById('orderForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const url = form.action;
        let hasItems = false;

        // Cek dan disable input dengan qty 0 atau kosong agar tidak dikirim ke server
        document.querySelectorAll('#order-list .product-item').forEach(itemDiv => {
            const qtyInput = itemDiv.querySelector('input[type="number"]');
            const idInput = itemDiv.querySelector('input[type="hidden"]');
            if (qtyInput && idInput) {
                if (parseInt(qtyInput.value) > 0) {
                    hasItems = true;
                } else {
                    idInput.disabled = true;
                    qtyInput.disabled = true;
                }
            }
        });

        if (!hasItems) {
            alert('Anda belum memasukkan kuantitas untuk produk apapun.');
            // Re-enable disabled inputs so user can try again
            document.querySelectorAll('#order-list input[disabled]').forEach(input => input.disabled = false);
            return;
        }

        if (!navigator.onLine) {
            const formData = new FormData(form);
            const fields = {};
            formData.forEach((value, key) => {
                if (key !== '_token') fields[key] = value;
            });

            await localDB.setItem('draft_order_' + Date.now(), {
                url: url,
                fields: fields
            });

            alert('Mode Offline: Data Order berhasil disimpan di perangkat.');
            form.reset();
            switchStep(2); // Kembali ke langkah sebelumnya
            return;
        }

        // Jika online, submit seperti biasa
        form.submit();
    });

    // Intercept Form Collection (Tagih Piutang)
    document.querySelectorAll('form[action*="/collection"]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = this.action;

            if (!navigator.onLine) {
                const fields = {};
                formData.forEach((value, key) => {
                    if (key !== 'payment_proof') fields[key] = value;
                });

                // Handle file bukti transfer
                let base64File = null;
                const fileInput = this.querySelector('input[type="file"]');
                if (fileInput && fileInput.files[0]) {
                    base64File = await new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result);
                        reader.readAsDataURL(fileInput.files[0]);
                    });
                }

                await localDB.setItem('draft_collection_' + Date.now(), {
                    url: url,
                    fields: fields,
                    fileBase64: base64File,
                    fileKey: 'payment_proof'
                });

                alert('Mode Offline: Data Penagihan berhasil disimpan di perangkat.');
                return;
            }
            this.submit();
        });
    });
</script>
@endsection