<x-app-layout>
    <!-- Tambahkan CDN ApexCharts di bagian atas -->
    <x-slot name="header">
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </h2>
            <div class="text-sm font-medium text-slate-500 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-100">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        <!-- 3 Kartu Ringkasan Utama -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kartu Total Saldo -->
            <div class="bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl p-6 text-white shadow-md shadow-teal-600/20 relative overflow-hidden">
                <i class="ti ti-wallet absolute -right-4 -bottom-4 text-9xl opacity-10 rotate-12"></i>
                <p class="text-teal-50 text-sm font-medium tracking-wide uppercase mb-1">Total Kekayaan</p>
                <h3 class="text-3xl font-bold">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
                <div class="mt-4 flex items-center gap-2 text-xs bg-black/10 w-fit px-3 py-1.5 rounded-lg backdrop-blur-sm">
                    <i class="ti ti-building-bank text-base"></i>
                    Dari seluruh dompet aktif
                </div>
            </div>

            <!-- Kartu Pemasukan -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium tracking-wide uppercase mb-1">Pemasukan Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <i class="ti ti-arrow-down-left text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Kartu Pengeluaran -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium tracking-wide uppercase mb-1">Pengeluaran Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i class="ti ti-arrow-up-right text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Area Grafik & Transaksi Terakhir -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- GRAFIK SCATTER (Lebar 2 Kolom) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Aktivitas Arus Kas</h3>
                    <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-3 py-1 rounded-full">7 Hari Terakhir</span>
                </div>
                <!-- Wadah untuk Chart -->
                <div id="cashflowChart" class="w-full h-72"></div>
            </div>

            <!-- TRANSAKSI TERAKHIR (Lebar 1 Kolom) -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Aktivitas Terakhir</h3>
                    <a href="{{ url('/transactions') }}" class="text-sm font-semibold text-teal-600 hover:text-teal-700 transition">Lihat Semua</a>
                </div>

                <div class="space-y-5 flex-1 overflow-y-auto">
                    @forelse($recentTransactions as $trx)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $trx->type == 'income' ? 'bg-teal-50 text-teal-600' : 'bg-rose-50 text-rose-600' }}">
                                    <i class="{{ $trx->category->icon ?? 'ti ti-tag' }} text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 line-clamp-1">{{ $trx->category->name ?? 'Transfer' }}</p>
                                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }} • {{ $trx->wallet->name ?? '' }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold {{ $trx->type == 'income' ? 'text-teal-600' : 'text-rose-600' }}">
                                {{ $trx->type == 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-10 flex flex-col items-center">
                            <i class="ti ti-receipt-off text-4xl text-slate-300 mb-2"></i>
                            <p class="text-sm text-slate-500">Belum ada transaksi</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Script Inisialisasi ApexCharts -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                chart: {
                    type: 'line', // Menggunakan base 'line' agar sumbu X sejajar sempurna
                    height: 320,
                    fontFamily: "'Plus Jakarta Sans', sans-serif",
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    dropShadow: {
                        enabled: true,
                        top: 4,
                        left: 0,
                        blur: 3,
                        color: '#000',
                        opacity: 0.08
                    }
                },
                stroke: {
                    width: 0 // Menghilangkan garis penyambung agar tetap tampil sebagai titik-titik murni
                },
                series: [
                    {
                        name: 'Pemasukan',
                        data: {!! json_encode($chartIncome) !!}
                    },
                    {
                        name: 'Pengeluaran',
                        data: {!! json_encode($chartExpense) !!}
                    }
                ],
                colors: ['#0d9488', '#e11d48'], // Teal 600, Rose 600
                markers: {
                    size: 7,
                    strokeWidth: 3,
                    strokeColors: '#ffffff',
                    hover: { size: 9 }
                },
                xaxis: {
                    categories: {!! json_encode($chartDates) !!},
                    tooltip: { enabled: false },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px', fontWeight: 500 }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px', fontWeight: 500 },
                        formatter: function (value) {
                            if (value >= 1000000) return "Rp " + (value / 1000000).toFixed(1) + "M";
                            if (value >= 1000) return "Rp " + (value / 1000).toFixed(0) + "K";
                            return "Rp " + value;
                        }
                    }
                },
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 4,
                    row: {
                        colors: ['#f8fafc', 'transparent'], // Latar belakang belang-belang halus (Zebra striping)
                        opacity: 1
                    },
                    xaxis: { lines: { show: true } }, // Menampilkan garis vertikal pendukung
                    yaxis: { lines: { show: true } }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    markers: { radius: 12 },
                    itemMargin: { horizontal: 10, vertical: 0 }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: 'light',
                    y: {
                        formatter: function (y) {
                            if (typeof y !== "undefined") {
                                return "Rp " + y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                            return y;
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#cashflowChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
