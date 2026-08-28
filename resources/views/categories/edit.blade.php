<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('categories.index') }}" class="p-2 bg-surface border border-border rounded-lg text-muted hover:text-primary transition-colors" title="Kembali">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-heading font-bold text-heading">Edit Kategori: {{ $category->name }}</h2>
        </div>
    </x-slot>

    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm w-full">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Kategori -->
                <div>
                    <label for="name" class="block text-sm font-medium text-heading mb-1">Nama Kategori</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ti ti-tag text-muted text-lg"></i>
                        </div>
                        <input type="text" name="name" id="name" required class="w-full pl-10 rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5" value="{{ old('name', $category->name) }}">
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
                            <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                            <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                        </select>
                    </div>
                    @error('type') <p class="mt-1 text-sm text-danger flex items-center"><i class="ti ti-alert-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Input Ikon (Searchable Dropdown dengan Alpine.js) -->
                <div x-data="iconPicker('{{ old('icon', $category->icon ?? 'ti-tag') }}')" class="relative mb-5">
                    <label for="icon" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Pilih Ikon</label>

                    <!-- Input Tersembunyi (Ini yang dikirim ke Database) -->
                    <input type="hidden" name="icon" x-model="fullIconClass">

                    <!-- Input Pencarian Visual -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i :class="fullIconClass + ' text-xl text-teal-600'"></i>
                        </div>
                        <input type="text"
                            x-model="search"
                            @focus="open = true"
                            @click.away="open = false"
                            @keydown.escape="open = false"
                            placeholder="Ketik nama ikon (cth: school, car, coffee)..."
                            class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-teal-500 rounded-xl text-sm font-medium transition"
                            autocomplete="off" />
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <i class="ti ti-chevron-down text-slate-400 transition" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                    </div>

                    <!-- Dropdown Hasil Pencarian / Rekomendasi -->
                    <div x-show="open"
                        x-transition.opacity
                        class="absolute z-50 w-full mt-2 bg-white border border-slate-100 shadow-xl rounded-xl max-h-60 overflow-y-auto"
                        style="display: none;">

                        <!-- Jika Ikon Tidak Ditemukan -->
                        <template x-if="filteredIcons.length === 0">
                            <div class="px-4 py-3 text-sm text-slate-500 text-center flex flex-col items-center gap-1">
                                <i class="ti ti-ghost text-2xl text-slate-300"></i>
                                Tidak menemukan ikon
                            </div>
                        </template>

                        <!-- Daftar Ikon yang Ditemukan -->
                        <template x-for="iconName in filteredIcons" :key="iconName">
                            <div @click="selectIcon(iconName)"
                                class="flex items-center gap-3 px-4 py-2 hover:bg-teal-50 cursor-pointer border-b border-slate-50 last:border-0 transition">
                                <div class="p-2 bg-white border border-slate-100 shadow-sm rounded-lg text-teal-600">
                                    <i :class="'ti ti-' + iconName + ' text-lg'"></i>
                                </div>
                                <span x-text="iconName" class="text-sm font-medium text-slate-700 capitalize"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-border">
                <a href="{{ route('categories.index') }}" class="px-5 py-2.5 text-sm font-medium text-heading bg-surface border border-border rounded-lg hover:bg-background transition-colors flex items-center">
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

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('iconPicker', (initialValue) => ({
            open: false,
            search: '',
            selected: '',

            // Koleksi Ikon PLMS-Finance (Bisa Anda tambah/kurangi nanti)
            icons: [
                // Edukasi & Transportasi
                'school', 'books', 'car', 'bus', 'train', 'plane', 'bike', 'scooter',
                // Belanja & Gaya Hidup
                'shopping-cart', 'basket', 'bag', 'building-store', 'shirt', 'hanger', 'gift',
                // Hiburan & Gadget
                'device-gamepad', 'device-tv', 'device-mobile', 'headset', 'movie', 'music',
                // Makanan & Minuman
                'coffee', 'cup', 'pizza', 'soup', 'meat', 'bottle', 'tools-kitchen-2',
                // Keuangan & Bisnis
                'wallet', 'coin', 'cash', 'credit-card', 'building-bank', 'chart-bar', 'tag',
                // Kesehatan & Rumah
                'heart', 'medical-cross', 'pill', 'stethoscope', 'home', 'building',
                // Lain-lain
                'tools', 'wifi', 'bolt', 'droplet', 'flame', 'baby-carriage'
            ],

            init() {
                // Bersihkan awalan 'ti ti-' saat me-load data untuk mode "Edit"
                this.selected = initialValue.replace('ti ti-', '').replace('ti-', '');
                this.search = this.selected;
            },

            get filteredIcons() {
                // Jika search kosong, atau baru selesai pilih, tampilkan semua ikon
                if (this.search === '' || this.search === this.selected) {
                    return this.icons;
                }
                // Filter berdasarkan huruf yang diketik
                return this.icons.filter(i => i.toLowerCase().includes(this.search.toLowerCase()));
            },

            get fullIconClass() {
                // Menggabungkan kembali class lengkap untuk disimpan ke database
                return 'ti ti-' + (this.selected || 'tag'); // Default-nya ti-tag
            },

            selectIcon(iconName) {
                this.selected = iconName;
                this.search = iconName;
                this.open = false; // Tutup pop-up setelah memilih
            }
        }));
    });
    </script>
</x-app-layout>
