<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PLMS Finance') }} - Kelola Keuangan Semudah Chat</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-grid-pattern {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800 selection:bg-teal-500 selection:text-white flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
                    <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-teal-600/20 text-white">
                        <i class="ti ti-wallet text-2xl"></i>
                    </div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-800">PLMS<span class="text-teal-600">Finance</span></span>
                </div>

                <!-- Right Menu -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition hidden sm:block">Ke Dashboard</a>
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition">
                                Dashboard Saya <i class="ti ti-arrow-right ml-1"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition">
                                    Daftar Gratis
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden flex-grow">
        <div class="absolute inset-0 bg-grid-pattern opacity-40"></div>

        <!-- Gradient Blobs -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-teal-400/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-emerald-400/20 blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-50 border border-teal-100 text-teal-700 text-xs font-semibold mb-6">
                <i class="ti ti-sparkles text-sm"></i> Cara Baru Kelola Keuangan
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-slate-900 tracking-tight mb-8 leading-tight">
                Catat Pengeluaran,<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-500">
                    Semudah Membalas Chat.
                </span>
            </h1>
            
            <p class="mt-4 max-w-2xl mx-auto text-lg md:text-xl text-slate-500 font-medium mb-10">
                Tinggalkan aplikasi rumit. PLMS Finance mengintegrasikan kecerdasan WhatsApp Bot dengan Dashboard Analitik modern untuk mencatat, melacak, dan merencanakan keuangan Anda dalam hitungan detik.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white text-base font-bold rounded-2xl shadow-lg shadow-teal-600/30 hover:shadow-xl transition-all flex items-center justify-center gap-2 transform hover:-translate-y-1">
                    <i class="ti ti-rocket text-xl"></i> Mulai Sekarang, Gratis!
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-50 text-slate-700 text-base font-bold rounded-2xl shadow-sm border border-slate-200 transition-all flex items-center justify-center gap-2">
                    <i class="ti ti-info-circle text-xl"></i> Pelajari Cara Kerja
                </a>
            </div>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section class="py-20 bg-white border-y border-slate-100 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4">Kenapa Memilih PLMS Finance?</h2>
                <p class="text-slate-500">Dibangun untuk kecepatan, kemudahan, dan visibilitas total atas aset keuangan Anda tanpa membuang waktu berharga.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white transition-all">
                        <i class="ti ti-brand-whatsapp text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">WhatsApp Bot Pintar</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Tidak perlu buka aplikasi. Cukup kirim pesan dengan format sederhana ke nomor bot kami, dan transaksi otomatis tercatat di server.</p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="ti ti-chart-pie text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Dashboard Analitik</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Visualisasikan arus kas Anda dengan grafik interaktif, laporan bulanan, dan perbandingan pemasukan vs pengeluaran yang memanjakan mata.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all">
                        <i class="ti ti-wallet text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Multi-Dompet</h3>
                    <p class="text-slate-500 leading-relaxed text-sm">Pisahkan uang Anda. Kelola dompet tunai, rekening bank, hingga e-Wallet dalam satu pintu dan catat transfer antar dompet dengan mudah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="py-24 relative bg-slate-900 text-white">
        <!-- Decor -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-teal-600/20 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Mulai Dalam 3 Langkah</h2>
                <p class="text-slate-400">Proses pengaturan yang tidak sampai 2 menit.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <!-- Garis Penghubung (Desktop) -->
                <div class="hidden md:block absolute top-8 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-teal-500/0 via-teal-500/50 to-teal-500/0"></div>

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-2xl font-bold text-teal-400 mb-6 shadow-xl">
                        1
                    </div>
                    <h3 class="text-lg font-bold mb-2">Buat Akun</h3>
                    <p class="text-slate-400 text-sm">Daftarkan diri Anda dan masukkan nomor WhatsApp aktif yang akan digunakan untuk mencatat keuangan.</p>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-2xl font-bold text-teal-400 mb-6 shadow-xl">
                        2
                    </div>
                    <h3 class="text-lg font-bold mb-2">Terima Pesan Bot</h3>
                    <p class="text-slate-400 text-sm">Sistem kami akan otomatis mengirimkan panduan langsung ke WhatsApp Anda detik itu juga.</p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-2xl font-bold text-teal-400 mb-6 shadow-xl">
                        3
                    </div>
                    <h3 class="text-lg font-bold mb-2">Mulai Chat!</h3>
                    <p class="text-slate-400 text-sm">Ketik "BANTUAN" di WhatsApp untuk melihat menu, atau langsung catat transaksi pertama Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 lg:gap-16">
                <!-- Brand Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-teal-600 rounded-lg flex items-center justify-center text-white">
                            <i class="ti ti-wallet text-xl"></i>
                        </div>
                        <span class="font-extrabold text-xl tracking-tight text-slate-800">PLMS<span class="text-teal-600">Finance</span></span>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-sm mb-6">
                        Solusi modern manajemen finansial pribadi yang menghubungkan kenyamanan WhatsApp dengan kekuatan visualisasi data. Kendalikan masa depan keuangan Anda hari ini.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-teal-50 hover:text-teal-600 transition">
                            <i class="ti ti-brand-github text-xl"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-teal-50 hover:text-teal-600 transition">
                            <i class="ti ti-brand-instagram text-xl"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-teal-50 hover:text-teal-600 transition">
                            <i class="ti ti-brand-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold text-slate-800 mb-6">Tautan Cepat</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('login') }}" class="text-slate-500 hover:text-teal-600 transition">Masuk Akun</a></li>
                        <li><a href="{{ route('register') }}" class="text-slate-500 hover:text-teal-600 transition">Daftar Gratis</a></li>
                        <li><a href="#how-it-works" class="text-slate-500 hover:text-teal-600 transition">Cara Kerja Bot</a></li>
                    </ul>
                </div>

                <!-- Legal & Contact -->
                <div>
                    <h4 class="font-bold text-slate-800 mb-6">Legal & Bantuan</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="text-slate-500 hover:text-teal-600 transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-slate-500 hover:text-teal-600 transition">Syarat & Ketentuan</a></li>
                        <li>
                            <a href="mailto:support@plms-finance.app" class="inline-flex items-center gap-2 text-slate-500 hover:text-teal-600 transition mt-2">
                                <i class="ti ti-mail"></i> support@plms-finance.app
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-100 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-400 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} PLMS Finance Management. Dirancang untuk pencatatan yang lebih baik.
                </p>
                <div class="flex items-center gap-2 text-slate-400 text-sm">
                    Dibuat dengan <i class="ti ti-heart-filled text-rose-500"></i> di Indonesia
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
