<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('goals.index') }}" class="p-2 bg-surface border border-border rounded-lg text-muted hover:text-primary transition-colors" title="Kembali">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-heading font-bold text-heading">Setor Tabungan</h2>
        </div>
    </x-slot>

    @if(session('error'))
        <div class="mb-6 p-4 bg-danger/10 border border-danger/20 text-danger rounded-xl flex items-center">
            <i class="ti ti-alert-circle w-5 h-5 mr-2 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm w-full">
        <form action="{{ route('savings.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Pilih Target (Goal) -->
                <div>
                    <label for="goal_id" class="block text-sm font-medium text-heading mb-1">Untuk Target Keuangan Apa?</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted">
                            <i class="ti ti-target text-xl"></i>
                        </span>
                        <select name="goal_id" id="goal_id" required class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-white">
                            <option value="">Pilih Target...</option>
                            @foreach($goals as $goal)
                                <option value="{{ $goal->id }}" {{ (old('goal_id') ?? $selectedGoalId) == $goal->id ? 'selected' : '' }}>
                                    {{ $goal->name }} (Kurang: Rp {{ number_format($goal->target_amount - $goal->current_amount, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('goal_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Pilih Sumber Dana (Wallet) -->
                <div>
                    <label for="wallet_id" class="block text-sm font-medium text-heading mb-1">Ambil Uang Dari Dompet Mana?</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted">
                            <i class="ti ti-wallet text-xl"></i>
                        </span>
                        <select name="wallet_id" id="wallet_id" required class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-white">
                            <option value="">Pilih Sumber Dana...</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                    {{ $wallet->name }} (Saldo: Rp {{ number_format($wallet->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('wallet_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Nominal Tabungan -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-heading mb-1">Nominal yang Disetor</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted font-bold">
                            Rp
                        </span>
                        <input type="number" name="amount" id="amount" required min="1" step="0.01" class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body font-medium text-lg" placeholder="0" value="{{ old('amount') }}">
                    </div>
                    @error('amount') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="date" class="block text-sm font-medium text-heading mb-1">Tanggal Setor</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted">
                            <i class="ti ti-calendar text-xl"></i>
                        </span>
                        <input type="date" name="date" id="date" required class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body" value="{{ old('date', date('Y-m-d')) }}">
                    </div>
                    @error('date') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-heading mb-1">Catatan (Opsional)</label>
                    <textarea name="description" id="description" rows="2" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5 shadow-sm" placeholder="Misal: Tabungan minggu ke-3...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Footer Form -->
            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-border">
                <a href="{{ route('goals.index') }}" class="px-5 py-2.5 text-sm font-medium text-heading bg-surface border border-border rounded-lg hover:bg-background transition-colors flex items-center">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-surface bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center shadow-sm shadow-primary/30">
                    <i class="ti ti-pig-money mr-2 text-lg"></i>
                    Setor Sekarang
                </button>
            </div>
        </form>
    </div>

    <!-- Proteksi Multiple Submit -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                const submitBtn = form.querySelector('button[type="submit"]');
                form.addEventListener('submit', function(e) {
                    if (!submitBtn.disabled) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="ti ti-loader-2 animate-spin mr-2 text-lg"></i> Memproses...';
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    } else {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</x-app-layout>
