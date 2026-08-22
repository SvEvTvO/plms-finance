<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Saving;
use App\Models\Wallet;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Exception;

class SavingController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function create(Request $request)
    {
        // Ambil goal yang belum selesai
        $goals = Goal::where('user_id', auth()->id())
                     ->where('is_completed', false)
                     ->orderBy('deadline')
                     ->get();

        // Ambil dompet yang aktif
        $wallets = Wallet::where('user_id', auth()->id())
                       ->where('is_active', true)
                       ->get();

        // Jika user mengklik "Setor" dari kartu target tertentu, tangkap ID-nya
        $selectedGoalId = $request->query('goal_id');

        return view('savings.create', compact('goals', 'wallets', 'selectedGoalId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'goal_id' => 'required|exists:goals,id',
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $this->financeService->createSaving($validated);
            return redirect()->route('goals.index')->with('success', 'Tabungan berhasil disetorkan! Saldo dompet Anda telah dikurangi.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Untuk sementara, fungsi hapus tabungan jika terjadi salah input
    public function destroy(Saving $saving)
    {
        if ($saving->user_id !== auth()->id()) abort(403);

        try {
            $this->financeService->deleteSaving($saving);
            return back()->with('success', 'Tabungan berhasil ditarik kembali ke dompet.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
