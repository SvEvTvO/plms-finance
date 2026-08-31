<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Saving;
use App\Models\Wallet;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Throwable;

class SavingController extends Controller
{
    public function __construct(
        protected FinanceService $financeService
    ) {}

    public function create(Request $request): View
    {
        $userId = auth()->id();

        // Ambil target yang belum selesai
        $goals = Goal::where('user_id', $userId)
            ->where('is_completed', false)
            ->orderBy('deadline')
            ->get();

        // Ambil dompet yang aktif
        $wallets = Wallet::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedGoalId = $request->query('goal_id');

        return view('savings.create', compact('goals', 'wallets', 'selectedGoalId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = auth()->id();

        // Validasi ketat kepemilikan goal & wallet oleh user yang sedang login
        $validated = $request->validate([
            'goal_id'     => ['required', Rule::exists('goals', 'id')->where('user_id', $userId)],
            'wallet_id'   => ['required', Rule::exists('wallets', 'id')->where('user_id', $userId)],
            'amount'      => ['required', 'numeric', 'min:1'],
            'date'        => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->financeService->createSaving($validated);
            return redirect()->route('goals.index')->with('success', 'Tabungan berhasil disetorkan! Saldo dompet Anda telah dikurangi.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Saving $saving): RedirectResponse
    {
        abort_if($saving->user_id !== auth()->id(), 403);

        try {
            $this->financeService->deleteSaving($saving);
            return back()->with('success', 'Tabungan berhasil ditarik kembali ke dompet.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
