<?php

namespace App\Http\Controllers;

use App\Models\DataMitra;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isKasir()) {
            $today = Carbon::today();

            $pendapatanHariIni = Transaksi::where('user_id', $user->id)
                ->whereDate('tanggal_transaksi', $today)
                ->sum('total_harga');

            $transaksiHariIni = Transaksi::where('user_id', $user->id)
                ->whereDate('tanggal_transaksi', $today)
                ->count();

            $transaksiTerakhir = Transaksi::where('user_id', $user->id)
                ->whereDate('tanggal_transaksi', $today)
                ->latest('tanggal_transaksi')
                ->limit(5)
                ->get(['id', 'tanggal_transaksi', 'total_harga', 'bayar', 'kembalian']);

            return Inertia::render('DashboardKasir', [
                'pendapatanHariIni' => (float) $pendapatanHariIni,
                'transaksiHariIni' => $transaksiHariIni,
                'transaksiTerakhir' => $transaksiTerakhir,
            ]);
        }

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Pendapatan hari ini
        $pendapatanHariIni = Transaksi::whereDate('tanggal_transaksi', $today)
            ->sum('total_harga');

        // 2. Pendapatan bulan ini
        $pendapatanBulanIni = Transaksi::where('tanggal_transaksi', '>=', $startOfMonth)
            ->sum('total_harga');

        // 3. Jumlah transaksi hari ini
        $transaksiHariIni = Transaksi::whereDate('tanggal_transaksi', $today)->count();

        // 4. Jumlah produk aktif
        $jumlahProduk = Produk::count();

        // 5. Jumlah mitra/cabang
        $jumlahMitra = DataMitra::count();

        // 6. Produk dengan stok menipis (10 ke bawah)
        $stokMenipis = Produk::where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get(['id', 'nama_produk', 'stok', 'satuan']);

        // 7. Grafik pendapatan 7 hari terakhir
        $tujuhHariLalu = Carbon::today()->subDays(6);

        $transaksiMingguan = Transaksi::where('tanggal_transaksi', '>=', $tujuhHariLalu)
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_harga) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $grafikPendapatan = [];
        for ($i = 0; $i < 7; $i++) {
            $tanggal = $tujuhHariLalu->copy()->addDays($i)->format('Y-m-d');
            $grafikPendapatan[] = [
                'tanggal' => Carbon::parse($tanggal)->translatedFormat('D, j M'),
                'total' => $transaksiMingguan->get($tanggal)?->total ?? 0,
            ];
        }

        // 8. Produk terlaris (berdasarkan jumlah terjual)
        $produkTerlaris = DetailTransaksi::join('produk', 'detail_transaksi.produk_id', '=', 'produk.id')
            ->selectRaw('produk.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual')
            ->groupBy('produk.id', 'produk.nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'pendapatanHariIni' => (float) $pendapatanHariIni,
                'pendapatanBulanIni' => (float) $pendapatanBulanIni,
                'transaksiHariIni' => $transaksiHariIni,
                'jumlahProduk' => $jumlahProduk,
                'jumlahMitra' => $jumlahMitra,
            ],
            'stokMenipis' => $stokMenipis,
            'grafikPendapatan' => $grafikPendapatan,
            'produkTerlaris' => $produkTerlaris,
        ]);
    }
}
