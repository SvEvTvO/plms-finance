<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PLMS Finance') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-700 selection:bg-teal-500 selection:text-white">

        <!-- WRAPPER UTAMA (Terkunci seukuran layar) -->
        <div class="flex h-screen overflow-hidden bg-slate-50">

            <!-- Overlay Mobile -->
            <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

            <!-- SIDEBAR -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col transform -translate-x-full transition-transform duration-300 lg:relative lg:translate-x-0 shrink-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:16px_16px] bg-[position:0_0,8px_8px]">

                <div class="absolute inset-0 bg-white/95 backdrop-blur-[1px] pointer-events-none"></div>

                <div class="h-16 flex items-center px-6 border-b border-slate-200/50 shrink-0 z-10 relative">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white font-bold shadow-md shadow-teal-600/20 group-hover:scale-105 transition-transform">
                            <i class="ti ti-wallet text-xl"></i>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-slate-900">PLMS<span class="text-teal-600">Finance</span></span>
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto p-4 space-y-4 z-10 relative">
                    <!-- MENU UTAMA -->
                    <div>
                        <h4 class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Menu Utama</h4>
                        <ul class="space-y-0.5">
                            <li>
                                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('dashboard') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-600 transition-colors' }}">
                                    <i class="ti ti-layout-dashboard text-lg mr-3"></i> Dashboard
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- KEUANGAN -->
                    <div>
                        <h4 class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Keuangan</h4>
                        <ul class="space-y-0.5">
                            <li>
                                <a href="{{ route('wallets.index') }}" class="flex items-center px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('wallets.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-600 transition-colors' }}">
                                    <i class="ti ti-wallet text-lg mr-3"></i> Dompet & Rekening
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categories.index') }}" class="flex items-center px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('categories.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-600 transition-colors' }}">
                                    <i class="ti ti-category text-lg mr-3"></i> Kategori Transaksi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('transactions.index') }}" class="flex items-center px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('transactions.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-600 transition-colors' }}">
                                    <i class="ti ti-arrows-exchange text-lg mr-3"></i> Transaksi
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- PERENCANAAN -->
                    <div>
                        <h4 class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Perencanaan</h4>
                        <ul class="space-y-0.5">
                            <li>
                                <a href="{{ route('goals.index') }}" class="flex items-center px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('goals.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-600 transition-colors' }}">
                                    <i class="ti ti-target text-lg mr-3"></i> Target Keuangan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('reports.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-slate-600 hover:bg-teal-50 hover:text-teal-600 transition-colors' }}">
                                    <i class="ti ti-chart-pie text-lg mr-3"></i> Laporan & Ekspor
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="p-4 border-t border-slate-200/50 shrink-0 z-10 relative bg-white">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                            <i class="ti ti-logout text-lg mr-3"></i> Keluar Akun
                        </button>
                    </form>
                </div>
            </aside>

            <!-- KANAN: AREA KONTEN UTAMA -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50/50">

                <!-- TOP NAVBAR (BACKGROUND HIJAU/TEAL) -->
                <header class="h-16 shrink-0 bg-teal-600 flex items-center justify-between px-4 sm:px-6 z-20 shadow-md">
                    <div class="flex items-center gap-4">
                        <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-teal-100 hover:text-white rounded-lg hover:bg-teal-700 transition-colors">
                            <i class="ti ti-menu-2 text-2xl"></i>
                        </button>
                        <!-- Sapaan ringan di Desktop agar kiri tidak kosong -->
                        <span class="text-teal-100 font-medium text-sm hidden lg:block">👋 Selamat datang kembali, semangat kelola keuangan!</span>
                    </div>

                    <div class="flex items-center shrink-0">
                        <div class="flex items-center gap-3 border-l border-teal-500/50 pl-4 ml-2">
                            <span class="text-sm font-bold text-white hidden sm:block">{{ Auth::user()->name }}</span>
                            <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-teal-700 font-bold shrink-0 shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </header>

                <!-- MAIN CONTENT (AREA HEADER DIKEMBALIKAN KE SINI) -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

                    @if (isset($header))
                        <!-- Header kini bebas merentang tanpa tercekik Navbar! -->
                        <div class="mb-6 lg:mb-8">
                            {{ $header }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>

            </div>
        </div>

        <!-- Script Mobile Sidebar -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const toggleBtn = document.getElementById('sidebarToggle');

                function toggleSidebar() {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                }

                if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
                if (overlay) overlay.addEventListener('click', toggleSidebar);
            });
        </script>

        <!-- Script Anti Double-Submit -->
        <script>
            document.addEventListener('submit', function (e) {
                const form = e.target;

                // Jika form sudah disubmit sebelumnya, hentikan aksi
                if (form.getAttribute('data-submitting') === 'true') {
                    e.preventDefault();
                    return;
                }

                // Tandai form sedang diproses
                form.setAttribute('data-submitting', 'true');

                // Cari tombol submit di dalam form tersebut
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // Nonaktifkan tombol agar tidak bisa diklik lagi
                    submitBtn.disabled = true;
                    // Tambahkan efek visual (agak pudar dan kursor dilarang)
                    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');

                    // Opsional: Ubah teks tombol untuk memberi indikasi visual
                    const originalContent = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="ti ti-loader animate-spin text-lg mr-2"></i> Memproses...';
                }
            });
        </script>
    </body>
</html>
