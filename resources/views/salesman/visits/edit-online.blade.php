@extends('layouts.mobile')

@section('content')
<div class="p-5 space-y-5">
    <a href="{{ route('salesman.home') }}" class="text-sm font-semibold flex items-center gap-1 mb-2" style="color:var(--slate);">← Kembali</a>

    <div class="card p-4" style="background:var(--ink); border:none;">
        <div class="flex justify-between items-center">
            <div>
                <div class="text-[11px] uppercase tracking-wider" style="color:#9DAEC7;">Edit Laporan</div>
                <div class="display text-lg mt-1" style="color:#fff;">{{ \Carbon\Carbon::parse($onlineReport->report_date)->translatedFormat('l, d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <form action="{{ route('salesman.online.update', $onlineReport) }}" method="POST" id="onlineForm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Jam Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $onlineReport->start_time->format('H:i')) }}" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Jam Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $onlineReport->end_time->format('H:i')) }}" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Catatan / Aktivitas</label>
                <textarea name="notes" rows="2" class="w-full p-2 text-sm rounded-lg border" style="border-color:var(--border);">{{ old('notes', $onlineReport->notes) }}</textarea>
            </div>

            <div class="mb-2 relative">
                <label class="block text-xs font-bold mb-1" style="color:var(--slate);">Cari Produk</label>
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="productSearchInput" placeholder="Ketik nama produk..." autocomplete="off"
                           onkeydown="return event.key != 'Enter';"
                           oninput="renderSearchResults(this.value)"
                           onfocus="renderSearchResults(this.value)"
                           class="w-full pl-10 pr-3 py-3 text-sm border rounded-lg" style="border-color:var(--border);">
                </div>
                <div id="searchResults" class="hidden absolute left-0 right-0 mt-1 z-20 rounded-lg border shadow-lg max-h-64 overflow-y-auto"
                     style="background:#fff; border-color:var(--border);"></div>
            </div>

            <div id="cartEmpty" class="card p-6 text-center text-sm mb-4 hidden" style="color:var(--slate); background:var(--paper-dim); border:1px dashed var(--border);">
                Belum ada produk dipilih.
            </div>

            <div id="cartList" class="space-y-3 mb-4"></div>

            <div class="flex justify-between items-center mb-4 p-3 rounded-xl" style="background:var(--paper-dim);">
                <span class="font-bold text-base" style="color:var(--ink);">Total Penjualan:</span>
                <span class="font-bold text-base" id="onlineTotal" style="color:var(--orange);">Rp 0</span>
            </div>

            <div id="cartInputs"></div>

            <button type="submit" id="onlineSubmitBtn" class="btn-primary w-full text-sm" style="padding:14px;" disabled>Update Laporan</button>
        </form>
    </div>
</div>

<script>
    const ALL_PRODUCTS = [
        @foreach($products as $p)
        { id: {{ $p->id }}, name: @json($p->name), price: {{ $p->price }} },
        @endforeach
    ];

    // Isi keranjang awal dari data database
    let cart = {};
    @foreach($onlineReport->items as $item)
    cart[{{ $item->product_id }}] = { id: {{ $item->product_id }}, name: @json($item->product->name), price: {{ $item->price }}, qty: {{ $item->qty }} };
    @endforeach

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
            listEl.innerHTML = '';
            emptyEl.classList.remove('hidden');
            inputsEl.innerHTML = '';
            submitBtn.disabled = true;
            document.getElementById('onlineTotal').textContent = 'Rp 0';
            return;
        }

        emptyEl.classList.add('hidden');
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

    // Render cart awal saat halaman dimuat
    renderCart();
</script>
@endsection