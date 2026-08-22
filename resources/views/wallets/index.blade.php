<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-heading font-bold text-heading">Dompet & Rekening</h2>
            <a href="{{ route('wallets.create') }}" class="bg-primary text-surface px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 flex items-center transition-colors shadow-sm">
                <i class="ti ti-plus mr-2 text-lg"></i> Tambah Wallet
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

    <!-- SECTION 1: TOP 3 WALLETS (CARD VIEW) -->
    @if($topWallets->count() > 0)
        <div class="mb-8">
            <h3 class="text-lg font-heading font-bold text-heading mb-4 flex items-center">
                <i class="ti ti-star text-warning mr-2"></i> Sorotan Utama
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topWallets as $wallet)
                    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm flex flex-col relative overflow-hidden transition-all hover:shadow-md">
                        <div class="absolute top-0 left-0 w-full h-1" style="background-color: {{ $wallet->color }}"></div>

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <!-- Penambahan Badge Status di sini -->
                                <h3 class="text-lg font-heading font-bold text-heading flex items-center flex-wrap gap-2">
                                    <span>{{ $wallet->name }}</span>

                                    @if($wallet->is_default)
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-primary/10 text-primary rounded border border-primary/20">Default</span>
                                    @endif

                                    @if($wallet->is_active)
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-success/10 text-success rounded border border-success/20">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-muted/10 text-muted rounded border border-border">Nonaktif</span>
                                    @endif
                                </h3>
                                <p class="text-sm text-muted mt-1">{{ $wallet->type }}</p>
                            </div>
                            <i class="ti ti-wallet text-2xl text-muted"></i>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm text-muted mb-1">Saldo</p>
                            <p class="text-2xl font-heading font-bold text-heading">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                        </div>

                        <div class="mt-auto flex items-center justify-between pt-4 border-t border-border">
                            <div class="flex space-x-2">
                                <a href="{{ route('wallets.edit', $wallet) }}" class="p-2 text-muted hover:text-primary transition-colors" title="Edit">
                                    <i class="ti ti-edit text-xl"></i>
                                </a>
                                <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus wallet ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-muted hover:text-danger transition-colors" title="Hapus">
                                        <i class="ti ti-trash text-xl"></i>
                                    </button>
                                </form>
                            </div>

                            @if(!$wallet->is_default && $wallet->is_active)
                                <form action="{{ route('wallets.set-default', $wallet) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-primary hover:underline">
                                        Jadikan Default
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty State jika belum ada dompet sama sekali -->
        <div class="col-span-full bg-surface border border-dashed border-border rounded-xl p-8 flex flex-col items-center justify-center text-center mb-8">
            <i class="ti ti-wallet text-4xl text-muted mb-3"></i>
            <h3 class="text-lg font-heading font-bold text-heading mb-1">Belum ada Wallet</h3>
            <p class="text-muted text-sm mb-4">Mulai kelola keuangan Anda dengan menambahkan sumber dana pertama.</p>
            <a href="{{ route('wallets.create') }}" class="bg-primary text-surface px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90">
                Tambah Wallet Pertama
            </a>
        </div>
    @endif

    <!-- SECTION 2: THE REST OF WALLETS (TABLE VIEW) -->
    @if($tableWallets->total() > 0)
        <div>
            <h3 class="text-lg font-heading font-bold text-heading mb-4 flex items-center">
                <i class="ti ti-list text-primary mr-2"></i> Dompet Lainnya
            </h3>

            <div class="bg-surface border border-border rounded-xl overflow-hidden shadow-sm w-full mb-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-background/50 border-b border-border text-sm text-muted">
                                <th class="px-6 py-4 font-medium">Informasi Wallet</th>
                                <th class="px-6 py-4 font-medium">Tipe</th>
                                <th class="px-6 py-4 font-medium text-right">Saldo</th>
                                <th class="px-6 py-4 font-medium text-center">Status</th>
                                <th class="px-6 py-4 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($tableWallets as $wallet)
                                <tr class="hover:bg-background/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $wallet->color }}"></div>
                                            <div>
                                                <p class="font-heading font-bold text-heading text-lg">{{ $wallet->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm border border-border bg-background px-2.5 py-1 rounded-md text-muted inline-flex items-center">
                                            @if($wallet->type == 'Bank') <i class="ti ti-building-bank mr-1.5 text-lg"></i>
                                            @elseif($wallet->type == 'E-Wallet') <i class="ti ti-device-mobile mr-1.5 text-lg"></i>
                                            @elseif($wallet->type == 'Cash') <i class="ti ti-cash mr-1.5 text-lg"></i>
                                            @else <i class="ti ti-wallet mr-1.5 text-lg"></i>
                                            @endif
                                            {{ $wallet->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-lg font-heading font-bold text-heading">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($wallet->is_active)
                                            <span class="text-sm text-success flex items-center justify-center"><i class="ti ti-circle-check-filled mr-1.5 text-lg"></i> Aktif</span>
                                        @else
                                            <span class="text-sm text-muted flex items-center justify-center"><i class="ti ti-circle-x-filled mr-1.5 text-lg"></i> Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <!-- Grup Tombol Aksi - Selalu Tampil (Default Visible) -->
                                        <div class="flex items-center justify-end space-x-2">
                                            @if(!$wallet->is_default && $wallet->is_active)
                                                <form action="{{ route('wallets.set-default', $wallet) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-medium border border-border bg-background hover:bg-primary/10 hover:text-primary hover:border-primary/30 text-heading px-3 py-1.5 rounded transition-colors">
                                                        Jadikan Default
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('wallets.edit', $wallet) }}" class="p-1.5 bg-background border border-border rounded text-muted hover:text-primary hover:border-primary/30 transition-colors" title="Edit">
                                                <i class="ti ti-edit text-lg"></i>
                                            </a>
                                            <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus wallet ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-background border border-border rounded text-muted hover:text-danger hover:border-danger/30 transition-colors" title="Hapus">
                                                    <i class="ti ti-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Controls -->
            <div class="mt-4">
                {{ $tableWallets->links() }}
            </div>
        </div>
    @endif
</x-app-layout>
