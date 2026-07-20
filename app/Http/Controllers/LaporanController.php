<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\BarangProduksi;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    private function getQueryData(Request $request): array
    {
        $filter = $request->input('filter', 'bulan');
        $dari   = $request->input('dari');
        $sampai = $request->input('sampai');

        $transaksiQuery   = Transaksi::query();
        $pengeluaranQuery = BarangProduksi::where('sumber', 'pasar');
        $sponsorQuery     = BarangProduksi::where('sumber', 'sponsor');

        if ($filter === 'hari') {
            $transaksiQuery->whereDate('tanggal_transaksi', Carbon::today());
            $pengeluaranQuery->whereDate('tanggal_masuk', Carbon::today());
            $sponsorQuery->whereDate('tanggal_masuk', Carbon::today());
            $periode = 'Hari Ini (' . Carbon::today()->format('d/m/Y') . ')';
        } elseif ($filter === 'minggu') {
            $transaksiQuery->whereBetween('tanggal_transaksi', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $pengeluaranQuery->whereBetween('tanggal_masuk', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $sponsorQuery->whereBetween('tanggal_masuk', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $periode = 'Minggu Ini (' . Carbon::now()->startOfWeek()->format('d/m/Y') . ' - ' . Carbon::now()->endOfWeek()->format('d/m/Y') . ')';
        } elseif ($filter === 'bulan') {
            $transaksiQuery->whereYear('tanggal_transaksi', Carbon::now()->year)->whereMonth('tanggal_transaksi', Carbon::now()->month);
            $pengeluaranQuery->whereYear('tanggal_masuk', Carbon::now()->year)->whereMonth('tanggal_masuk', Carbon::now()->month);
            $sponsorQuery->whereYear('tanggal_masuk', Carbon::now()->year)->whereMonth('tanggal_masuk', Carbon::now()->month);
            $periode = Carbon::now()->translatedFormat('F Y');
        } elseif ($filter === 'custom' && $dari && $sampai) {
            $transaksiQuery->whereBetween('tanggal_transaksi', [$dari, $sampai]);
            $pengeluaranQuery->whereBetween('tanggal_masuk', [$dari, $sampai]);
            $sponsorQuery->whereBetween('tanggal_masuk', [$dari, $sampai]);
            $periode = Carbon::parse($dari)->format('d/m/Y') . ' - ' . Carbon::parse($sampai)->format('d/m/Y');
        } else {
            $periode = 'Semua';
        }

        $transaksi   = $transaksiQuery->orderBy('tanggal_transaksi')->get();
        $pengeluaran = $pengeluaranQuery->orderBy('tanggal_masuk')->get();
        $sponsor     = $sponsorQuery->orderBy('tanggal_masuk')->get();

        $totalPendapatan  = (float) $transaksi->sum('total_harga');
        $totalPengeluaran = (float) $pengeluaran->sum(fn($p) => ($p->harga_beli ?? 0) * $p->jumlah_dibeli);
        $totalSponsor     = (float) $sponsor->sum(fn($s) => ($s->harga_beli ?? 0) * $s->jumlah_dibeli);
        $labaRugi         = $totalPendapatan - $totalPengeluaran;

        return compact('transaksi', 'pengeluaran', 'sponsor', 'totalPendapatan', 'totalPengeluaran', 'totalSponsor', 'labaRugi', 'filter', 'dari', 'sampai', 'periode');
    }

    public function index(Request $request)
    {
        $data = $this->getQueryData($request);

        return Inertia::render('Laporan/Index', [
            'transaksi'          => $data['transaksi'],
            'pengeluaran'        => $data['pengeluaran'],
            'sponsor'            => $data['sponsor'],
            'totalPendapatan'    => $data['totalPendapatan'],
            'totalPengeluaran'   => $data['totalPengeluaran'],
            'totalSponsor'       => $data['totalSponsor'],
            'labaRugi'           => $data['labaRugi'],
            'filters'            => [
                'filter' => $data['filter'],
                'dari'   => $data['dari'],
                'sampai' => $data['sampai'],
            ],
        ]);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getQueryData($request);

        $pdf = Pdf::loadView('laporan.export-pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . now()->format('Ymd') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getQueryData($request);

        $export = new LaporanExport(
            transaksi: $data['transaksi'],
            pengeluaran: $data['pengeluaran'],
            sponsor: $data['sponsor'],
            totalPendapatan: $data['totalPendapatan'],
            totalPengeluaran: $data['totalPengeluaran'],
            totalSponsor: $data['totalSponsor'],
            labaRugi: $data['labaRugi'],
            periode: $data['periode'],
        );

        return Excel::download($export, 'laporan-keuangan-' . now()->format('Ymd') . '.xlsx');
    }
}
