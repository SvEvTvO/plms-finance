<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PLMS Finance - Kendalikan Keuangan Anda</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Pola background dots halus */
        .bg-dots {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900 selection:bg-teal-500 selection:text-white flex flex-col min-h-screen">

    <!-- NAVBAR -->
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200 fixed top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white font-bold">
                        <i class="ti ti-wallet text-xl"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-900">PLMS<span class="text-teal-600">Finance</span></span>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-700 hover:text-teal-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-teal-600 transition">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-semibold bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition shadow-sm shadow-teal-600/30">Daftar Gratis</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <main class="flex-grow pt-24 pb-16 flex items-center bg-dots">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100 text-teal-700 text-xs font-bold uppercase tracking-wider mb-6 border border-teal-200">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                    </span>
                    Web App + WhatsApp Bot
                </div>
                
                <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                    Kendalikan Keuangan Anda, <br class="hidden md:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-emerald-600">Semudah Kirim Pesan.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Catat pengeluaran instan via WhatsApp Bot, pantau target tabungan impian, dan analisis laporan keuangan Anda secara mendalam melalui Dasbor Web interaktif.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 text-base font-bold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition shadow-lg shadow-teal-600/30 flex items-center justify-center">
                            Buka Dasbor Saya <i class="ti ti-arrow-right ml-2 text-xl"></i>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 text-base font-bold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition shadow-lg shadow-teal-600/30 flex items-center justify-center">
                            Mulai Sekarang - Gratis
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 text-base font-bold text-slate-700 bg-white border-2 border-slate-200 rounded-xl hover:border-teal-500 hover:text-teal-600 transition flex items-center justify-center">
                            Masuk ke Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </main>

    <!-- FEATURES SECTION -->
    <section class="py-16 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900">Arsitektur Finansial Lengkap</h2>
                <p class="text-slate-500 mt-3">Satu aplikasi untuk seluruh kebutuhan pencatatan gaya hidup Anda.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                        <i class="ti ti-wallet text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Multi-Dompet</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Pisahkan pencatatan antara Rekening Bank, e-Wallet, dan Uang Tunai Anda dengan fitur transfer antar dompet.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center mb-4">
                        <i class="ti ti-target text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Target & Tabungan</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Buat target pembelian barang impian dan sisihkan tabungan secara rutin hingga mencapai 100%.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-4">
                        <i class="ti ti-brand-whatsapp text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Integrasi Bot WA</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Jajan di jalan? Cukup ketik pesan singkat ke Bot WhatsApp untuk mencatat pengeluaran tanpa buka web.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mb-4">
                        <i class="ti ti-file-spreadsheet text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Ekspor ke Excel</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Unduh data CSV mentah yang kompatibel dengan Microsoft Excel untuk membuat analitik dan grafik Scatter Plot.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 py-8 border-t border-slate-800 text-center">
        <p class="text-sm text-slate-400">
            &copy; {{ date('Y') }} PLMS Finance Tracker. Dibuat dengan presisi menggunakan Laravel.
        </p>
    </footer>

</body>
</html>
