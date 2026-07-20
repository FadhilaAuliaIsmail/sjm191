<?php

namespace App\Http\Controllers;

use App\Models\BarangProduksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangProduksiController extends Controller
{
    public function index(Request $request)
    {
        $sumber = $request->input('sumber', 'pasar'); // pasar / sponsor
        $filter = $request->input('filter', 'semua'); // semua, hari, minggu, bulan, custom
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $query = BarangProduksi::where('sumber', $sumber);

        if ($filter === 'hari') {
            $query->whereDate('tanggal_masuk', Carbon::today());
        } elseif ($filter === 'minggu') {
            $query->whereBetween('tanggal_masuk', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        } elseif ($filter === 'bulan') {
            $query->whereYear('tanggal_masuk', Carbon::now()->year)
                ->whereMonth('tanggal_masuk', Carbon::now()->month);
        } elseif ($filter === 'custom' && $dari && $sampai) {
            $query->whereBetween('tanggal_masuk', [$dari, $sampai]);
        }

        $barangProduksi = $query->latest('tanggal_masuk')->get();

        // Ringkasan total per nama barang + satuan (termasuk total nilai/belanja)
        $ringkasan = $barangProduksi
            ->groupBy(fn($item) => $item->nama_barang . '|' . $item->satuan)
            ->map(function ($items) {
                return [
                    'nama_barang' => $items->first()->nama_barang,
                    'satuan' => $items->first()->satuan,
                    'total_jumlah' => $items->sum('jumlah'),
                    'total_belanja' => $items->sum(function ($i) {
                        return ($i->harga_beli ?? 0) * $i->jumlah_dibeli;
                    }),
                ];
            })
            ->values();

        $totalBelanjaKeseluruhan = $barangProduksi->sum(function ($i) {
            return ($i->harga_beli ?? 0) * $i->jumlah_dibeli;
        });

        return Inertia::render('BarangProduksi/Index', [
            'barangProduksi' => $barangProduksi,
            'ringkasan' => $ringkasan,
            'totalBelanjaKeseluruhan' => $totalBelanjaKeseluruhan,
            'filters' => [
                'sumber' => $sumber,
                'filter' => $filter,
                'dari' => $dari,
                'sampai' => $sampai,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_barang' => 'required|string|max:255',
            'sumber' => 'required|in:pasar,sponsor',
            'tanggal_masuk' => 'required|date',
            'jumlah_dibeli' => 'required|integer|min:1',
            'berat_per_satuan' => 'required|numeric|min:0.01',
            'satuan' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ];

        if ($request->input('sumber') === 'pasar') {
            $rules['harga_beli'] = 'required|numeric|min:0';
        } else {
            $rules['harga_beli'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Total dihitung otomatis: jumlah dibeli x berat per satuan
        $validated['jumlah'] = $validated['jumlah_dibeli'] * $validated['berat_per_satuan'];

        $validated['user_id'] = $request->user()->id;
        BarangProduksi::create($validated);

        return redirect()->route('barang-produksi.index', ['sumber' => $validated['sumber']])
            ->with('success', 'Barang produksi berhasil ditambahkan.');
    }

    public function destroy(BarangProduksi $barangProduksi)
    {
        $sumber = $barangProduksi->sumber;
        $barangProduksi->delete();

        return redirect()->route('barang-produksi.index', ['sumber' => $sumber])
            ->with('success', 'Barang produksi berhasil dihapus.');
    }

    public function update(Request $request, BarangProduksi $barangProduksi)
    {
        $rules = [
            'nama_barang' => 'required|string|max:255',
            'sumber' => 'required|in:pasar,sponsor',
            'tanggal_masuk' => 'required|date',
            'jumlah_dibeli' => 'required|integer|min:1',
            'berat_per_satuan' => 'required|numeric|min:0.01',
            'satuan' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ];

        if ($request->input('sumber') === 'pasar') {
            $rules['harga_beli'] = 'required|numeric|min:0';
        } else {
            $rules['harga_beli'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $validated['jumlah'] = $validated['jumlah_dibeli'] * $validated['berat_per_satuan'];

        $barangProduksi->update($validated);

        return redirect()->route('barang-produksi.index', ['sumber' => $validated['sumber']])
            ->with('success', 'Barang produksi berhasil diperbarui.');
    }
}
