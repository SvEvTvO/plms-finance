<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('wallets.index') }}" class="p-2 bg-surface border border-border rounded-lg text-muted hover:text-primary transition-colors" title="Kembali">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-heading font-bold text-heading">Edit Wallet: {{ $wallet->name }}</h2>
        </div>
    </x-slot>

    <!-- Card full-width -->
    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm w-full">
        <form action="{{ route('wallets.update', $wallet) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Kolom 1: Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-heading mb-1">Nama Dompet / Rekening</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ti ti-id text-muted text-lg"></i>
                        </div>
                        <input type="text" name="name" id="name" required class="w-full pl-10 rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5" value="{{ old('name', $wallet->name) }}">
                    </div>
                    @error('name') <p class="mt-1 text-sm text-danger flex items-center"><i class="ti ti-alert-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Kolom 2: Tipe -->
                <div>
                    <label for="type" class="block text-sm font-medium text-heading mb-1">Tipe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ti ti-category text-muted text-lg"></i>
                        </div>
                        <select name="type" id="type" required class="w-full pl-10 rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5 appearance-none">
                            <option value="Bank" {{ old('type', $wallet->type) == 'Bank' ? 'selected' : '' }}>Bank</option>
                            <option value="E-Wallet" {{ old('type', $wallet->type) == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="Cash" {{ old('type', $wallet->type) == 'Cash' ? 'selected' : '' }}>Tunai / Cash</option>
                            <option value="Other" {{ old('type', $wallet->type) == 'Other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    @error('type') <p class="mt-1 text-sm text-danger flex items-center"><i class="ti ti-alert-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Kolom 3: Saldo Saat Ini (Disabled) -->
                <div>
                    <label class="block text-sm font-medium text-heading mb-1">Saldo Saat Ini (Hanya Baca)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-muted font-bold">Rp</span>
                        </div>
                        <input type="text" disabled class="w-full pl-10 rounded-lg border-border/50 bg-background/50 text-muted cursor-not-allowed py-2.5 font-medium" value="{{ number_format($wallet->balance, 0, ',', '.') }}">
                    </div>
                    <p class="mt-1 text-xs text-info flex items-center">
                        <i class="ti ti-info-circle mr-1"></i>
                        Gunakan fitur Transaksi (Pemasukan/Pengeluaran) untuk mengubah saldo.
                    </p>
                </div>

                <!-- Kolom 4: Warna Label & Status -->
                <div class="space-y-4">
                    <div>
                        <label for="color" class="block text-sm font-medium text-heading mb-1">Warna Label</label>
                        <div class="flex items-center space-x-3 bg-background p-1.5 rounded-lg border border-border">
                            <input type="color" name="color" id="color" class="h-10 w-16 p-0.5 rounded cursor-pointer border-0 bg-transparent" value="{{ old('color', $wallet->color) }}">
                        </div>
                        @error('color') <p class="mt-1 text-sm text-danger flex items-center"><i class="ti ti-alert-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center bg-background p-3 rounded-lg border border-border">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="h-5 w-5 rounded border-border text-primary focus:ring-primary/20" {{ old('is_active', $wallet->is_active) ? 'checked' : '' }}>
                        <div class="ml-3">
                            <label for="is_active" class="block text-sm font-medium text-heading cursor-pointer">Wallet Aktif</label>
                            <p class="text-xs text-muted">Hanya wallet aktif yang bisa digunakan transaksi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-border">
                <a href="{{ route('wallets.index') }}" class="px-5 py-2.5 text-sm font-medium text-heading bg-surface border border-border rounded-lg hover:bg-background transition-colors flex items-center">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-surface bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center shadow-sm shadow-primary/30">
                    <i class="ti ti-device-floppy mr-2 text-lg"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>


    <!-- JS Logic untuk Mencegah Multiple Submit -->
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
