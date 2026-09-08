<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1B2A41; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #1B2A41; font-size: 24px; }
        .header p { margin: 5px 0 0 0; font-size: 14px; color: #666; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: bold; color: #1B2A41; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #F6F2E9; color: #1B2A41; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; color: #fff; }
        .badge-green { background-color: #2F6F4F; }
        .badge-amber { background-color: #B8860B; }
        .badge-red { background-color: #C23B22; }
        .text-right { text-align: right; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px;}
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN PERFORMA SALESMAN</h1>
        <p>PT Ath-Thoifah &bull; Periode: {{ now()->translatedFormat('F Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informasi Salesman</div>
        <table>
            <tr>
                <th width="20%">Nama</th>
                <td width="30%">{{ $employee->full_name }}</td>
                <th width="20%">Kode</th>
                <td width="30%">{{ $employee->employee_code }}</td>
            </tr>
            <tr>
                <th>Area</th>
                <td>{{ $employee->salesArea->name ?? '-' }}</td>
                <th>Tipe</th>
                <td>{{ ucfirst($employee->type) }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $employee->user->email ?? '-' }}</td>
                <th>Status</th>
                <td>{{ ucfirst($employee->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Pencapaian Target & Realisasi</div>
        @if($target)
        <table>
            <thead>
                <tr>
                    <th>Metrik</th>
                    <th class="text-right">Realisasi</th>
                    <th class="text-right">Target</th>
                    <th class="text-right">Pencapaian (%)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Kunjungan</td>
                    <td class="text-right">{{ $stats['visits'] }}</td>
                    <td class="text-right">{{ $target->visit_target }}</td>
                    <td class="text-right">{{ $metrics['visit'] }}%</td>
                    <td>
                        @if($metrics['visit'] >= 70) <span class="badge badge-green">Baik</span>
                        @elseif($metrics['visit'] >= 50) <span class="badge badge-amber">Cukup</span>
                        @else <span class="badge badge-red">Kurang</span> @endif
                    </td>
                </tr>
                <tr>
                    <td>Order</td>
                    <td class="text-right">{{ $stats['orders'] }}</td>
                    <td class="text-right">{{ $target->order_target }}</td>
                    <td class="text-right">{{ $metrics['order'] }}%</td>
                    <td>
                        @if($metrics['order'] >= 70) <span class="badge badge-green">Baik</span>
                        @elseif($metrics['order'] >= 50) <span class="badge badge-amber">Cukup</span>
                        @else <span class="badge badge-red">Kurang</span> @endif
                    </td>
                </tr>
                <tr>
                    <td>Nilai Penjualan (Rp)</td>
                    <td class="text-right">{{ number_format($stats['sales'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($target->sales_target, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $metrics['sales'] }}%</td>
                    <td>
                        @if($metrics['sales'] >= 70) <span class="badge badge-green">Baik</span>
                        @elseif($metrics['sales'] >= 50) <span class="badge badge-amber">Cukup</span>
                        @else <span class="badge badge-red">Kurang</span> @endif
                    </td>
                </tr>
                <tr>
                    <td>Collection (Rp)</td>
                    <td class="text-right">{{ number_format($stats['collections'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($target->collection_target, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $metrics['collection'] }}%</td>
                    <td>
                        @if($metrics['collection'] >= 70) <span class="badge badge-green">Baik</span>
                        @elseif($metrics['collection'] >= 50) <span class="badge badge-amber">Cukup</span>
                        @else <span class="badge badge-red">Kurang</span> @endif
                    </td>
                </tr>
            </tbody>
        </table>
        @else
        <p style="text-align:center; padding:10px; background:#eee;">Belum ada target ditetapkan untuk periode ini.</p>
        @endif
    </div>

    @if($employee->type == 'online')
    <div class="section">
        <div class="section-title">Riwayat Laporan Online Bulan Ini</div>
        @if($monthlyOnlineReports->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Kerja</th>
                    <th>Catatan</th>
                    <th class="text-right">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyOnlineReports as $rep)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($rep->report_date)->format('d M Y') }}</td>
                    <td>{{ $rep->start_time }} - {{ $rep->end_time }}</td>
                    <td>{{ $rep->notes ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($rep->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="text-align:center; padding:10px; background:#eee;">Tidak ada laporan online bulan ini.</p>
        @endif
    </div>
    @else
    <div class="section">
        <div class="section-title">Riwayat Kunjungan Bulan Ini</div>
        @if($monthlyVisits->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Toko Mitra</th>
                    <th>Waktu Check-in</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyVisits as $v)
                <tr>
                    <td>{{ $v->customer->name ?? '-' }}</td>
                    <td>{{ $v->check_in_at->format('d M Y H:i') }}</td>
                    <td>{{ $v->check_out_at ? 'Selesai' : 'Sedang Visit' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="text-align:center; padding:10px; background:#eee;">Tidak ada kunjungan bulan ini.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Riwayat Order Bulan Ini</div>
        @if($monthlyOrders->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Kode Order</th>
                    <th>Toko</th>
                    <th>Tanggal</th>
                    <th class="text-right">Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyOrders as $o)
                <tr>
                    <td>{{ $o->order_code }}</td>
                    <td>{{ $o->customer->name ?? '-' }}</td>
                    <td>{{ $o->created_at->format('d M Y') }}</td>
                    <td class="text-right">Rp {{ number_format($o->total_amount, 0, ',', '.') }}</td>
                    <td>{{ $o->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="text-align:center; padding:10px; background:#eee;">Tidak ada order bulan ini.</p>
        @endif
    </div>
    @endif

    <div class="footer">
        Dokumen ini dicetak secara otomatis pada {{ now()->format('d M Y H:i') }} dari Sistem Sales Operations Ath-Thoifah.
    </div>

</body>
</html>