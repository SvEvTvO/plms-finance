<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

            // PERBAIKAN BUG: Gunakan filter() dengan fungsi callback agar objek Carbon bisa diformat ke string Y-m-d
            $incomeData = $chartDataRaw->first(function ($item) use ($dateString) {
                return Carbon::parse($item->date)->format('Y-m-d') === $dateString && $item->type === 'income';
            });

            $expenseData = $chartDataRaw->first(function ($item) use ($dateString) {
                return Carbon::parse($item->date)->format('Y-m-d') === $dateString && $item->type === 'expense';
            });

            // PERBAIKAN BUG: Pastikan di-cast ke (float) agar ApexCharts mengenali ini sebagai murni angka
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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $type = $request->input('type');
        $categoryId = $request->input('category_id');

        $query = Transaction::with(['category', 'wallet', 'sourceWallet', 'destinationWallet'])
                            ->where('user_id', auth()->id())
                            ->whereBetween('date', [$startDate, $endDate]);

        if ($type) $query->where('type', $type);
        if ($categoryId) $query->where('category_id', $categoryId);

        $transactions = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $response = new StreamedResponse(function() use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Delimiter ';' sangat krusial untuk Microsoft Excel di sistem regional Indonesia agar auto-parsing berjalan mulus. 
            // File mentah ini siap untuk visualisasi Scatter Plot di Excel sesuai rencana awal kita.
            fputcsv($handle, ['Tanggal', 'Jenis Mutasi', 'Kategori', 'Dompet / Sumber', 'Dompet Tujuan', 'Nominal', 'Keterangan'], ';');

            foreach ($transactions as $trx) {
                $kategori = $trx->category ? $trx->category->name : '-';
                $sumber = $trx->type === 'transfer' ? ($trx->sourceWallet->name ?? '-') : ($trx->wallet->name ?? '-');
                $tujuan = $trx->type === 'transfer' ? ($trx->destinationWallet->name ?? '-') : '-';
                fputcsv($handle, [
                    \Carbon\Carbon::parse($trx->date)->format('Y-m-d'),
                    strtoupper($trx->type), $kategori, $sumber, $tujuan, $trx->amount, $trx->description ?? '-'
                ], ';');
            }
            fclose($handle);
        });

        $fileName = "Laporan_{$startDate}_sd_{$endDate}.csv";
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}
