@extends('layouts.app')

@section('title', 'Detail Mitra')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="display text-2xl">Detail Mitra</h2>
        <p class="text-sm" style="color:var(--slate);">Informasi lengkap toko mitra.</p>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn-outline">← Kembali</a>
</div>

@if (session('success'))
    <div class="card p-4 mb-4" style="background:var(--green-soft); color:var(--green); border:1px solid var(--green);">✅ {{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="card p-4 mb-4" style="background:var(--red-soft); color:var(--red); border:1px solid var(--red);">⚠️ {{ session('error') }}</div>
@endif

<!-- Info Dasar Mitra -->
<div class="card p-5 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Nama Toko</div>
        <div class="text-lg font-bold" style="color:var(--ink);">{{ $customer->name }}</div>
        <div class="text-xs mono" style="color:var(--slate);">{{ $customer->customer_code }}</div>
    </div>
    <div>
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Pemilik & Kontak</div>
        <div class="text-sm" style="color:var(--ink);">{{ $customer->owner_name ?? '-' }}</div>
        <div class="text-xs" style="color:var(--slate);">{{ $customer->phone_number ?? '-' }}</div>
    </div>
    <div class="md:col-span-2">
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Alamat</div>
        <div class="text-sm" style="color:var(--ink);">{{ $customer->address ?? '-' }}</div>
        @if($customer->latitude && $customer->longitude)
            <a href="https://www.google.com/maps?q={{ $customer->latitude }},{{ $customer->longitude }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Lihat di Google Maps</a>
        @endif
    </div>
    <div>
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Limit Kredit</div>
        <div class="text-sm font-bold" style="color:var(--ink);">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</div>
    </div>
    <div>
        <div class="text-xs uppercase tracking-wider font-bold mb-1" style="color:var(--slate);">Termin Pembayaran</div>
        <div class="text-sm font-bold" style="color:var(--ink);">{{ $customer->credit_terms_days }} Hari</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Status Diskon Reseller -->
    <div class="card p-5 mb-6">
        <div class="text-lg font-bold mb-4" style="color:var(--ink);">Skema Diskon Reseller</div>
        
        @if($discount)
            <div class="p-4 rounded-xl mb-4" style="background: {{ $discount->is_approved ? 'var(--green-soft)' : '#FBEFD9' }};">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase font-bold" style="color:var(--slate);">Nilai Diskon Saat Ini</div>
                        <div class="text-2xl font-bold" style="color: {{ $discount->is_approved ? 'var(--green)' : 'var(--amber)' }};">{{ $discount->discount_value }}%</div>
                        <div class="text-xs font-bold mt-1" style="color: {{ $discount->is_approved ? 'var(--green)' : 'var(--amber)' }};">
                            @if($discount->is_approved) ✓ Disetujui & Aktif @else ⏳ Menunggu Approval @endif
                        </div>
                    </div>
                    
                    @if(!$discount->is_approved)
                    <div class="flex gap-2">
                        <form action="{{ route('admin.customers.discount.approve', [$customer, $discount]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn text-xs" style="background:var(--green);">Approve</button>
                        </form>
                        <form action="{{ route('admin.customers.discount.reject', [$customer, $discount]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-outline text-xs" style="border-color:var(--red); color:var(--red);">Reject</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm mb-4" style="color:var(--slate);">Belum ada diskon aktif untuk mitra ini.</p>
        @endif

        <!-- Form Set/Ubah Diskon oleh Admin -->
        <div class="border-t pt-4" style="border-color:var(--border);">
            <form action="{{ route('admin.customers.discount.store', $customer) }}" method="POST" class="flex flex-col sm:flex-row items-end gap-2 mb-2">
                @csrf
                <div class="flex-1 w-full">
                    <label class="text-xs font-bold uppercase block mb-1" style="color:var(--slate);">{{ $discount ? 'Ubah Diskon (%)' : 'Set Diskon Manual (%)' }}</label>
                    <input type="number" name="discount_value" min="0" max="100" value="{{ $discount->discount_value ?? '' }}" placeholder="Contoh: 10" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
                <button type="submit" class="btn text-sm w-full sm:w-auto" style="padding: 10px 20px;">Simpan Diskon</button>
            </form>
            
            @if($discount)
            <form action="{{ route('admin.customers.discount.destroy', [$customer, $discount]) }}" method="POST" onsubmit="return confirm('Hapus diskon ini permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-red-600 hover:underline mt-2">Hapus Diskon Permanen</button>
            </form>
            @endif
        </div>
    </div>

    <!-- Statistik Pembelian (Pie Chart dengan Persentase) -->
    <div class="card p-5">
        <div class="text-lg font-bold mb-4" style="color:var(--ink);">Statistik Tipe Pembelian</div>
        @php $totalPurchases = $paymentStats->sum(); @endphp
        @if($totalPurchases > 0)
            <div class="flex flex-col md:flex-row items-center">
                <div style="width: 200px; height: 200px; position: relative;">
                    <canvas id="paymentChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <div class="text-[10px] uppercase font-bold" style="color:var(--slate);">Total</div>
                        <div class="text-sm font-bold" style="color:var(--ink);">Rp {{ number_format($totalPurchases / 1000000, 1) }}jt</div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 md:ml-6 space-y-3 w-full">
                    @foreach($paymentStats as $type => $total)
                        @php $percentage = $totalPurchases > 0 ? round(($total / $totalPurchases) * 100, 1) : 0; @endphp
                        <div class="p-2 rounded-lg" style="background:var(--paper-dim);">
                            <div class="flex justify-between items-center text-sm mb-1">
                                <span class="font-semibold capitalize flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background: {{ $type == 'cash' ? '#2F6F4F' : ($type == 'konsinyasi' ? '#E8B23C' : '#C23B22') }}"></span>
                                    {{ $type }}
                                </span>
                                <span class="font-bold text-xs" style="color:var(--ink);">{{ $percentage }}%</span>
                            </div>
                            <div class="mono text-xs font-bold text-right" style="color:var(--slate);">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-sm text-center py-8" style="color:var(--slate);">Belum ada data pembelian.</p>
        @endif
    </div>
</div>

<!-- Stok Produk di Mitra -->
<div class="card p-5 mb-6">
    <div class="text-lg font-bold mb-4" style="color:var(--ink);">Stok Produk di Mitra</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4 text-xs">Produk</th>
                    <th class="p-4 text-xs text-center">Konsinyasi</th>
                    <th class="p-4 text-xs text-center">Lunas (Cash)</th>
                    <th class="p-4 text-xs text-center">Piutang</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stocks as $s)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 text-sm font-semibold">{{ $s->product->name }}</td>
                    <td class="p-4 text-center text-sm font-bold text-yellow-600">{{ $s->qty_konsinyasi }} pcs</td>
                    <td class="p-4 text-center text-sm font-bold text-green-600">{{ $s->qty_lunas }} pcs</td>
                    <td class="p-4 text-center text-sm font-bold text-red-600">{{ $s->qty_piutang }} pcs</td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-8 text-center text-sm" style="color:var(--slate);">Belum ada stok tercatat di toko ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Produk Terlaris di Mitra Ini -->
<div class="card p-5 mb-6">
    <div class="text-lg font-bold mb-4" style="color:var(--ink);">Produk Paling Banyak Dibeli</div>
    <div class="space-y-3">
        @forelse ($topProducts as $item)
            @php $maxQty = $topProducts->first()->total_qty; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold">{{ $item->product->name }}</span>
                    <span class="mono">{{ $item->total_qty }} pcs</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($item->total_qty / max(1, $maxQty)) * 100 }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-center py-4" style="color:var(--slate);">Belum ada pembelian.</p>
        @endforelse
    </div>
