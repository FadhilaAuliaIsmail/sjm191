<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiController extends Controller
{
    // Halaman POS - grid produk gaya GoFood
    public function pos()
    {
        $produk = Produk::where('stok', '>', 0)->get()->map(function ($produk) {
            return [
                'id' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'harga' => $produk->harga,
                'stok' => $produk->stok,
                'foto' => $produk->foto_url, // <-- pakai accessor, sudah full URL
            ];
        });

        return Inertia::render('Pos/Index', [
            'produk' => $produk,
        ]);
    }

    public function index(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal', now()->startOfDay()->toDateString());
        $tanggalAkhir = $request->input('tanggal_akhir', now()->toDateString());

        $transaksis = Transaksi::with('user')
            ->whereDate('tanggal_transaksi', '>=', $tanggalAwal)
            ->whereDate('tanggal_transaksi', '<=', $tanggalAkhir)
            ->latest('tanggal_transaksi')
            ->get();

        return Inertia::render('Transaksi/Index', [
            'transaksis' => $transaksis,
            'totalPendapatan' => $transaksis->sum('total_harga'),
            'jumlahTransaksi' => $transaksis->count(),
            'filters' => [
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir,
            ],
        ]);
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load('detailTransaksi.produk', 'user');

        return Inertia::render('Transaksi/Show', [
            'transaksi' => $transaksi,
        ]);
    }

    // Proses transaksi dari keranjang POS
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'bayar' => 'required|numeric|min:0',
        ]);

        $transaksi = DB::transaction(function () use ($validated, $request) {
            $totalHarga = 0;
            $items = [];

            foreach ($validated['items'] as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['produk_id']);

                if ($produk->stok < $item['jumlah']) {
                    abort(422, "Stok {$produk->nama_produk} tidak cukup.");
                }

                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;

                $items[] = [
                    'produk' => $produk,
                    'jumlah' => $item['jumlah'],
                    'harga' => $produk->harga,
                    'subtotal' => $subtotal,
                ];

                $produk->decrement('stok', $item['jumlah']);
            }

            if ($validated['bayar'] < $totalHarga) {
                abort(422, 'Jumlah bayar kurang dari total harga.');
            }

            $transaksi = Transaksi::create([
                'user_id' => $request->user()->id,
                'tanggal_transaksi' => now(),
                'total_harga' => $totalHarga,
                'bayar' => $validated['bayar'],
                'kembalian' => $validated['bayar'] - $totalHarga,
            ]);

            foreach ($items as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $transaksi;
        });

        return redirect()->route('transaksi.show', $transaksi)->with('success', 'Transaksi berhasil!');
    }

    public function struk(Transaksi $transaksi)
    {
        $transaksi->load('detailTransaksi.produk', 'user');

        $pdf = Pdf::loadView('struk', ['transaksi' => $transaksi])
            ->setPaper([0, 0, 226.77, 800], 'portrait');
        // 226.77pt = 80mm. Tinggi 800pt cukup besar, akan auto-crop oleh printer thermal.

        return $pdf->stream("struk-transaksi-{$transaksi->id}.pdf");
    }
}
