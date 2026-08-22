<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // 1. Ambil tepat 1 dompet yang berstatus Default
        $defaultWallet = Wallet::where('user_id', $userId)
            ->where('is_default', true)
            ->first();

        // 2. Ambil 2 dompet dengan saldo tertinggi (selain dompet default)
        $topBalanceWallets = Wallet::where('user_id', $userId)
            ->where('is_default', false)
            ->orderByDesc('balance')
            ->limit(2)
            ->get();

        // 3. Gabungkan: Default di urutan pertama (kiri), disusul saldo tertinggi (tengah & kanan)
        $topWallets = collect();

        if ($defaultWallet) {
            $topWallets->push($defaultWallet);
        }

        foreach ($topBalanceWallets as $wallet) {
            $topWallets->push($wallet);
        }

        // 4. Ambil ID dari dompet-dompet di atas agar tidak berulang di tabel
        $topWalletIds = $topWallets->pluck('id')->toArray();

        // 5. Ambil SISA dompet untuk dimasukkan ke tabel (Pagination max 7)
        $tableWallets = Wallet::where('user_id', $userId)
            ->whereNotIn('id', $topWalletIds)
            ->orderByDesc('created_at')
            ->paginate(7);

        return view('wallets.index', compact('topWallets', 'tableWallets'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Bank,E-Wallet,Cash,Other',
            'balance' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:7',
        ]);

        DB::transaction(function () use ($validated) {
            $isFirst = Wallet::where('user_id', auth()->id())->count() === 0;

            Wallet::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'type' => $validated['type'],
                'balance' => $validated['balance'],
                'color' => $validated['color'] ?? '#14B8A6', // Default ke Primary Color
                'is_active' => true,
                'is_default' => $isFirst, // Otomatis default jika ini wallet pertama
            ]);
        });

        return redirect()->route('wallets.index')->with('success', 'Wallet berhasil ditambahkan.');
    }

    public function edit(Wallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) abort(403);
        return view('wallets.edit', compact('wallet'));
    }

    public function update(Request $request, Wallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Bank,E-Wallet,Cash,Other',
            'color' => 'nullable|string|max:7',
        ]);

        $wallet->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'color' => $validated['color'] ?? $wallet->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('wallets.index')->with('success', 'Wallet berhasil diperbarui.');
    }

    public function destroy(Wallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) abort(403);

        if ($wallet->is_default) {
            return back()->with('error', 'Tidak dapat menghapus wallet utama (default).');
        }

        $wallet->delete(); // Soft delete
        return redirect()->route('wallets.index')->with('success', 'Wallet berhasil dihapus.');
    }

    public function setDefault(Wallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) abort(403);

        DB::transaction(function () use ($wallet) {
            // Reset semua wallet milik user menjadi bukan default
            Wallet::where('user_id', auth()->id())->update(['is_default' => false]);

            // Set wallet terpilih menjadi default
            $wallet->update(['is_default' => true]);
        });

        return back()->with('success', 'Wallet utama berhasil diubah.');
    }
}
