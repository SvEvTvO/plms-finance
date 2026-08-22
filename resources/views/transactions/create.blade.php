<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('transactions.index') }}" class="p-2 bg-surface border border-border rounded-lg text-muted hover:text-primary transition-colors" title="Kembali">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-heading font-bold text-heading">Catat Transaksi</h2>
        </div>
    </x-slot>

    @if(session('error'))
        <div class="mb-6 p-4 bg-danger/10 border border-danger/20 text-danger rounded-xl flex items-center">
            <i class="ti ti-alert-circle w-5 h-5 mr-2 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm w-full">
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            <!-- Hidden input untuk menyimpan tipe transaksi yang dipilih -->
            <input type="hidden" name="type" id="typeInput" value="{{ old('type', 'expense') }}">

            <div class="space-y-8">

                <!-- 1. Pemilihan Tipe Transaksi Berwarna (Segmented Controls) -->
                <div>
                    <label class="block text-sm font-medium text-heading mb-3 text-center md:text-left">Tipe Transaksi</label>
                    <div class="grid grid-cols-3 gap-2 bg-background p-1.5 rounded-xl border border-border">

                        <button type="button" onclick="setType('expense')" id="btn-expense" class="type-btn py-2.5 px-4 rounded-lg font-medium text-sm flex flex-col md:flex-row items-center justify-center transition-all">
                            <i class="ti ti-arrow-up-right text-lg md:mr-2 mb-1 md:mb-0"></i>
                            <span>Pengeluaran</span>
                        </button>

                        <button type="button" onclick="setType('income')" id="btn-income" class="type-btn py-2.5 px-4 rounded-lg font-medium text-sm flex flex-col md:flex-row items-center justify-center transition-all">
                            <i class="ti ti-arrow-down-left text-lg md:mr-2 mb-1 md:mb-0"></i>
                            <span>Pemasukan</span>
                        </button>

                        <button type="button" onclick="setType('transfer')" id="btn-transfer" class="type-btn py-2.5 px-4 rounded-lg font-medium text-sm flex flex-col md:flex-row items-center justify-center transition-all">
                            <i class="ti ti-arrows-exchange text-lg md:mr-2 mb-1 md:mb-0"></i>
                            <span>Transfer</span>
                        </button>

                    </div>
                    @error('type') <p class="mt-2 text-sm text-danger text-center md:text-left">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- 2. Tanggal (Desain Input Group Baru) -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-heading mb-1">Tanggal</label>
                        <div class="flex shadow-sm rounded-lg">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted">
                                <i class="ti ti-calendar-event text-xl"></i>
                            </span>
                            <input type="date" name="date" id="date" required class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body" value="{{ old('date', date('Y-m-d')) }}">
                        </div>
                        @error('date') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <!-- 3. Nominal (Desain Input Group Baru) -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-heading mb-1">Nominal</label>
                        <div class="flex shadow-sm rounded-lg">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-border bg-background text-muted font-bold">
                                Rp
                            </span>
                            <input type="number" name="amount" id="amount" required min="1" step="0.01" class="flex-1 min-w-0 block w-full px-3 py-2.5 rounded-none rounded-r-lg border-border focus:border-primary focus:ring-primary/20 text-body font-medium text-lg" placeholder="0" value="{{ old('amount') }}">
                        </div>
                        @error('amount') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <!-- 4. Area Income/Expense -->
                    <div id="area-standard" class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="wallet_id" class="block text-sm font-medium text-heading mb-1">Dompet / Sumber Dana</label>
                            <select name="wallet_id" id="wallet_id" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5">
                                <option value="">Pilih Dompet</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                        {{ $wallet->name }} (Saldo: Rp {{ number_format($wallet->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('wallet_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-heading mb-1">Kategori</label>
                            <select name="category_id" id="category_id" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5">
                                <option value="">Pilih Kategori</option>
                                <optgroup label="Pengeluaran (Expense)">
                                    @foreach($categories->where('type', 'expense') as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Pemasukan (Income)">
                                    @foreach($categories->where('type', 'income') as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('category_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- 5. Area Transfer -->
                    <div id="area-transfer" class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                        <div>
                            <label for="source_wallet_id" class="block text-sm font-medium text-heading mb-1 text-danger">Dari Dompet (Dikurangi)</label>
                            <select name="source_wallet_id" id="source_wallet_id" class="w-full rounded-lg border-danger/30 focus:border-danger focus:ring-danger/20 text-body bg-background py-2.5">
                                <option value="">Pilih Dompet Asal</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" {{ old('source_wallet_id') == $wallet->id ? 'selected' : '' }}>
                                        {{ $wallet->name }} (Saldo: Rp {{ number_format($wallet->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('source_wallet_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="destination_wallet_id" class="block text-sm font-medium text-heading mb-1 text-success">Ke Dompet (Ditambah)</label>
                            <select name="destination_wallet_id" id="destination_wallet_id" class="w-full rounded-lg border-success/30 focus:border-success focus:ring-success/20 text-body bg-background py-2.5">
                                <option value="">Pilih Dompet Tujuan</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" {{ old('destination_wallet_id') == $wallet->id ? 'selected' : '' }}>
                                        {{ $wallet->name }} (Saldo: Rp {{ number_format($wallet->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('destination_wallet_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- 6. Deskripsi -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-heading mb-1">Deskripsi / Catatan (Opsional)</label>
                        <textarea name="description" id="description" rows="2" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-body bg-background py-2.5" placeholder="Misal: Beli makan siang, Gaji bulanan...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Footer Form -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-border">
                    <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 text-sm font-medium text-heading bg-surface border border-border rounded-lg hover:bg-background transition-colors flex items-center">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-surface bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center shadow-sm shadow-primary/30">
                        <i class="ti ti-device-floppy mr-2 text-lg"></i>
                        Simpan Transaksi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- JS Logic untuk Button Warna, Area Dinamis & Multiple Submit Protection -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // State
            let currentType = document.getElementById('typeInput').value;

            // Elements
            const typeInput = document.getElementById('typeInput');
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            const areaStandard = document.getElementById('area-standard');
            const areaTransfer = document.getElementById('area-transfer');
            
            // Standard Inputs
            const walletInput = document.getElementById('wallet_id');
            const categoryInput = document.getElementById('category_id');
            
            // Transfer Inputs
            const sourceWalletInput = document.getElementById('source_wallet_id');
            const destWalletInput = document.getElementById('destination_wallet_id');
            
            // Buttons
            const btnExpense = document.getElementById('btn-expense');
            const btnIncome = document.getElementById('btn-income');
            const btnTransfer = document.getElementById('btn-transfer');

            // --- FUNGSI 1: MENGUBAH TIPE TRANSAKSI ---
            // Karena fungsi ini dipanggil dari atribut onclick="setType('...')", 
            // kita harus mengeksposnya ke global window.
            window.setType = function(type) {
                currentType = type;
                typeInput.value = type;
                renderUI();
            };

            // --- FUNGSI 2: MERENDER UI & MENGATUR INPUT AKTIF ---
            function renderUI() {
                // 1. Reset class semua button
                [btnExpense, btnIncome, btnTransfer].forEach(btn => {
                    btn.className = 'type-btn py-2.5 px-4 rounded-lg font-medium text-sm flex flex-col md:flex-row items-center justify-center transition-all text-muted hover:bg-border/50 bg-transparent';
                });

                // 2. Beri warna pada button yang aktif & Atur form dinamis
                if (currentType === 'expense') {
                    btnExpense.className = 'type-btn py-2.5 px-4 rounded-lg font-medium text-sm flex flex-col md:flex-row items-center justify-center transition-all bg-danger text-surface shadow-md shadow-danger/20';
                    areaStandard.classList.remove('hidden');
                    areaTransfer.classList.add('hidden');
                    
                    // Disable transfer inputs agar lolos validasi Laravel
                    walletInput.disabled = false;
                    categoryInput.disabled = false;
                    sourceWalletInput.disabled = true;
                    destWalletInput.disabled = true;
                } 
                else if (currentType === 'income') {
                    btnIncome.className = 'type-btn py-2.5 px-4 rounded-lg font-medium text-sm flex flex-col md:flex-row items-center justify-center transition-all bg-success text-surface shadow-md shadow-success/20';
                    areaStandard.classList.remove('hidden');
                    areaTransfer.classList.add('hidden');
                    
                    walletInput.disabled = false;
                    categoryInput.disabled = false;
                    sourceWalletInput.disabled = true;
                    destWalletInput.disabled = true;
                } 
                else if (currentType === 'transfer') {
                    btnTransfer.className = 'type-btn py-2.5 px-4 rounded-lg font-medium text-sm flex flex-col md:flex-row items-center justify-center transition-all bg-warning text-surface shadow-md shadow-warning/20';
                    areaStandard.classList.add('hidden');
                    areaTransfer.classList.remove('hidden');
                    
                    // Disable standard inputs agar lolos validasi Laravel
                    walletInput.disabled = true;
                    categoryInput.disabled = true;
                    sourceWalletInput.disabled = false;
                    destWalletInput.disabled = false;
                }
            }

            // --- FUNGSI 3: MENCEGAH MULTIPLE SUBMIT (DUPLIKASI DATA) ---
            form.addEventListener('submit', function(e) {
                // Jika tombol belum didisable, jalankan form dan disable tombolnya
                if (!submitBtn.disabled) {
                    submitBtn.disabled = true;
                    
                    // Simpan state konten tombol lama jika sewaktu-waktu submit gagal di client-side
                    const originalContent = submitBtn.innerHTML;
                    
                    submitBtn.innerHTML = '<i class="ti ti-loader-2 animate-spin mr-2 text-lg"></i> Memproses...';
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                } else {
                    // Jika tombol sudah didisable, blokir pengiriman form selanjutnya
                    e.preventDefault();
                }
            });

            // Jalankan UI pertama kali
            renderUI();
        });
    </script>
</x-app-layout>
