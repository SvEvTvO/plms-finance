
<x-app-layout>

    {{-- ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- ========================================== -->
    <!-- HERO HEADER CARD                           -->
    <!-- ========================================== -->
    <x-slot name="header">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative overflow-hidden">
            <!-- Decorative Glow Background -->
            <div class="absolute right-0 top-0 w-64 h-64 bg-gradient-to-bl from-teal-100/60 to-transparent rounded-full blur-3xl -z-10 translate-x-1/4 -translate-y-1/4 pointer-events-none"></div>

            <div>
                <p class="text-xs font-bold text-teal-600 mb-1.5 tracking-widest uppercase">Dashboard Keuangan</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                </h2>
                <p class="text-slate-500 mt-1 font-medium text-sm">Berikut ringkasan kondisi keuanganmu hari ini.</p>
            </div>

            <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200 px-4 py-3 rounded-2xl text-sm font-bold text-slate-600 shadow-sm shrink-0">
                <i class="ti ti-calendar-event text-teal-600 text-lg"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        </div>
    </x-slot>


    {{-- =========================
        MAIN CONTENT
    ========================== --}}
    <div class="py-6 sm:py-8">

        <div class="max-w-7xl mx-auto space-y-6">


        <!-- ========================================== -->
        <!-- 3 KARTU RINGKASAN UTAMA                    -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- ====================================== -->
            <!-- TOTAL KEKAYAAN                          -->
            <!-- ====================================== -->
            <div
                class="rounded-3xl p-7 text-white shadow-xl shadow-[#00968a]/20 relative overflow-hidden group"
                style="background: linear-gradient(135deg, #00968a 0%, #12a99c 100%);"
            >

                <!-- Dekorasi Background -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 transition-transform duration-700 group-hover:scale-150"
                ></div>

                <i
                    class="ti ti-wallet absolute -right-6 -bottom-6 text-[120px] text-white/10 rotate-12 group-hover:rotate-0 transition-transform duration-500"
                ></i>


                <div class="relative z-10 flex flex-col h-full justify-between">

                    <div>

                        <p class="text-white/90 text-xs font-bold tracking-widest uppercase mb-1">
                            Total Kekayaan
                        </p>

                        <p class="text-white/65 text-[11px] font-medium mb-4">
                            Seluruh dompet aktif
                        </p>

                        <h3 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                            Rp {{ number_format($totalBalance, 0, ',', '.') }}
                        </h3>

                    </div>


                    <div
                        class="mt-6 flex items-center gap-2 text-xs font-medium bg-white/10 w-fit px-3 py-1.5 rounded-xl backdrop-blur-md border border-white/10"
                    >
                        <i class="ti ti-shield-check text-base"></i>

                        <span>
                            Real-time balance
                        </span>
                    </div>

                </div>

            </div>


            <!-- ====================================== -->
            <!-- PEMASUKAN                               -->
            <!-- ====================================== -->
            <div
                class="bg-white rounded-3xl p-7 border border-slate-100 shadow-sm hover:shadow-lg hover:shadow-[#00968a]/5 transition-all duration-300 flex flex-col justify-between relative overflow-hidden group"
            >

                <!-- Soft Glow -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-[#e6f7f5] rounded-full blur-3xl -mr-10 -mt-10 transition-transform duration-700 group-hover:scale-150 pointer-events-none"
                ></div>


                <div class="flex justify-between items-start mb-6">

                    <div
                        class="w-14 h-14 rounded-2xl bg-[#e6f7f5] border border-[#ccefeb] text-[#00968a] flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300"
                    >
                        <i class="ti ti-arrow-down-left text-2xl"></i>
                    </div>

                    <span
                        class="bg-slate-50 border border-slate-100 text-slate-500 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wider"
                    >
                        Bulan Ini
                    </span>

                </div>


                <div>

                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-1">
                        Pemasukan
                    </p>

                    <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                        Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}
                    </h3>

                </div>


                <!-- Indikator Trend -->
                <div
                    class="mt-4 flex items-center gap-1.5 text-xs font-bold {{ $incomeChange >= 0 ? 'text-[#00968a]' : 'text-rose-600' }}"
                >

                    <i
                        class="ti {{ $incomeChange >= 0 ? 'ti-trending-up' : 'ti-trending-down' }} text-base"
                    ></i>

                    <span>
                        {{ abs(round($incomeChange, 1)) }}% vs bulan lalu
                    </span>

                </div>

            </div>


            <!-- ====================================== -->
            <!-- PENGELUARAN                             -->
            <!-- ====================================== -->
            <div
                class="bg-white rounded-3xl p-7 border border-slate-100 shadow-sm hover:shadow-lg hover:shadow-rose-500/5 transition-all duration-300 flex flex-col justify-between relative overflow-hidden group"
            >

                <!-- Soft Glow -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full blur-3xl -mr-10 -mt-10 transition-transform duration-700 group-hover:scale-150 pointer-events-none"
                ></div>


                <div class="flex justify-between items-start mb-6">

                    <div
                        class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300"
                    >
                        <i class="ti ti-arrow-up-right text-2xl"></i>
                    </div>

                    <span
                        class="bg-slate-50 border border-slate-100 text-slate-500 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wider"
                    >
                        Bulan Ini
                    </span>

                </div>


                <div>

                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-1">
                        Pengeluaran
                    </p>

                    <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                        Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
                    </h3>

                </div>


                <!-- Indikator Trend -->
                <div
                    class="mt-4 flex items-center gap-1.5 text-xs font-bold {{ $expenseChange <= 0 ? 'text-[#00968a]' : 'text-rose-600' }}"
                >

                    <i
                        class="ti {{ $expenseChange <= 0 ? 'ti-trending-down' : 'ti-trending-up' }} text-base"
                    ></i>

                    <span>
                        {{ abs(round($expenseChange, 1)) }}% vs bulan lalu
                    </span>

                </div>

            </div>

        </div>



            {{-- =========================
                CASH FLOW CHART
            ========================== --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- Chart Header --}}
                <div class="px-6 pt-6">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <div class="flex items-center gap-2">

                                <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                                    <i class="ti ti-chart-line text-lg"></i>
                                </div>

                                <h3 class="text-lg font-bold text-slate-800">
                                    Aktivitas Arus Kas
                                </h3>

                            </div>

                            <p class="text-sm text-slate-500 mt-2">
                                Perbandingan pemasukan dan pengeluaran dalam 7 hari terakhir.
                            </p>
                        </div>


                        <div class="flex items-center gap-2 w-fit bg-slate-50 border border-slate-100 px-3 py-2 rounded-lg">

                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>

                            <span class="text-xs font-semibold text-slate-600">
                                7 Hari Terakhir
                            </span>

                        </div>

                    </div>

                </div>


                {{-- Chart --}}
                <div class="w-full px-2 sm:px-4">

                    <div
                        id="cashflowChart"
                        class="w-full"
                        style="width: 100%; min-height: 360px;"
                    ></div>

                </div>

            </div>



            {{-- =========================
                BOTTOM SECTION
            ========================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ==========================================
                    RINGKASAN BULAN INI
                =========================================== --}}
                <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-6">

                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-5">

                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="ti ti-report-money text-xl"></i>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-800">
                                Ringkasan Bulan Ini
                            </h3>

                            <p class="text-xs text-slate-400 mt-0.5">
                                Kondisi arus kas
                            </p>
                        </div>

                    </div>


                    {{-- ======================================
                        PEMASUKAN
                    ======================================= --}}
                    <div class="flex items-center justify-between py-3">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-xl bg-[#e6f7f5] text-[#00968a] flex items-center justify-center">
                                <i class="ti ti-arrow-down-left text-lg"></i>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-slate-700">
                                    Pemasukan
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    Bulan ini
                                </p>
                            </div>

                        </div>

                        <span class="text-sm font-bold text-[#00968a]">
                            Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}
                        </span>

                    </div>


                    {{-- Divider --}}
                    <div class="border-t border-slate-100"></div>


                    {{-- ======================================
                        PENGELUARAN
                    ======================================= --}}
                    <div class="flex items-center justify-between py-3">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                                <i class="ti ti-arrow-up-right text-lg"></i>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-slate-700">
                                    Pengeluaran
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    Bulan ini
                                </p>
                            </div>

                        </div>

                        <span class="text-sm font-bold text-rose-600">
                            Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
                        </span>

                    </div>


                    {{-- ======================================
                        NET CASH FLOW
                    ======================================= --}}
                    <div
                        class="mt-4 rounded-2xl p-4 border
                        {{ $financialStatus === 'surplus'
                            ? 'bg-[#f0fbfa] border-[#d8f3ef]'
                            : ($financialStatus === 'deficit'
                                ? 'bg-rose-50 border-rose-100'
                                : 'bg-slate-50 border-slate-100')
                        }}"
                    >

                        <div class="flex items-center justify-between gap-4">

                            <div>

                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest
                                    {{ $financialStatus === 'surplus'
                                        ? 'text-[#00968a]'
                                        : ($financialStatus === 'deficit'
                                            ? 'text-rose-600'
                                            : 'text-slate-500')
                                    }}"
                                >
                                    Arus Kas Bersih
                                </p>

                                <p class="text-[11px] text-slate-400 mt-1">
                                    Pemasukan - Pengeluaran
                                </p>

                            </div>


                            <p
                                class="text-lg font-extrabold
                                {{ $financialStatus === 'surplus'
                                    ? 'text-[#00968a]'
                                    : ($financialStatus === 'deficit'
                                        ? 'text-rose-600'
                                        : 'text-slate-600')
                                }}"
                            >

                                {{ $netCashFlow >= 0 ? '+' : '-' }}
                                Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}

                            </p>

                        </div>

                    </div>


                    {{-- ======================================
                        PENGGUNAAN PEMASUKAN
                    ======================================= --}}
                    <div class="mt-6">

                        <div class="flex items-end justify-between mb-2">

                            <div>
                                <p class="text-xs font-bold text-slate-700">
                                    Penggunaan Pemasukan
                                </p>

                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Porsi pemasukan yang sudah digunakan
                                </p>
                            </div>

                            <span class="text-sm font-bold text-slate-700">
                                {{ number_format($expenseRatio, 1) }}%
                            </span>

                        </div>


                        {{-- Progress --}}
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">

                            <div
                                class="h-full rounded-full transition-all duration-700
                                {{ $expenseRatio > 90
                                    ? 'bg-rose-500'
                                    : ($expenseRatio > 70
                                        ? 'bg-amber-400'
                                        : 'bg-[#00968a]')
                                }}"
                                style="width: {{ $expenseRatio }}%"
                            ></div>

                        </div>


                        <div class="flex items-center justify-between mt-2">

                            <span class="text-[10px] text-slate-400">
                                Rp {{ number_format($expenseThisMonth, 0, ',', '.') }} digunakan
                            </span>

                            <span class="text-[10px] text-slate-400">
                                Rp {{ number_format($incomeThisMonth, 0, ',', '.') }} pemasukan
                            </span>

                        </div>

                    </div>


                    {{-- ======================================
                        STATUS KEUANGAN
                    ======================================= --}}
                    <div
                        class="mt-5 flex items-center gap-2 px-3.5 py-2.5 rounded-xl
                        {{ $financialStatus === 'surplus'
                            ? 'bg-[#f0fbfa] text-[#00968a]'
                            : ($financialStatus === 'deficit'
                                ? 'bg-rose-50 text-rose-600'
                                : 'bg-slate-50 text-slate-600')
                        }}"
                    >

                        <i
                            class="ti
                            {{ $financialStatus === 'surplus'
                                ? 'ti-circle-check'
                                : ($financialStatus === 'deficit'
                                    ? 'ti-alert-circle'
                                    : 'ti-minus')
                            }} text-base"
                        ></i>

                        <p class="text-xs font-semibold">
                            {{ $financialStatusText }}
                        </p>

                    </div>

                </div>



                {{-- ==========================================
                    AKTIVITAS TERAKHIR
                =========================================== --}}
                <div class="lg:col-span-3 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="p-6">

                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-5">

                            <div>
                                <h3 class="text-lg font-bold text-slate-800">
                                    Aktivitas Terakhir
                                </h3>

                                <p class="text-sm text-slate-400 mt-1">
                                    Transaksi terbaru yang tercatat.
                                </p>
                            </div>

                            <a
                                href="{{ url('/transactions') }}"
                                class="hidden sm:flex items-center gap-1.5 text-sm font-semibold text-[#00968a] hover:text-[#007f75] transition"
                            >
                                Lihat Semua
                                <i class="ti ti-arrow-right text-base"></i>
                            </a>

                        </div>


                        {{-- ======================================
                            TRANSACTION LIST
                        ======================================= --}}
                        <div class="divide-y divide-slate-100">

                            @forelse($recentTransactions as $trx)

                                <div class="group flex items-center justify-between gap-4 py-3.5 first:pt-0 last:pb-0">

                                    {{-- LEFT --}}
                                    <div class="flex items-center gap-3 min-w-0">

                                        {{-- Icon --}}
                                        <div
                                            class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-105
                                            {{ $trx->type == 'income'
                                                ? 'bg-[#e6f7f5] text-[#00968a]'
                                                : 'bg-rose-50 text-rose-600'
                                            }}"
                                        >

                                            <i class="{{ $trx->category->icon ?? 'ti ti-tag' }} text-xl"></i>

                                        </div>


                                        {{-- Transaction info --}}
                                        <div class="min-w-0">

                                            <p class="text-sm font-bold text-slate-800 truncate">
                                                {{ $trx->category->name ?? 'Transfer' }}
                                            </p>

                                            <div class="flex items-center gap-1.5 mt-1">

                                                <span class="text-xs text-slate-400">
                                                    {{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                                                </span>

                                                @if($trx->wallet)

                                                    <span class="text-slate-300 text-xs">
                                                        •
                                                    </span>

                                                    <span class="text-xs text-slate-400 truncate">
                                                        {{ $trx->wallet->name }}
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>


                                    {{-- RIGHT --}}
                                    <div class="text-right shrink-0">

                                        <p
                                            class="text-sm font-bold
                                            {{ $trx->type == 'income'
                                                ? 'text-[#00968a]'
                                                : 'text-rose-600'
                                            }}"
                                        >

                                            {{ $trx->type == 'income' ? '+' : '-' }}
                                            Rp {{ number_format($trx->amount, 0, ',', '.') }}

                                        </p>

                                        <p class="text-[10px] font-medium text-slate-400 mt-1">
                                            {{ $trx->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </p>

                                    </div>

                                </div>

                            @empty

                                <div class="text-center py-10">

                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                        <i class="ti ti-receipt-off text-2xl"></i>
                                    </div>

                                    <p class="text-sm font-semibold text-slate-600">
                                        Belum ada transaksi
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Transaksi yang kamu buat akan muncul di sini.
                                    </p>

                                </div>

                            @endforelse

                        </div>


                        {{-- Mobile --}}
                        <a
                            href="{{ url('/transactions') }}"
                            class="sm:hidden mt-5 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-[#f0fbfa] text-sm font-semibold text-[#00968a] hover:bg-[#e6f7f5] transition"
                        >
                            Lihat Semua Transaksi
                            <i class="ti ti-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- =========================
        APEX CHART
    ========================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const chartElement = document.querySelector("#cashflowChart");

            if (!chartElement) return;

            const options = {

                chart: {
                    type: 'line',
                    height: 360,
                    width: '100%',
                    fontFamily: "'Plus Jakarta Sans', sans-serif",
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    redrawOnParentResize: true,
                    redrawOnWindowResize: true
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

                colors: ['#0d9488', '#e11d48'],

                stroke: {
                    curve: 'smooth',
                    width: 3
                },

                markers: {
                    size: 5,
                    strokeWidth: 3,
                    strokeColors: '#ffffff',
                    hover: {
                        size: 7
                    }
                },

                xaxis: {
                    categories: {!! json_encode($chartDates) !!},

                    tooltip: {
                        enabled: false
                    },

                    axisBorder: {
                        show: false
                    },

                    axisTicks: {
                        show: false
                    },

                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px',
                            fontWeight: 600
                        },
                        offsetY: 4
                    }
                },

                yaxis: {

                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px',
                            fontWeight: 600
                        },

                        formatter: function (value) {

                            if (value >= 1000000) {
                                return "Rp " + (value / 1000000).toFixed(1) + "M";
                            }

                            if (value >= 1000) {
                                return "Rp " + (value / 1000).toFixed(0) + "K";
                            }

                            return "Rp " + value;
                        }
                    }

                },

                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,

                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 0
                    },

                    xaxis: {
                        lines: {
                            show: false
                        }
                    },

                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },

                legend: {
                    position: 'top',
                    horizontalAlign: 'right',

                    markers: {
                        radius: 12
                    },

                    itemMargin: {
                        horizontal: 12
                    },

                    fontWeight: 600,

                    labels: {
                        colors: '#475569'
                    }
                },

                tooltip: {

                    shared: true,
                    intersect: false,
                    theme: 'light',

                    style: {
                        fontSize: '13px',
                        fontFamily: "'Plus Jakarta Sans', sans-serif"
                    },

                    y: {
                        formatter: function (val) {

                            return "Rp " + Number(val).toLocaleString("id-ID");

                        }
                    }

                },

                responsive: [
                    {
                        breakpoint: 640,

                        options: {

                            chart: {
                                height: 300
                            },

                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'center'
                            },

                            yaxis: {
                                labels: {
                                    show: false
                                }
                            }

                        }
                    }
                ]

            };


            const chart = new ApexCharts(
                chartElement,
                options
            );

            chart.render();

        });
    </script>

</x-app-layout>
