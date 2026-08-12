@extends('layouts.app')

@section('title', 'Laporan Performa')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
    <div>
        <h2 class="display text-2xl">Laporan Performa Salesman</h2>
        <p class="text-sm" style="color:var(--slate);">Rekapitulasi pencapaian target vs realisasi.</p>
    </div>
    <form method="GET" action="">
        <input type="month" name="period" value="{{ $period }}" onchange="this.form.submit()">
    </form>
</div>

<div class="card overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[900px]">
        <thead>
            <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                <th class="p-4" rowspan="2">Salesman</th>
                <th class="p-2 text-center" colspan="2">Kunjungan</th>
                <th class="p-2 text-center" colspan="2">Order</th>
                <th class="p-2 text-center" colspan="2">Sales (Rp)</th>
                <th class="p-2 text-center" colspan="2">Collection (Rp)</th>
            </tr>
            <tr class="bg-gray-50 border-b" style="border-color:var(--border);">
                <th class="p-2 text-xs text-center">Target</th><th class="p-2 text-xs text-center">Actual</th>
                <th class="p-2 text-xs text-center">Target</th><th class="p-2 text-xs text-center">Actual</th>
                <th class="p-2 text-xs text-center">Target</th><th class="p-2 text-xs text-center">Actual</th>
                <th class="p-2 text-xs text-center">Target</th><th class="p-2 text-xs text-center">Actual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $data)
            <tr class="border-b" style="border-color:var(--border);">
                <td class="p-4 font-semibold">{{ $data['employee']->full_name }}</td>
                <td class="p-2 text-center mono">{{ $data['visits']['target'] }}</td>
                <td class="p-2 text-center mono font-bold">{{ $data['visits']['actual'] }}</td>
                <td class="p-2 text-center mono">{{ $data['orders']['target'] }}</td>
                <td class="p-2 text-center mono font-bold">{{ $data['orders']['actual'] }}</td>
                <td class="p-2 text-center mono text-xs">{{ number_format($data['sales']['target'] / 1000000, 1) }}jt</td>
                <td class="p-2 text-center mono font-bold text-xs" style="color:var(--green);">{{ number_format($data['sales']['actual'] / 1000000, 1) }}jt</td>
                <td class="p-2 text-center mono text-xs">{{ number_format($data['collections']['target'] / 1000000, 1) }}jt</td>
                <td class="p-2 text-center mono font-bold text-xs" style="color:var(--green);">{{ number_format($data['collections']['actual'] / 1000000, 1) }}jt</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection