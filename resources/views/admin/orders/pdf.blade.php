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
        .text-right { text-align: right; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px;}
    </style>
</head>
<body>

    <div class="header">
        <h1>INVOICE ORDER</h1>
        <p>CV. Ath-Thoifah &bull; {{ $order->created_at->format('d M Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informasi Order</div>
        <table>
            <tr>
                <th width="20%">Kode Order</th>
                <td width="30%">{{ $order->order_code }}</td>
                <th width="20%">Tanggal</th>
                <td width="30%">{{ $order->created_at->format('d M Y') }}</td>
            </tr>
            <tr>
                <th>Toko Mitra</th>
                <td>{{ $order->customer->name }}</td>
                <th>Salesman</th>
                <td>{{ $order->employee->full_name }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td colspan="3">{{ $order->customer->address }}</td>
            </tr>
            <tr>
                <th>Tipe Bayar</th>
                <td>{{ ucfirst($order->payment_type) }}</td>
                <th>Status</th>
                <td>{{ ucfirst($order->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detail Item</div>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="text-right">Harga</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Keseluruhan:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        Dokumen ini dicetak secara otomatis dari Sistem Sales Operations Ath-Thoifah.
    </div>

</body>
</html>