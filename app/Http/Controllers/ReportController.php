<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type = $request->input('type');
        $categoryId = $request->input('category_id');

        // 1. Query Utama untuk Tabel (dengan relasi)
        $query = Transaction::with(['category', 'wallet', 'sourceWallet', 'destinationWallet'])
                            ->where('user_id', auth()->id())
                            ->whereBetween('date', [$startDate, $endDate]);

        if ($type) $query->where('type', $type);
        if ($categoryId) $query->where('category_id', $categoryId);

        // 2. Hitung Summary
        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');
        $netIncome = $totalIncome - $totalExpense;

        // 3. Siapkan Data Chart (Query terpisah agar lebih bersih & akurat)
        $chartQuery = Transaction::where('user_id', auth()->id())
                                 ->whereBetween('date', [$startDate, $endDate]);

        if ($type) $chartQuery->where('type', $type);
        if ($categoryId) $chartQuery->where('category_id', $categoryId);

        $chartDataRaw = $chartQuery->selectRaw('date, type, sum(amount) as total')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        $dates = [];
        $incomes = [];
        $expenses = [];

        $currentDate = Carbon::parse($startDate);
        $lastDate = Carbon::parse($endDate);

        while ($currentDate->lte($lastDate)) {
            $dateString = $currentDate->format('Y-m-d');
            $dates[] = $currentDate->translatedFormat('d M');

            $incomeData = $chartDataRaw->first(function ($item) use ($dateString) {
                return Carbon::parse($item->date)->format('Y-m-d') === $dateString && $item->type === 'income';
            });

            $expenseData = $chartDataRaw->first(function ($item) use ($dateString) {
                return Carbon::parse($item->date)->format('Y-m-d') === $dateString && $item->type === 'expense';
            });

            $incomes[] = $incomeData ? (float) $incomeData->total : 0;
            $expenses[] = $expenseData ? (float) $expenseData->total : 0;

            $currentDate->addDay();
        }

        // 4. Ambil data transaksi dengan Pagination (Maks 10)
        $transactions = $query->orderByDesc('date')->orderByDesc('id')->paginate(10)->withQueryString();

        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();

        return view('reports.index', compact(
            'transactions', 'totalIncome', 'totalExpense', 'netIncome',
            'categories', 'startDate', 'endDate', 'type', 'categoryId',
            'dates', 'incomes', 'expenses'
        ));
    }

    public function export(Request $request)
    {
        // 1. Ambil Parameter Filter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type = $request->input('type');
        $categoryId = $request->input('category_id');

        // 2. Query Transaksi Berdasarkan Filter
        $query = Transaction::with(['category', 'wallet', 'sourceWallet', 'destinationWallet'])
                            ->where('user_id', auth()->id())
                            ->whereBetween('date', [$startDate, $endDate]);

        if ($type) $query->where('type', $type);
        if ($categoryId) $query->where('category_id', $categoryId);

        $transactions = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        // 3. Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan');

        // 4. Set Header di Baris 2
        $headers = [
            'A2' => 'No',
            'B2' => 'Tanggal',
            'C2' => 'Jenis Mutasi',
            'D2' => 'Kategori',
            'E2' => 'Dompet / Sumber',
            'F2' => 'Dompet Tujuan',
            'G2' => 'Nominal',
            'H2' => 'Keterangan'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Tinggi baris header (0.40" = 28.8 pt)
        $sheet->getRowDimension(2)->setRowHeight(28.8);

        // Styling Header
        $sheet->getStyle('A2:H2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'name' => 'Calibri',
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF00736A'], // Hex 00736a
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 5. Masukkan Data Mulai Baris 3
        $currentRow = 3;
        $no = 1;

        foreach ($transactions as $trx) {
            $sumber = '-';
            $tujuan = '-';
            $kategori = $trx->category->name ?? '-';

            if ($trx->type === 'transfer') {
                $kategori = 'Transfer Saldo';
                $sumber = $trx->sourceWallet->name ?? '-';
                $tujuan = $trx->destinationWallet->name ?? '-';
            } else {
                $sumber = $trx->wallet->name ?? '-';
            }

            $sheet->setCellValue('A' . $currentRow, $no++);
            $sheet->setCellValue('B' . $currentRow, Carbon::parse($trx->date)->format('Y-m-d'));
            $sheet->setCellValue('C' . $currentRow, strtoupper($trx->type));
            $sheet->setCellValue('D' . $currentRow, $kategori);
            $sheet->setCellValue('E' . $currentRow, $sumber);
            $sheet->setCellValue('F' . $currentRow, $tujuan);
            $sheet->setCellValue('G' . $currentRow, (float) $trx->amount);
            $sheet->setCellValue('H' . $currentRow, $trx->description ?? '-');

            // Tinggi baris data (0.26" = 18.72 pt)
            // $sheet->getRowDimension($currentRow)->setRowHeight(18.72);

            $currentRow++;
        }

        $lastRow = $currentRow - 1;

        // 6. Styling Data Rows (Jika ada data)
        if ($lastRow >= 3) {
            $dataRange = 'A3:H' . $lastRow;

            // Background Data dan Warna Text
            $sheet->getStyle($dataRange)->applyFromArray([
                'font' => [
                    'color' => ['argb' => 'FFFFFFFF'],
                    'name' => 'Calibri',
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF00968A'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Alignment: A-G Rata Tengah
            $sheet->getStyle('A3:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alignment H: Rata Kiri & AKTIFKAN WRAP TEXT
            $sheet->getStyle('H3:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('H3:H' . $lastRow)->getAlignment()->setWrapText(true); // <--- TAMBAHKAN INI
        }

        // 7. Border Garis Pembatas (Outline 999999)
        $tableRange = 'A2:H' . max(2, $lastRow);
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF999999'], // Outline color
                ],
            ],
        ]);

        // 8. Auto-fit lebar kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('H')->setWidth(50); // Kolom keterangan dilebarkan manual

        // 9. Eksekusi Download (.xlsx)
        $fileName = "Laporan_Keuangan_{$startDate}_sd_{$endDate}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
