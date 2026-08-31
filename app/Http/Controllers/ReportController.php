<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $userId    = auth()->id();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type      = $request->input('type');
        $categoryId = $request->input('category_id');

        // 1. Base Query Filter
        $baseQuery = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($type) {
            $baseQuery->where('type', $type);
        }
        if ($categoryId) {
            $baseQuery->where('category_id', $categoryId);
        }

        // 2. Hitung Summary (1 Query Agregasi)
        $summary = (clone $baseQuery)
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw('type, sum(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $totalIncome  = (float) ($summary['income'] ?? 0);
        $totalExpense = (float) ($summary['expense'] ?? 0);
        $netIncome    = $totalIncome - $totalExpense;

        // 3. Data Chart (1 Query Agregasi)
        $chartDataRaw = (clone $baseQuery)
            ->selectRaw('date, type, sum(amount) as total')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        // Buat map [tanggal][tipe] => total untuk lookup instan O(1)
        $chartMap = [];
        foreach ($chartDataRaw as $item) {
            $d = Carbon::parse($item->date)->format('Y-m-d');
            $chartMap[$d][$item->type] = (float) $item->total;
        }

        $dates    = [];
        $incomes  = [];
        $expenses = [];

        $currentDate = Carbon::parse($startDate);
        $lastDate    = Carbon::parse($endDate);

        while ($currentDate->lte($lastDate)) {
            $dateString = $currentDate->format('Y-m-d');
            $dates[]    = $currentDate->translatedFormat('d M');

            $incomes[]  = $chartMap[$dateString]['income'] ?? 0;
            $expenses[] = $chartMap[$dateString]['expense'] ?? 0;

            $currentDate->addDay();
        }

        // 4. Data Transaksi Paginated
        $transactions = (clone $baseQuery)
            ->with(['category', 'wallet', 'sourceWallet', 'destinationWallet'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::where('user_id', $userId)->orderBy('name')->get();

        return view('reports.index', compact(
            'transactions', 'totalIncome', 'totalExpense', 'netIncome',
            'categories', 'startDate', 'endDate', 'type', 'categoryId',
            'dates', 'incomes', 'expenses'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $userId    = auth()->id();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type      = $request->input('type');
        $categoryId = $request->input('category_id');

        $query = Transaction::with(['category', 'wallet', 'sourceWallet', 'destinationWallet'])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($type) {
            $query->where('type', $type);
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $transactions = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan');

        // Header
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

        $sheet->getRowDimension(2)->setRowHeight(28.8);

        $sheet->getStyle('A2:H2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'name' => 'Calibri',
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF00736A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Isi Data
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

            $currentRow++;
        }

        $lastRow = $currentRow - 1;

        if ($lastRow >= 3) {
            $dataRange = 'A3:H' . $lastRow;

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

            $sheet->getStyle('A3:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H3:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('H3:H' . $lastRow)->getAlignment()->setWrapText(true);
        }

        $tableRange = 'A2:H' . max(2, $lastRow);
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF999999'],
                ],
            ],
        ]);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('H')->setWidth(50);

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