</div>

<!-- Riwayat Pembelian (Orders) -->
<div class="card p-5 mb-6">
    <div class="text-lg font-bold mb-4" style="color:var(--ink);">Riwayat Pembelian</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                    <th class="p-4 text-xs">Kode Order</th>
                    <th class="p-4 text-xs">Salesman</th>
                    <th class="p-4 text-xs">Total</th>
                    <th class="p-4 text-xs">Tipe Bayar</th>
                    <th class="p-4 text-xs">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $o)
                <tr class="border-b" style="border-color:var(--border);">
                    <td class="p-4 mono text-xs">{{ $o->order_code }}</td>
                    <td class="p-4 text-sm">{{ $o->employee->full_name ?? '-' }}</td>
                    <td class="p-4 mono text-sm">Rp {{ number_format($o->total_amount, 0, ',', '.') }}</td>
                    <td class="p-4">
                        <span class="badge {{ $o->payment_type == 'cash' ? 'badge-green' : ($o->payment_type == 'konsinyasi' ? 'badge-amber' : 'badge-red') }}">
                            {{ ucfirst($o->payment_type) }}
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="badge {{ $o->status == 'delivered' ? 'badge-green' : 'badge-slate' }}">{{ $o->status }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-sm" style="color:var(--slate);">Belum ada riwayat pembelian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</div>

<!-- Riwayat Piutang & Penagihan -->
<div class="card p-5">
    <div class="text-lg font-bold mb-4" style="color:var(--ink);">Riwayat Piutang & Penagihan</div>
    @if($receivables->count() > 0)
    <div class="space-y-4">
        @foreach($receivables as $r)
            @php $isUnpaid = $r->status != 'paid'; @endphp
            
            <!-- Bungkus dengan link jika belum lunas -->
            <a href="{{ $isUnpaid ? route('admin.tasks.create', ['customer_id' => $r->customer_id]) : '#' }}" class="block hover:bg-gray-50 transition-colors rounded-xl">
                <div class="border rounded-xl p-4" style="border-color: {{ $isUnpaid ? 'var(--red)' : 'var(--border)' }}; background: {{ $isUnpaid ? 'var(--red-soft)' : '#fff' }};">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-sm">{{ $r->reference_code }}</span>
                        <div class="flex items-center gap-2">
                            @if($isUnpaid)
                                <span class="text-xs font-bold text-red-600 bg-white px-2 py-1 rounded-full border" style="border-color:var(--red);">+ Buat Tugas Tagih</span>
                            @endif
                            <span class="badge {{ $r->status == 'overdue' ? 'badge-red' : ($r->status == 'paid' ? 'badge-green' : 'badge-amber') }}">{{ $r->status }}</span>
                        </div>
                    </div>
                    <div class="text-xs mb-3" style="color:var(--slate);">Jatuh Tempo: {{ \Carbon\Carbon::parse($r->due_date)->format('d M Y') }}</div>
                    <div class="flex justify-between items-center text-sm mb-4">
                        <div>
                            <div style="color:var(--slate);">Total Piutang</div>
                            <div class="font-bold">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right">
                            <div style="color:var(--slate);">Sisa Tagihan</div>
                            <div class="font-bold text-red-600">Rp {{ number_format($r->total_amount - $r->paid_amount, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    
                    @if($r->collections->count() > 0)
                    <div class="border-t pt-3" style="border-color:var(--border);">
                        <div class="text-xs font-bold mb-2" style="color:var(--slate);">Riwayat Pembayaran:</div>
                        <div class="space-y-2">
                            @foreach($r->collections as $c)
                            <div class="flex justify-between items-center text-xs">
                                <span>{{ $c->payment_date->format('d M Y') }} - {{ $c->employee->full_name }}</span>
                                <span class="font-bold text-green-600">Rp {{ number_format($c->amount, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
    @else
    <p class="text-sm text-center py-4" style="color:var(--slate);">Toko ini tidak memiliki riwayat piutang.</p>
    @endif
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('paymentChart');
    if (ctx) {
        const paymentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Cash', 'Konsinyasi', 'Piutang'],
                datasets: [{
                    label: 'Total Pembelian',
                    data: [
                        {{ $paymentStats['cash'] ?? 0 }},
                        {{ $paymentStats['konsinyasi'] ?? 0 }},
                        {{ $paymentStats['piutang'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#2F6F4F', // green
                        '#E8B23C', // amber
                        '#C23B22'  // red
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
</script>

@endsection