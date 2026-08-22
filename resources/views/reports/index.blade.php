<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-heading font-bold text-heading">Laporan & Ekspor Data</h2>
    </x-slot>

    <!-- SECTION 1: SUMMARY CARDS (DI PINDAH KE ATAS & DIBERI WARNA SESUAI PERAN) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface rounded-xl border border-success/30 p-5 shadow-sm border-l-4 border-l-success">
            <p class="text-sm font-medium text-muted mb-1 flex items-center"><i class="ti ti-arrow-down-left mr-2 text-success"></i> Total Pemasukan</p>
            <h3 class="text-2xl font-heading font-bold text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-surface rounded-xl border border-danger/30 p-5 shadow-sm border-l-4 border-l-danger">
            <p class="text-sm font-medium text-muted mb-1 flex items-center"><i class="ti ti-arrow-up-right mr-2 text-danger"></i> Total Pengeluaran</p>
            <h3 class="text-2xl font-heading font-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-surface rounded-xl border {{ $netIncome >= 0 ? 'border-primary/30 border-l-primary' : 'border-danger/30 border-l-danger' }} p-5 shadow-sm border-l-4">
            <p class="text-sm font-medium text-muted mb-1 flex items-center"><i class="ti ti-scale mr-2 text-primary"></i> Selisih (Net)</p>
            <h3 class="text-2xl font-heading font-bold {{ $netIncome >= 0 ? 'text-primary' : 'text-danger' }}">
                Rp {{ number_format($netIncome, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    <!-- SECTION 2: CHART PERGERAKAN KEUANGAN -->
    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm mb-8">
        <h3 class="text-lg font-heading font-bold text-heading mb-4 flex items-center">
            <i class="ti ti-chart-line text-primary mr-2"></i> Pergerakan Keuangan
        </h3>
        <!-- Container untuk ApexCharts -->
        <div id="financeChart" class="w-full h-64"></div>
    </div>

    <!-- SECTION 3: FILTER & EXPORT FORM -->
    <div class="bg-surface rounded-xl border border-border p-6 shadow-sm mb-6">
        <form action="{{ route('reports.index') }}" method="GET">
            
            <!-- MOBILE ONLY: PILIHAN MASTER -->
            <div class="block lg:hidden mb-4">
                <label for="mobile_master" class="block text-xs font-bold text-heading mb-2 uppercase tracking-wide">Pilihan Master Filter</label>
                <select id="mobile_master" onchange="toggleMobileFilter()" class="w-full rounded-lg border-2 border-border focus:border-primary focus:ring-primary/20 text-sm bg-background py-2.5 font-medium">
                    <option value="tanggal">🗓️ Berdasarkan Tanggal</option>
                    <option value="jenis">🏷️ Berdasarkan Jenis Transaksi</option>
                    <option value="kategori">📁 Berdasarkan Kategori</option>
                </select>
            </div>

            <!-- INPUT FIELD CONTAINER -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-end">
                
                <!-- Tanggal Group -->
                <div id="filter_tanggal" class="lg:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-medium text-muted mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-sm bg-background py-2">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-medium text-muted mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-sm bg-background py-2">
                    </div>
                </div>

                <!-- Tipe Transaksi Group -->
                <div id="filter_jenis">
                    <label for="type" class="block text-xs font-medium text-muted mb-1">Jenis Transaksi</label>
                    <select name="type" id="type" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-sm bg-background py-2">
                        <option value="">Semua Jenis</option>
                        <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="transfer" {{ $type === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>

                <!-- Kategori Group -->
                <div id="filter_kategori">
                    <label for="category_id" class="block text-xs font-medium text-muted mb-1">Kategori</label>
                    <select name="category_id" id="category_id" class="w-full rounded-lg border-border focus:border-primary focus:ring-primary/20 text-sm bg-background py-2">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="mt-5 flex space-x-3 pt-4 border-t border-border">
                <button type="submit" class="flex-1 lg:flex-none lg:px-8 bg-primary text-surface py-2.5 rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors shadow-sm">
                    <i class="ti ti-filter mr-1"></i> Terapkan Filter
                </button>
                <a href="{{ route('reports.export', request()->all()) }}" class="flex-1 lg:flex-none lg:px-8 bg-success text-surface py-2.5 rounded-lg text-sm font-bold hover:bg-success/90 transition-colors shadow-sm text-center flex items-center justify-center">
                    <i class="ti ti-file-spreadsheet mr-1"></i> Unduh Excel
                </a>
            </div>
        </form>
    </div>

    <!-- SECTION 4: DATA TABLE (DENGAN LABEL WARNA & PAGINATION) -->
    <div class="bg-surface border border-border rounded-xl overflow-hidden shadow-sm w-full">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-background/50 border-b border-border">
                    <tr class="text-xs font-medium text-muted uppercase tracking-wider">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Kategori & Ket</th>
                        <th class="px-6 py-3">Sumber / Tujuan</th>
                        <th class="px-6 py-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-background/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap w-40">
                                <div class="text-sm font-bold text-heading mb-1">{{ \Carbon\Carbon::parse($trx->date)->translatedFormat('d M Y') }}</div>
                                <!-- Pewarnaan Label Jenis Transaksi -->
                                @if($trx->type === 'income')
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-success/10 text-success rounded border border-success/20">Income</span>
                                @elseif($trx->type === 'expense')
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-danger/10 text-danger rounded border border-danger/20">Expense</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-info/10 text-info rounded border border-info/20">Transfer</span>
                                @endif
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
                                    <div class="text-xs text-muted mt-1 truncate max-w-[200px]" title="{{ $trx->description }}">{{ $trx->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->type === 'transfer')
                                    <div class="text-xs text-muted flex items-center">
                                        <span class="text-danger font-medium">{{ $trx->sourceWallet->name ?? '?' }}</span> 
                                        <i class="ti ti-arrow-right mx-1 text-[10px]"></i> 
                                        <span class="text-success font-medium">{{ $trx->destinationWallet->name ?? '?' }}</span>
                                    </div>
                                @else
                                    <div class="text-sm text-heading font-medium flex items-center">
                                        <i class="ti ti-wallet mr-1.5 text-muted"></i> {{ $trx->wallet->name ?? 'Dihapus' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($trx->type === 'income')
                                    <p class="text-base font-bold text-success">+ Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @elseif($trx->type === 'expense')
                                    <p class="text-base font-bold text-danger">- Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-base font-bold text-info">Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-muted">
                                <i class="ti ti-file-search text-4xl mb-3 block text-muted/50"></i>
                                <p class="text-sm font-medium">Tidak ada transaksi yang sesuai dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- BAGIAN PAGINATION -->
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-border bg-background/30">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- SCRIPT UNTUK APEXCHARTS & LOGIKA MOBILE FILTER -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Logika Toggle Mobile Filter ("PILIHAN MASTER")
        function toggleMobileFilter() {
            const isDesktop = window.innerWidth >= 1024; // Tailwind 'lg' breakpoint
            const filterTanggal = document.getElementById('filter_tanggal');
            const filterJenis = document.getElementById('filter_jenis');
            const filterKategori = document.getElementById('filter_kategori');

            if (isDesktop) {
                // Di Desktop, tampilkan semua secara sejajar (inline)
                filterTanggal.style.display = 'grid';
                filterJenis.style.display = 'block';
                filterKategori.style.display = 'block';
            } else {
                // Di Mobile, tampilkan HANYA yang dipilih oleh Master Select
                const masterValue = document.getElementById('mobile_master').value;
                filterTanggal.style.display = (masterValue === 'tanggal') ? 'grid' : 'none';
                filterJenis.style.display = (masterValue === 'jenis') ? 'block' : 'none';
                filterKategori.style.display = (masterValue === 'kategori') ? 'block' : 'none';
            }
        }

        // Jalankan saat halaman dimuat dan saat layar di-resize (responsif)
        window.addEventListener('resize', toggleMobileFilter);
        document.addEventListener('DOMContentLoaded', function() {
            toggleMobileFilter();

            // Render ApexCharts
            var options = {
                series: [{
                    name: 'Pemasukan',
                    data: {!! json_encode($incomes) !!}
                }, {
                    name: 'Pengeluaran',
                    data: {!! json_encode($expenses) !!}
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                colors: ['#10b981', '#ef4444'], // Success (Green) dan Danger (Red)
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] }
                },
                xaxis: {
                    categories: {!! json_encode($dates) !!},
                    tooltip: { enabled: false },
                    labels: { style: { colors: '#9ca3af' } }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#9ca3af' },
                        formatter: function (value) {
                            return "Rp " + (value / 1000).toFixed(0) + "k"; // Singkat jadi 'k' agar rapi
                        }
                    }
                },
                grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right' }
            };

            var chart = new ApexCharts(document.querySelector("#financeChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
