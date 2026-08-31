<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        // 1. Ambil tepat 1 dompet default
        $defaultWallet = Wallet::where('user_id', $userId)
            ->where('is_default', true)
            ->first();

        // 2. Ambil 2 dompet dengan saldo tertinggi (selain dompet default)
        $topBalanceWallets = Wallet::where('user_id', $userId)
            ->where('is_default', false)
            ->orderByDesc('balance')
            ->limit(2)
            ->get();

        // 3. Gabungkan ke dalam satu collection
        $topWallets = collect([$defaultWallet])->filter()->concat($topBalanceWallets);

        // 4. Ambil ID dompet top agar tidak duplikat di tabel bawah
        $topWalletIds = $topWallets->pluck('id')->toArray();

        // 5. Ambil sisa dompet untuk tabel list (Pagination 7)
        $tableWallets = Wallet::where('user_id', $userId)
            ->whereNotIn('id', $topWalletIds)
            ->orderByDesc('created_at')
            ->paginate(7);

        return view('wallets.index', compact('topWallets', 'tableWallets'));
    }

    public function create(): View
    {
        return view('wallets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = auth()->id();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|string|in:Bank,E-Wallet,Cash,Other',
            'balance' => 'required|numeric|min:0',
            'color'   => 'nullable|string|max:7',
        ]);

        DB::transaction(function () use ($validated, $userId) {
            $isFirstWallet = !Wallet::where('user_id', $userId)->exists();

            Wallet::create([
                'user_id'    => $userId,
                'name'       => $validated['name'],
                'type'       => $validated['type'],
                'balance'    => $validated['balance'],
                'color'      => $validated['color'] ?? '#14B8A6',
                'is_active'  => true,
                'is_default' => $isFirstWallet,
            ]);
        });

        return redirect()->route('wallets.index')->with('success', 'Wallet berhasil ditambahkan.');
    }

    public function edit(Wallet $wallet): View
    {
        abort_if($wallet->user_id !== auth()->id(), 403);

        return view('wallets.edit', compact('wallet'));
    }

    public function update(Request $request, Wallet $wallet): RedirectResponse
    {
        abort_if($wallet->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|string|in:Bank,E-Wallet,Cash,Other',
            'color' => 'nullable|string|max:7',
        ]);

        $wallet->update([
            'name'      => $validated['name'],
            'type'      => $validated['type'],
            'color'     => $validated['color'] ?? $wallet->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('wallets.index')->with('success', 'Wallet berhasil diperbarui.');
    }

    public function destroy(Wallet $wallet): RedirectResponse
    {
        abort_if($wallet->user_id !== auth()->id(), 403);

        if ($wallet->is_default) {
            return back()->with('error', 'Tidak dapat menghapus wallet utama (default).');
        }

        $wallet->delete();

        return redirect()->route('wallets.index')->with('success', 'Wallet berhasil dihapus.');
    }

    public function setDefault(Wallet $wallet): RedirectResponse
    {
        $userId = auth()->id();
        abort_if($wallet->user_id !== $userId, 403);

        DB::transaction(function () use ($wallet, $userId) {
            Wallet::where('user_id', $userId)->update(['is_default' => false]);
            $wallet->update(['is_default' => true]);
        });

        return back()->with('success', 'Wallet utama berhasil diubah.');
    }
}
