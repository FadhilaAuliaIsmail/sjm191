<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithEvents, WithTitle, WithColumnWidths
{
    // Warna brand — CV. Susu Jahe Merah 191
    private const MAROON = 'A31D22';
    private const CREAM  = 'FAF6EE';
    private const ABU    = '78716C';
    private const ABU_MUDA = 'A8A29E';

    public function __construct(
        protected Collection $transaksi,
        protected Collection $pengeluaran,
        protected Collection $sponsor,
        protected float $totalPendapatan,
        protected float $totalPengeluaran,
        protected float $totalSponsor,
        protected float $labaRugi,
        protected string $periode,
    ) {}

    public function title(): string
    {
        return 'Laporan Laba Rugi';
    }

    // FromCollection wajib diimplementasikan, tapi datanya kita tulis manual
    // di event AfterSheet supaya seluruh bagian bisa disusun bebas dalam satu sheet.
    public function collection(): Collection
    {
        return collect();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 24,
            'C' => 14,
            'D' => 17,
            'E' => 16,
            'F' => 26,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                $row = $this->tulisJudul($sheet, $row);
                $row = $this->tulisRingkasan($sheet, $row);

                $row = $this->tulisSection(
                    sheet: $sheet,
                    row: $row,
                    judul: 'PENDAPATAN (TRANSAKSI PENJUALAN)',
                    headers: ['Tanggal', 'Total Harga', 'Bayar', 'Kembalian'],
                    rows: $this->transaksi->map(fn($t) => [
                        \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d/m/Y'),
                        (float) $t->total_harga,
                        (float) $t->bayar,
                        (float) $t->kembalian,
                    ])->all(),
                    kolomUang: [1, 2, 3],
                    labelTotal: 'Total Pendapatan',
                    nilaiTotal: $this->totalPendapatan,
                    kosongText: 'Tidak ada transaksi pada periode ini.',
                );

                $row = $this->tulisSection(
                    sheet: $sheet,
                    row: $row,
                    judul: 'PENGELUARAN (BELANJA BAHAN DARI PASAR)',
                    headers: ['Tanggal', 'Nama Barang', 'Jumlah', 'Harga Beli', 'Subtotal'],
                    rows: $this->pengeluaran->map(fn($p) => [
                        \Carbon\Carbon::parse($p->tanggal_masuk)->format('d/m/Y'),
                        $p->nama_barang,
                        $p->jumlah_dibeli . ' ' . $p->satuan,
                        (float) ($p->harga_beli ?? 0),
                        (float) (($p->harga_beli ?? 0) * $p->jumlah_dibeli),
                    ])->all(),
                    kolomUang: [3, 4],
                    labelTotal: 'Total Beban Belanja Bahan',
                    nilaiTotal: $this->totalPengeluaran,
                    kosongText: 'Tidak ada pengeluaran pada periode ini.',
                );

                $row = $this->tulisSection(
                    sheet: $sheet,
                    row: $row,
                    judul: 'BARANG DARI SPONSOR',
                    headers: ['Tanggal', 'Nama Barang', 'Jumlah', 'Estimasi Harga', 'Subtotal', 'Keterangan'],
                    rows: $this->sponsor->map(fn($s) => [
                        \Carbon\Carbon::parse($s->tanggal_masuk)->format('d/m/Y'),
                        $s->nama_barang,
                        $s->jumlah_dibeli . ' ' . $s->satuan,
                        $s->harga_beli ? (float) $s->harga_beli : null,
                        $s->harga_beli ? (float) ($s->harga_beli * $s->jumlah_dibeli) : null,
                        $s->keterangan ?? '-',
                    ])->all(),
                    kolomUang: [3, 4],
                    labelTotal: 'Total Nilai Barang Sponsor',
                    nilaiTotal: $this->totalSponsor,
                    kosongText: 'Tidak ada barang sponsor pada periode ini.',
                    catatan: '*Barang sponsor tidak memengaruhi perhitungan Laba/Rugi karena bukan pengeluaran kas.',
                );

                $sheet->getStyle('A1:F' . ($row + 5))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }

    private function tulisJudul(Worksheet $sheet, int $row): int
    {
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'CV. SUSU JAHE MERAH 191 — LAPORAN LABA RUGI');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => self::MAROON]],
        ]);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'Periode: ' . $this->periode);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => self::ABU]],
        ]);

        return $row + 2;
    }

    private function tulisRingkasan(Worksheet $sheet, int $row): int
    {
        $baris = [
            ['Total Pendapatan', $this->totalPendapatan, false],
            ['Total Pengeluaran', $this->totalPengeluaran, false],
            ['Laba / Rugi', $this->labaRugi, true],
        ];

        foreach ($baris as [$label, $nilai, $sorot]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $nilai);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);

            if ($sorot) {
                $warna = $nilai >= 0 ? '1F7A4D' : self::MAROON;
                $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $warna]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::CREAM]],
                ]);
            }
            $row++;
        }

        $sheet->setCellValue("A{$row}", 'Total Nilai Barang Sponsor (di luar Laba/Rugi)');
        $sheet->setCellValue("B{$row}", $this->totalSponsor);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(self::ABU));
        $row++;

        return $row + 2;
    }

    /**
     * Menulis satu blok tabel (judul section, header, data, baris total) dan
     * mengembalikan nomor baris berikutnya yang masih kosong.
     */
    private function tulisSection(
        Worksheet $sheet,
        int $row,
        string $judul,
        array $headers,
        array $rows,
        array $kolomUang,
        string $labelTotal,
        float $nilaiTotal,
        string $kosongText,
        ?string $catatan = null,
    ): int {
        $jumlahKolom = count($headers);
        $kolomTerakhir = chr(ord('A') + $jumlahKolom - 1);

        // Judul section
        $sheet->mergeCells("A{$row}:{$kolomTerakhir}{$row}");
        $sheet->setCellValue("A{$row}", $judul);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::MAROON]],
        ]);
        $row++;

        // Header tabel
        foreach ($headers as $i => $h) {
            $kolom = chr(ord('A') + $i);
            $sheet->setCellValue("{$kolom}{$row}", $h);
        }
        $sheet->getStyle("A{$row}:{$kolomTerakhir}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(20);
        $row++;

        // Data
        if (empty($rows)) {
            $sheet->mergeCells("A{$row}:{$kolomTerakhir}{$row}");
            $sheet->setCellValue("A{$row}", $kosongText);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => self::ABU_MUDA]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;
        } else {
            foreach ($rows as $dataRow) {
                foreach ($dataRow as $i => $nilai) {
                    $kolom = chr(ord('A') + $i);
                    $sheet->setCellValue("{$kolom}{$row}", $nilai ?? '-');
                    if (in_array($i, $kolomUang, true) && $nilai !== null) {
                        $sheet->getStyle("{$kolom}{$row}")->getNumberFormat()->setFormatCode('#,##0');
                    }
                }
                $row++;
            }
        }

        // Baris total
        $kolomLabelAkhir = chr(ord('A') + $jumlahKolom - 2);
        $sheet->mergeCells("A{$row}:{$kolomLabelAkhir}{$row}");
        $sheet->setCellValue("A{$row}", $labelTotal);
        $sheet->setCellValue("{$kolomTerakhir}{$row}", $nilaiTotal);
        $sheet->getStyle("{$kolomTerakhir}{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A{$row}:{$kolomTerakhir}{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E3A93B']]],
        ]);
        $row++;

        if ($catatan) {
            $sheet->mergeCells("A{$row}:{$kolomTerakhir}{$row}");
            $sheet->setCellValue("A{$row}", $catatan);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => self::ABU_MUDA]],
            ]);
            $row++;
        }

        return $row + 2;
    }
}
