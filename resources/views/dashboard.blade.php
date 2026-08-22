<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-heading font-bold text-heading">Overview Keuangan</h2>
            <div class="text-sm font-medium text-muted bg-background px-4 py-2 rounded-full border border-border">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        </div>
    </x-slot>

    <!-- SECTION 1: METRIK KEUANGAN (CARDS) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Total Kekayaan (Hero Card dengan Gradient Premium) -->
        <div class="lg:col-span-1 bg-gradient-to-br from-primary to-primary-600 rounded-2xl p-6 shadow-lg shadow-primary/30 relative overflow-hidden text-surface">
            <!-- Dekorasi Pattern Background -->
            <div class="absolute -top-10 -right-10 opacity-20">
                <i class="ti ti-wallet text-[120px]"></i>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-black/10 to-transparent"></div>
            
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div>
                    <p class="text-sm font-medium text-surface/80 mb-1 flex items-center">
                        <i class="ti ti-building-bank mr-2 text-lg"></i> Total Kekayaan
                    </p>
                    <h3 class="text-4xl font-heading font-bold tracking-tight">
                        Rp {{ number_format($totalBalance, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="mt-6 pt-4 border-t border-surface/20 flex justify-between items-center">
                    <p class="text-xs font-medium text-surface/80">Dari seluruh dompet & rekening</p>
                    <a href="{{ route('wallets.index') }}" class="w-8 h-8 rounded-full bg-surface/20 flex items-center justify-center hover:bg-surface/30 transition-colors">
                        <i class="ti ti-arrow-right text-surface"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Arus Kas (Income & Expense) -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pemasukan Bulan Ini -->
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm flex flex-col justify-between hover:border-success/30 transition-colors group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-success/10 text-success flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="ti ti-arrow-down-left text-2xl"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-background text-muted text-xs font-bold rounded-full border border-border uppercase tracking-wide">
                        {{ \Carbon\Carbon::now()->translatedFormat('F') }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-muted mb-1">Pemasukan Bulan Ini</p>
                    <h3 class="text-3xl font-heading font-bold text-heading">
                        Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <!-- Pengeluaran Bulan Ini -->
            <div class="bg-surface rounded-2xl border border-border p-6 shadow-sm flex flex-col justify-between hover:border-danger/30 transition-colors group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-danger/10 text-danger flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="ti ti-arrow-up-right text-2xl"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-background text-muted text-xs font-bold rounded-full border border-border uppercase tracking-wide">
                        {{ \Carbon\Carbon::now()->translatedFormat('F') }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-muted mb-1">Pengeluaran Bulan Ini</p>
                    <h3 class="text-3xl font-heading font-bold text-heading">
                        Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: TRANSAKSI TERAKHIR -->
    <div class="bg-surface border border-border rounded-2xl overflow-hidden shadow-sm w-full mb-8">
        <div class="p-6 border-b border-border flex justify-between items-center">
            <h3 class="text-lg font-heading font-bold text-heading flex items-center">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center mr-3">
                    <i class="ti ti-history text-lg"></i>
                </div>
                Transaksi Terakhir
            </h3>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-background border border-border rounded-lg text-sm font-medium text-heading hover:bg-border/50 hover:text-primary transition-colors">
                Lihat Semua
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-border">
                    @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-background/50 transition-colors group">
                            <!-- Kolom 1: Ikon Besar & Nama Kategori -->
                            <td class="px-6 py-4 w-1/3">
                                <div class="flex items-center space-x-4">
                                    @if($trx->type === 'transfer')
                                        <div class="w-12 h-12 rounded-full bg-info/10 text-info flex items-center justify-center flex-shrink-0">
                                            <i class="ti ti-arrows-exchange text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-base font-bold text-heading">Transfer Saldo</p>
                                            <p class="text-xs text-muted mt-0.5 uppercase tracking-wider">{{ $trx->type }}</p>
                                        </div>
                                    @elseif($trx->type === 'income')
                                        <div class="w-12 h-12 rounded-full bg-success/10 text-success flex items-center justify-center flex-shrink-0">
                                            <i class="ti {{ $trx->category->icon ?? 'ti-circle' }} text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-base font-bold text-heading">{{ $trx->category->name ?? 'Tanpa Kategori' }}</p>
                                            <p class="text-xs text-muted mt-0.5 uppercase tracking-wider">{{ $trx->type }}</p>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-danger/10 text-danger flex items-center justify-center flex-shrink-0">
                                            <i class="ti {{ $trx->category->icon ?? 'ti-circle' }} text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-base font-bold text-heading">{{ $trx->category->name ?? 'Tanpa Kategori' }}</p>
                                            <p class="text-xs text-muted mt-0.5 uppercase tracking-wider">{{ $trx->type }}</p>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom 2: Tanggal & Dompet -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1.5">
                                    <div class="flex items-center text-sm font-medium text-heading">
                                        <i class="ti ti-calendar text-muted mr-2"></i>
                                        {{ \Carbon\Carbon::parse($trx->date)->translatedFormat('d M Y') }}
                                    </div>
                                    
                                    @if($trx->type === 'transfer')
                                        <div class="text-xs text-muted flex items-center">
                                            <i class="ti ti-wallet text-muted mr-2 text-sm"></i>
                                            <span class="font-medium text-danger">{{ $trx->sourceWallet->name ?? '?' }}</span> 
                                            <i class="ti ti-arrow-right mx-1 text-[10px]"></i> 
                                            <span class="font-medium text-success">{{ $trx->destinationWallet->name ?? '?' }}</span>
                                        </div>
                                    @else
                                        <div class="text-xs text-muted flex items-center">
                                            <i class="ti ti-wallet text-muted mr-2 text-sm"></i>
                                            <div class="w-2 h-2 rounded-full mr-1.5" style="background-color: {{ $trx->wallet->color ?? '#ccc' }}"></div>
                                            {{ $trx->wallet->name ?? 'Dihapus' }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom 3: Nominal -->
                            <td class="px-6 py-4 text-right">
                                @if($trx->type === 'income')
                                    <p class="text-xl font-heading font-bold text-success">+ Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @elseif($trx->type === 'expense')
                                    <p class="text-xl font-heading font-bold text-danger">- Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-xl font-heading font-bold text-info">Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @endif
                                
                                @if($trx->description)
                                    <p class="text-xs text-muted mt-1 truncate max-w-[200px] ml-auto" title="{{ $trx->description }}">
                                        {{ $trx->description }}
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-background rounded-full border border-border flex items-center justify-center mb-4">
                                        <i class="ti ti-receipt text-4xl text-muted"></i>
                                    </div>
                                    <h3 class="text-lg font-heading font-bold text-heading mb-1">Belum ada aktivitas</h3>
                                    <p class="text-muted text-sm mb-5">Transaksi yang Anda buat akan muncul di sini.</p>
                                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-5 py-2.5 bg-primary text-surface rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm shadow-primary/30">
                                        <i class="ti ti-plus mr-2 text-lg"></i> Catat Transaksi Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
