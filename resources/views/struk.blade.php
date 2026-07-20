<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .struk-wrapper {
            width: 76mm;
            margin-left: 2mm;
            margin-right: 2mm;
            padding: 4mm 0;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
            word-wrap: break-word;
        }
        td.right {
            width: 40%;
        }
        .item-name {
            font-size: 11px;
        }
        .item-detail {
            font-size: 10px;
            color: #333;
        }
        .footer {
            margin-top: 10px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="struk-wrapper">
        <div class="center bold" style="font-size: 14px;">Susu Jahe Merah 191</div>
        <div class="center" style="font-size: 10px;">Struk Pembayaran</div>

        <div class="divider"></div>

        <div>No. Transaksi: #{{ $transaksi->id }}</div>
        <div>Tanggal: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d M Y, H:i') }}</div>
        <div>Kasir: {{ $transaksi->user->name }}</div>

        <div class="divider"></div>

        <table>
            @foreach ($transaksi->detailTransaksi as $detail)
                <tr>
                    <td colspan="2" class="item-name">{{ $detail->produk->nama_produk }}</td>
                </tr>
                <tr>
                    <td class="item-detail">{{ $detail->jumlah }} x Rp{{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="right item-detail">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <table>
            <tr>
                <td class="bold">Total</td>
                <td class="right bold">Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td class="right">Rp{{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="right">Rp{{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="center footer">Terima kasih atas kunjungan Anda!</div>
    </div>
</body>
</html>