<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #292524; background: #fff; }

        .wrapper { max-width: 580px; margin: 0 auto; padding: 36px 32px; }

        .header-table { width: 100%; margin-bottom: 4px; table-layout: fixed; }
        .header-table td { padding: 0; }
        .header-table .logo-cell { width: 150px; vertical-align: middle; }
        .header-table .logo-cell.kiri { text-align: left; }
        .header-table .logo-cell.kanan { text-align: right; }
        .header-table .logo-cell.kiri img { width: 110px; height: auto; }
        .header-table .logo-cell.kanan img { width: 135px; height: auto; }
        .header-table .judul-cell { vertical-align: middle; text-align: center; }

        .judul { text-align: center; font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .judul .laba { color: #292524; }
        .judul .rugi { color: #a31d22; }
        .perusahaan { text-align: center; font-size: 15px; font-weight: 700; color: #a31d22; margin-bottom: 2px; letter-spacing: 0.2px; }
        .kode { text-align: center; font-size: 10px; font-weight: 700; color: #e3a93b; letter-spacing: 3px; margin-bottom: 10px; }
        .periode { text-align: center; font-size: 10.5px; color: #78716c; margin-bottom: 18px; }

        hr.tebal { border: none; border-top: 2.5px solid #a31d22; margin-bottom: 4px; }
        hr.tipis { border: none; border-top: 1px solid #e7e0d3; margin: 4px 0; }

        table.laporan { width: 100%; border-collapse: collapse; }
        table.laporan td { padding: 5px 0; font-size: 11.5px; vertical-align: middle; }
        table.laporan td.label { color: #44403c; }
        table.laporan td.label.indent { padding-left: 18px; color: #57534e; }
        table.laporan td.nilai { text-align: right; color: #292524; white-space: nowrap; width: 140px; }

        table.detail { width: 100%; border-collapse: collapse; margin-bottom: 2px; table-layout: fixed; }
        table.detail th {
            font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.04em;
            color: #a8a29e; font-weight: 700; text-align: left;
            padding: 4px 8px 4px 0; border-bottom: 1px solid #e7e0d3;
            word-wrap: break-word;
        }
        table.detail th.kanan { text-align: right; }
        table.detail td { font-size: 11px; padding: 4px 8px 4px 0; color: #44403c; border-bottom: 1px solid #f2ede1; word-wrap: break-word; }
        table.detail td.kanan { text-align: right; white-space: nowrap; color: #292524; }
        table.detail td.kosong { text-align: center; color: #a8a29e; font-style: italic; padding: 10px 0; }

        tr.subtotal-row td { font-weight: 700; padding-top: 6px; border-top: 1px solid #e3a93b; border-bottom: none; }
        tr.subtotal-row td.kanan { color: #292524; }

        tr.section-title td { font-weight: 700; padding-top: 12px; padding-bottom: 4px; color: #a31d22; text-transform: uppercase; font-size: 10.5px; letter-spacing: 0.05em; }
        tr.total-row td { font-weight: 700; border-top: 1px solid #e3a93b; padding-top: 6px; }
        tr.grand-row td { font-weight: 700; font-size: 13px; background: #faf6ee; padding: 12px 10px; }
        tr.grand-row td.label { border-radius: 6px 0 0 6px; color: #a31d22; }
        tr.grand-row td.nilai { border-radius: 0 6px 6px 0; }
        .negatif { color: #a31d22; }
        .positif { color: #1f7a4d; }

        .catatan-sponsor { font-size: 9.5px; color: #a8a29e; font-style: italic; padding: 4px 0 2px; }

        .footer { margin-top: 28px; padding-top: 10px; border-top: 1px solid #e7e0d3; font-size: 9.5px; color: #a8a29e; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">

    <table class="header-table">
        <tr>
            <td class="logo-cell kiri">
                <img src="{{ public_path('image/logo-sjm.png') }}" alt="Susu Jahe Merah 191">
            </td>
            <td class="judul-cell">
                <div class="judul"><span class="laba">Laporan </span><p><span class="rugi">Laba &amp; Rugi</span></p></div>
                <div class="perusahaan">CV. Susu Jahe Merah</div>
                <div class="kode">191</div>
            </td>
            <td class="logo-cell kanan">
                <img src="{{ public_path('image/logo-foyu.png') }}" alt="FOYU Krimer Kental Manis">
            </td>
        </tr>
    </table>
    <div class="periode">Periode {{ $periode }}</div>
    <hr class="tebal">
    <table class="laporan">
        <tr class="section-title"><td colspan="2">Pendapatan</td></tr>
    </table>

    <table class="detail">
        <tr>
            <th style="width:20%;">Tanggal</th>
            <th class="kanan" style="width:26.6%;">Total Harga</th>
            <th class="kanan" style="width:26.6%;">Bayar</th>
            <th class="kanan" style="width:26.8%;">Kembalian</th>
        </tr>
        @forelse($transaksi as $t)
        <tr>
            <td>{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d/m/Y') }}</td>
            <td class="kanan">{{ number_format($t->total_harga, 0, ',', '.') }}</td>
            <td class="kanan">{{ number_format($t->bayar, 0, ',', '.') }}</td>
            <td class="kanan">{{ number_format($t->kembalian, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="kosong">Tidak ada transaksi</td>
        </tr>
        @endforelse
        <tr class="subtotal-row">
            <td>Total Pendapatan</td>
            <td class="kanan">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="laporan">
        <tr class="section-title"><td colspan="2">Belanja Bahan</td></tr>
    </table>

    <table class="detail">
        <tr>
            <th style="width:15%;">Tanggal</th>
            <th style="width:33%;">Nama Barang</th>
            <th class="kanan" style="width:15%;">Jumlah</th>
            <th class="kanan" style="width:18%;">Harga Beli</th>
            <th class="kanan" style="width:19%;">Subtotal</th>
        </tr>
        @forelse($pengeluaran as $p)
        <tr>
            <td>{{ \Carbon\Carbon::parse($p->tanggal_masuk)->format('d/m/Y') }}</td>
            <td>{{ $p->nama_barang }}</td>
            <td class="kanan">{{ $p->jumlah_dibeli }} {{ $p->satuan }}</td>
            <td class="kanan">{{ number_format($p->harga_beli ?? 0, 0, ',', '.') }}</td>
            <td class="kanan">{{ number_format(($p->harga_beli ?? 0) * $p->jumlah_dibeli, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="kosong">Tidak ada pengeluaran</td>
        </tr>
        @endforelse
        <tr class="subtotal-row">
            <td colspan="4">Total Beban Belanja Bahan</td>
            <td class="kanan">({{ number_format($totalPengeluaran, 0, ',', '.') }})</td>
        </tr>
    </table>

    <table class="laporan">
        <tr class="section-title"><td colspan="2">Barang dari Sponsor</td></tr>
    </table>

    <table class="detail">
        <tr>
            <th style="width:13%;">Tanggal</th>
            <th style="width:20%;">Nama Barang</th>
            <th class="kanan" style="width:10%;">Jumlah</th>
            <th class="kanan" style="width:15%;">Estimasi Harga</th>
            <th class="kanan" style="width:15%;">Subtotal</th>
            <th style="width:27%;">Keterangan</th>
        </tr>
        @forelse($sponsor as $s)
        <tr>
            <td>{{ \Carbon\Carbon::parse($s->tanggal_masuk)->format('d/m/Y') }}</td>
            <td>{{ $s->nama_barang }}</td>
            <td class="kanan">{{ $s->jumlah_dibeli }} {{ $s->satuan }}</td>
            <td class="kanan">{{ $s->harga_beli ? number_format($s->harga_beli, 0, ',', '.') : '-' }}</td>
            <td class="kanan">{{ $s->harga_beli ? number_format($s->harga_beli * $s->jumlah_dibeli, 0, ',', '.') : '-' }}</td>
            <td>{{ $s->keterangan ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="kosong">Tidak ada barang sponsor</td>
        </tr>
        @endforelse
        <tr class="subtotal-row">
            <td colspan="4">Total Nilai Barang Sponsor</td>
            <td class="kanan" colspan="2">{{ number_format($totalSponsor, 0, ',', '.') }}</td>
        </tr>
    </table>
    <div class="catatan-sponsor">*Barang sponsor tidak memengaruhi perhitungan Laba/Rugi karena bukan pengeluaran kas.</div>

    <table class="laporan">
        <tr><td colspan="2" style="padding-top:10px;"></td></tr>
        <tr class="grand-row">
            <td class="label">LABA / (RUGI) BERSIH</td>
            <td class="nilai {{ $labaRugi >= 0 ? 'positif' : 'negatif' }}">
                {{ $labaRugi < 0 ? '(' . number_format(abs($labaRugi), 0, ',', '.') . ')' : number_format($labaRugi, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Susu Jahe Merah 191 &middot; {{ now()->format('d F Y, H:i') }}
    </div>

</div>
</body>
</html>