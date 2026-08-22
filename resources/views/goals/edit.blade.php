<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('goals.index') }}" class="p-2 bg-surface border border-border rounded-lg text-muted hover:text-primary transition-colors" title="Kembali">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-heading font-bold text-heading">Edit Target: {{ $goal->name }}</h2>
        </div>
    </x-slot>

    <!-- Hapus max-w-3xl agar w-full bisa membentang menggunakan grid -->
    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm w-full">
        <form action="{{ route('goals.update', $goal) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Nama Target -->
                <div>
                    <label for="name" class="block text-sm font-medium text-heading mb-1">Nama Target / Impian</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted">
                            <i class="ti ti-target text-xl"></i>
                        </span>
                        <input type="text" name="name" id="name" required class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body" value="{{ old('name', $goal->name) }}">
                    </div>
                    @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Nominal Terkumpul (Read Only - Info) -->
                <div>
                    <label class="block text-sm font-medium text-heading mb-1">Terkumpul Saat Ini (Hanya Baca)</label>
                    <div class="flex shadow-sm rounded-lg opacity-70">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted font-bold">
                            Rp
                        </span>
                        <input type="text" disabled class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border bg-background cursor-not-allowed text-body font-medium" value="{{ number_format($goal->current_amount, 0, ',', '.') }}">
                    </div>
                    <p class="mt-1 text-xs text-info flex items-center"><i class="ti ti-info-circle mr-1"></i> Penambahan dilakukan melalui fitur Tabungan.</p>
                </div>

                <!-- Nominal Target -->
                <div>
                    <label for="target_amount" class="block text-sm font-medium text-heading mb-1">Target Jumlah yang Dibutuhkan</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted font-bold">
                            Rp
                        </span>
                        <input type="number" name="target_amount" id="target_amount" required min="1" step="0.01" class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body font-medium text-lg" value="{{ old('target_amount', floatval($goal->target_amount)) }}">
                    </div>
                    @error('target_amount') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Target (Deadline) -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-heading mb-1">Tenggat Waktu (Opsional)</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted">
                            <i class="ti ti-calendar text-xl"></i>
                        </span>
                        <input type="date" name="deadline" id="deadline" class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body" value="{{ old('deadline', $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('Y-m-d') : '') }}">
                    </div>
                    @error('deadline') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Link Pembelian -->
                <div>
                    <label for="purchase_link" class="block text-sm font-medium text-heading mb-1">Link Pembelian / Referensi (Opsional)</label>
                    <div class="flex shadow-sm rounded-lg">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted">
                            <i class="ti ti-link text-xl"></i>
                        </span>
                        <input type="url" name="purchase_link" id="purchase_link" class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body" placeholder="Misal: https://tokopedia.com/..." value="{{ old('purchase_link', $goal->purchase_link) }}">
                    </div>
                    @error('purchase_link') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <!-- Keterangan (Spans 2 Columns) -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-heading mb-1">Keterangan / Catatan (Opsional)</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5 shadow-sm" placeholder="Tuliskan spesifikasi, alasan menabung, atau catatan tambahan...">{{ old('description', $goal->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Footer Form -->
            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-border">
                <a href="{{ route('goals.index') }}" class="px-5 py-2.5 text-sm font-medium text-heading bg-surface border border-border rounded-lg hover:bg-background transition-colors flex items-center">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-surface bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center shadow-sm shadow-primary/30">
                    <i class="ti ti-device-floppy mr-2 text-lg"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- JS Logic Mencegah Multiple Submit -->
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
