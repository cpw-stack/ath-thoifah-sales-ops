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
        <form action="{{ route('salesman.schedule.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-xs font-bold mb-2" style="color:var(--slate);">PILIH TOKO MITRA</label>
                <select name="customer_id" class="w-full text-sm p-3 rounded-lg border" style="border-color:var(--border);" required>
                    <option value="">Cari & Pilih Toko...</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->customer_code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold mb-2" style="color:var(--slate);">TANGGAL KUNJUNGAN</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="visit_date" value="{{ today()->format('Y-m-d') }}" class="hidden peer" required>
                        <div class="p-3 text-center rounded-lg border text-sm font-semibold peer-checked:bg-[#1B2A41] peer-checked:text-white transition" style="border-color:var(--border);">
                            Hari Ini<br><span class="text-xs font-normal">{{ today()->translatedFormat('d M') }}</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="visit_date" value="{{ today()->addDay()->format('Y-m-d') }}" class="hidden peer">
                        <div class="p-3 text-center rounded-lg border text-sm font-semibold peer-checked:bg-[#1B2A41] peer-checked:text-white transition" style="border-color:var(--border);">
                            Besok<br><span class="text-xs font-normal">{{ today()->addDay()->translatedFormat('d M') }}</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full text-base" style="padding:14px;">
                Kirim Usulan Jadwal
            </button>
        </form>
    </div>
</div>
@endsection