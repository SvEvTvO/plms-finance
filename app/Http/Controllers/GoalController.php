<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', auth()->id())
            ->orderBy('is_completed')
            ->orderBy('deadline')
            ->get();

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'nullable|date',
            'purchase_link' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
        ]);

        Goal::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => 0,
            'deadline' => $validated['deadline'] ?? null,
            'purchase_link' => $validated['purchase_link'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_completed' => false,
        ]);

        return redirect()->route('goals.index')->with('success', 'Target keuangan berhasil ditambahkan.');
    }

    // INI ADALAH FUNGSI BARU YANG SAYA MAKSUD
    public function show(Goal $goal)
    {
        // Pastikan keamanan: hanya pemilik yang bisa melihat
        if ($goal->user_id !== auth()->id()) abort(403);

        // Ambil data riwayat tabungan untuk target ini beserta info dompetnya
        $savings = $goal->savings()->with('wallet')->orderByDesc('date')->orderByDesc('id')->get();

        return view('goals.show', compact('goal', 'savings'));
    }

    public function edit(Goal $goal)
    {
        if ($goal->user_id !== auth()->id()) abort(403);
        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'nullable|date',
            'purchase_link' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
        ]);

        $isCompleted = $goal->current_amount >= $validated['target_amount'];

        $goal->update([
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'deadline' => $validated['deadline'] ?? null,
            'purchase_link' => $validated['purchase_link'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_completed' => $isCompleted,
        ]);

        return redirect()->route('goals.index')->with('success', 'Target keuangan berhasil diperbarui.');
    }

    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== auth()->id()) abort(403);

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Target keuangan berhasil dihapus.');
    }
}
