<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-heading font-bold text-heading">Histori Transaksi</h2>
            <a href="{{ route('transactions.create') }}" class="bg-primary text-surface px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 flex items-center transition-colors shadow-sm">
                <i class="ti ti-plus mr-2 text-lg"></i> Catat Transaksi
            </a>
        </div>
    </x-slot>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-success/10 border border-success/20 text-success rounded-xl flex items-center">
            <i class="ti ti-check w-5 h-5 mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-danger/10 border border-danger/20 text-danger rounded-xl flex items-center">
            <i class="ti ti-alert-circle w-5 h-5 mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- ========================================== -->
    <!-- FILTER TRANSAKSI                           -->
    <!-- ========================================== -->
    <form action="{{ route('transactions.index') }}" method="GET" class="bg-surface border border-border rounded-xl p-5 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Search -->
            <div class="w-full md:w-1/3">
                <label for="search" class="block text-sm font-medium text-heading mb-1.5">Cari Transaksi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ti ti-search text-muted"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Keterangan atau kategori..." class="w-full pl-10 pr-3 py-2 bg-background border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none">
                </div>
            </div>

            <!-- Type -->
            <div class="w-full md:w-1/5">
                <label for="type" class="block text-sm font-medium text-heading mb-1.5">Jenis Transaksi</label>
                <select name="type" id="type" class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none">
                    <option value="">Semua Jenis</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="w-full md:w-1/5">
                <label for="start_date" class="block text-sm font-medium text-heading mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none">
            </div>
            <div class="w-full md:w-1/5">
                <label for="end_date" class="block text-sm font-medium text-heading mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none">
            </div>

            <!-- Buttons -->
            <div class="w-full md:w-auto flex gap-2">
                <button type="submit" class="bg-primary text-surface px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm flex items-center whitespace-nowrap">
                    <i class="ti ti-filter mr-1.5"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'type', 'start_date', 'end_date']))
                    <a href="{{ route('transactions.index') }}" class="bg-background border border-border text-heading px-4 py-2 rounded-lg text-sm font-medium hover:bg-background/80 transition-colors flex items-center whitespace-nowrap" title="Reset Filter">
                        <i class="ti ti-refresh"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- ========================================== -->
    <!-- TABEL TRANSAKSI                            -->
    <!-- ========================================== -->
    <div class="bg-surface border border-border rounded-xl overflow-hidden shadow-sm w-full">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-background/50 border-b border-border text-sm text-muted">
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Kategori / Deskripsi</th>
                        <th class="px-6 py-4 font-medium">Dompet</th>
                        <th class="px-6 py-4 font-medium text-right">Nominal</th>
                        <th class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-background/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-heading">{{ \Carbon\Carbon::parse($trx->date)->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-muted uppercase tracking-wider mt-0.5">{{ $trx->type }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->type === 'transfer')
                                    <div class="flex items-center text-info font-medium">
                                        <i class="ti ti-arrows-exchange text-lg mr-2"></i> Transfer Saldo
                                    </div>
                                @else
                                    <div class="flex items-center text-heading font-medium">
                                        <i class="ti {{ $trx->category->icon ?? 'ti-circle' }} text-lg mr-2 text-muted"></i>
                                        {{ $trx->category->name ?? 'Tanpa Kategori' }}
                                    </div>
                                @endif
                                @if($trx->description)
                                    <div class="text-sm text-muted mt-0.5">{{ $trx->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->type === 'transfer')
                                    <div class="text-sm flex items-center">
                                        <span class="font-medium text-danger">{{ $trx->sourceWallet->name ?? '?' }}</span>
                                        <i class="ti ti-arrow-right mx-2 text-muted"></i>
                                        <span class="font-medium text-success">{{ $trx->destinationWallet->name ?? '?' }}</span>
                                    </div>
                                @else
                                    <span class="text-sm border border-border bg-background px-2.5 py-1 rounded-md text-muted inline-flex items-center">
                                        <div class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $trx->wallet->color ?? '#ccc' }}"></div>
                                        {{ $trx->wallet->name ?? 'Dihapus' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($trx->type === 'income')
                                    <p class="text-lg font-heading font-bold text-success">+ Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @elseif($trx->type === 'expense')
                                    <p class="text-lg font-heading font-bold text-danger">- Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-lg font-heading font-bold text-info">Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('transactions.edit', $trx) }}" class="p-1.5 bg-background border border-border rounded text-muted hover:text-primary hover:border-primary/30 transition-colors" title="Edit">
                                        <i class="ti ti-edit text-lg"></i>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $trx) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Efek saldo dari transaksi ini akan dibatalkan/dikembalikan secara otomatis.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-background border border-border rounded text-muted hover:text-danger hover:border-danger/30 transition-colors" title="Hapus">
                                            <i class="ti ti-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-4">
                                        <i class="ti ti-receipt text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-heading font-bold text-heading mb-1">Belum ada Transaksi</h3>
                                    <p class="text-muted text-sm mb-5">Mulai kelola kas dengan mencatat pemasukan atau pengeluaran Anda.</p>
                                    <a href="{{ route('transactions.create') }}" class="bg-primary text-surface px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-primary/90 shadow-sm shadow-primary/30">
                                        Catat Transaksi
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-border">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
