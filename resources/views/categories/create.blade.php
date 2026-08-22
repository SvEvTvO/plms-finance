<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('categories.index') }}" class="p-2 bg-surface border border-border rounded-lg text-muted hover:text-primary transition-colors" title="Kembali">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-heading font-bold text-heading">Tambah Kategori</h2>
        </div>
    </x-slot>

    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm w-full">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Kategori -->
                <div>
                    <label for="name" class="block text-sm font-medium text-heading mb-1">Nama Kategori</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ti ti-tag text-muted text-lg"></i>
                        </div>
                        <input type="text" name="name" id="name" required class="w-full pl-10 rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5" placeholder="Misal: Gaji, Makanan, Transport" value="{{ old('name') }}">
                    </div>
                    @error('name') <p class="mt-1 text-sm text-danger flex items-center"><i class="ti ti-alert-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Tipe -->
                <div>
                    <label for="type" class="block text-sm font-medium text-heading mb-1">Tipe Kategori</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ti ti-arrows-exchange text-muted text-lg"></i>
                        </div>
                        <select name="type" id="type" required class="w-full pl-10 rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5 appearance-none">
                            <option value="">Pilih Tipe</option>
                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                        </select>
                    </div>
                    @error('type') <p class="mt-1 text-sm text-danger flex items-center"><i class="ti ti-alert-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Icon -->
                <div class="md:col-span-2">
                    <label for="icon" class="block text-sm font-medium text-heading mb-1">Kode Ikon (Opsional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ti ti-mood-smile text-muted text-lg"></i>
                        </div>
                        <input type="text" name="icon" id="icon" class="w-full pl-10 rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5" placeholder="Misal: ti-home, ti-car, ti-building-bank" value="{{ old('icon', 'ti-circle') }}">
                    </div>
                    <p class="mt-2 text-xs text-muted flex items-center">
                        <i class="ti ti-info-circle mr-1 text-info"></i>
                        Gunakan class dari <a href="https://tabler.io/icons" target="_blank" class="text-primary hover:underline ml-1">Tabler Icons</a> (contoh: ti-shopping-cart).
                    </p>
                    @error('icon') <p class="mt-1 text-sm text-danger flex items-center"><i class="ti ti-alert-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-border">
                <a href="{{ route('categories.index') }}" class="px-5 py-2.5 text-sm font-medium text-heading bg-surface border border-border rounded-lg hover:bg-background transition-colors flex items-center">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-surface bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center shadow-sm shadow-primary/30">
                    <i class="ti ti-device-floppy mr-2 text-lg"></i>
                    Simpan Kategori
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
