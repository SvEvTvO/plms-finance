<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-heading font-bold text-heading">Kategori Transaksi</h2>
            <a href="{{ route('categories.create') }}" class="bg-primary text-surface px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 flex items-center transition-colors shadow-sm">
                <i class="ti ti-plus mr-2 text-lg"></i> Tambah Kategori
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Kolom Pemasukan (Income) -->
        <div>
            <h3 class="text-lg font-heading font-bold text-heading mb-4 flex items-center border-b border-border pb-2">
                <div class="w-8 h-8 rounded bg-success/10 text-success flex items-center justify-center mr-3">
                    <i class="ti ti-arrow-down-right text-xl"></i>
                </div>
                Kategori Pemasukan
            </h3>
            
            <div class="bg-surface border border-border rounded-xl overflow-hidden shadow-sm">
                <ul class="divide-y divide-border">
                    @forelse($incomeCategories as $category)
                        <li class="flex items-center justify-between p-4 hover:bg-background/50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-success/5 text-success border border-success/10 flex items-center justify-center">
                                    <i class="ti {{ $category->icon }} text-xl"></i>
                                </div>
                                <span class="font-medium text-heading">{{ $category->name }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('categories.edit', $category) }}" class="p-1.5 text-muted hover:text-primary transition-colors" title="Edit">
                                    <i class="ti ti-edit text-lg"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Transaksi lama tidak akan terhapus, tapi kategori akan disembunyikan.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-muted hover:text-danger transition-colors" title="Hapus">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="p-6 text-center text-muted text-sm">Belum ada kategori pemasukan.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Kolom Pengeluaran (Expense) -->
        <div>
            <h3 class="text-lg font-heading font-bold text-heading mb-4 flex items-center border-b border-border pb-2">
                <div class="w-8 h-8 rounded bg-danger/10 text-danger flex items-center justify-center mr-3">
                    <i class="ti ti-arrow-up-right text-xl"></i>
                </div>
                Kategori Pengeluaran
            </h3>
            
            <div class="bg-surface border border-border rounded-xl overflow-hidden shadow-sm">
                <ul class="divide-y divide-border">
                    @forelse($expenseCategories as $category)
                        <li class="flex items-center justify-between p-4 hover:bg-background/50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-danger/5 text-danger border border-danger/10 flex items-center justify-center">
                                    <i class="ti {{ $category->icon }} text-xl"></i>
                                </div>
                                <span class="font-medium text-heading">{{ $category->name }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('categories.edit', $category) }}" class="p-1.5 text-muted hover:text-primary transition-colors" title="Edit">
                                    <i class="ti ti-edit text-lg"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Transaksi lama tidak akan terhapus, tapi kategori akan disembunyikan.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-muted hover:text-danger transition-colors" title="Hapus">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="p-6 text-center text-muted text-sm">Belum ada kategori pengeluaran.</li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</x-app-layout>
