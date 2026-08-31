<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'PLMS Finance') }} — Kelola Keuangan Semudah Chat</title>
        <meta name="description" content="PLMS Finance mencatat pemasukan & pengeluaran lewat WhatsApp Bot dan menampilkannya dalam dashboard analitik modern. Gratis, cepat, tanpa ribet.">

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }

            .bg-grid-pattern {
                background-image: radial-gradient(circle at 1px 1px, rgb(148 163 184 / 0.35) 1px, transparent 0);
                background-size: 28px 28px;
                mask-image: radial-gradient(ellipse 85% 65% at 50% 0%, black 40%, transparent 100%);
                -webkit-mask-image: radial-gradient(ellipse 85% 65% at 50% 0%, black 40%, transparent 100%);
            }

            /* ===== Hover Animations ===== */
            @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }
            @keyframes float-delayed { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(12px); } }
            @keyframes blob {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(30px, -40px) scale(1.08); }
                66% { transform: translate(-25px, 25px) scale(0.94); }
            }
            .animate-float { animation: float 6s ease-in-out infinite; }
            .animate-float-delayed { animation: float-delayed 7s ease-in-out infinite; }
            .animate-blob { animation: blob 14s ease-in-out infinite; }

            /* ===== Navbar state ===== */
            .nav-scrolled .glass {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(12px);
                border-color: rgba(226, 232, 240, 0.8);
                box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            }

            /* ===== FAQ ===== */
            details summary { list-style: none; cursor: pointer; }
            details summary::-webkit-details-marker { display: none; }
            details .faq-icon { transition: transform .3s ease; }
            details[open] .faq-icon { transform: rotate(45deg); }

            /* ===== Accordion Expand/Contract (Demo & FAQ) ===== */
            .accordion-wrapper {
                display: grid;
                grid-template-rows: 0fr;
                transition: grid-template-rows 0.3s ease-in-out;
            }
            .accordion-wrapper.is-open {
                grid-template-rows: 1fr;
            }
            .accordion-inner {
                overflow: hidden;
            }

            /* Chat fade-in */
            .fade-in { animation: fadeIn 0.4s ease-out both; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    </head>
    <body class="antialiased bg-white text-slate-800 selection:bg-teal-500 selection:text-white flex flex-col min-h-screen">

        <!-- ================================================================ -->
        <!-- NAVBAR                                                           -->
        <!-- ================================================================ -->
        <header class="fixed inset-x-0 top-0 z-50 transition-all duration-300" id="navbar">
            <nav class="mx-auto mt-3 max-w-7xl px-4 sm:px-6 lg:px-8 relative">
                <div class="glass flex h-16 items-center justify-between rounded-2xl border border-transparent bg-white/70 backdrop-blur-md px-4 shadow-sm sm:px-6 transition-all duration-300">
                    <a href="#" class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-700 text-white shadow-lg shadow-teal-700/20">
                            <i class="ti ti-wallet text-xl"></i>
                        </div>
                        <div>
                            <div class="text-[15px] font-extrabold leading-none tracking-tight text-slate-900">
                                PLMS<span class="text-teal-600">Finance</span>
                            </div>
                            <div class="mt-1 hidden text-[9px] font-bold uppercase tracking-[.18em] text-slate-400 sm:block">
                                Personal Finance
                            </div>
                        </div>
                    </a>

                    <div class="hidden items-center gap-7 md:flex">
                        <a href="#keunggulan" class="text-sm font-semibold text-slate-500 transition hover:text-teal-700">Keunggulan</a>
                        <a href="#cara-kerja" class="text-sm font-semibold text-slate-500 transition hover:text-teal-700">Cara Kerja</a>
                        <a href="#demo" class="text-sm font-semibold text-slate-500 transition hover:text-teal-700">Demo Format</a>
                        <a href="#faq" class="text-sm font-semibold text-slate-500 transition hover:text-teal-700">FAQ</a>
                    </div>

                    <div class="flex items-center gap-2.5">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="hidden px-3 py-2 text-sm font-bold text-slate-600 transition hover:text-teal-700 sm:block">
                                    Dashboard
                                </a>
                                <a href="{{ url('/dashboard') }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 hidden sm:block">
                                    Buka Aplikasi
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="hidden sm:block px-3 py-2 text-sm font-bold text-slate-600 transition hover:text-teal-700">
                                    Masuk
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-teal-700/20 transition hover:-translate-y-0.5 hover:bg-teal-800 hidden sm:block">
                                        Mulai Gratis
                                    </a>
                                @endif
                            @endauth
                        @endif

                        <button id="menu-btn" class="md:hidden flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 transition">
                            <i id="menu-icon" class="ti ti-menu-2 text-xl"></i>
                        </button>
                    </div>
                </div>

                <div id="mobile-menu" class="hidden absolute top-20 left-4 right-4 rounded-2xl bg-white border border-slate-100 p-4 shadow-xl shadow-slate-900/5 md:hidden">
                    <div class="flex flex-col space-y-1 pb-4 border-b border-slate-100">
                        <a href="#keunggulan" class="mobile-link rounded-xl px-4 py-3 text-sm font-bold text-slate-600 hover:bg-teal-50 hover:text-teal-700">Keunggulan</a>
                        <a href="#cara-kerja" class="mobile-link rounded-xl px-4 py-3 text-sm font-bold text-slate-600 hover:bg-teal-50 hover:text-teal-700">Cara Kerja</a>
                        <a href="#demo" class="mobile-link rounded-xl px-4 py-3 text-sm font-bold text-slate-600 hover:bg-teal-50 hover:text-teal-700">Demo Format</a>
                        <a href="#faq" class="mobile-link rounded-xl px-4 py-3 text-sm font-bold text-slate-600 hover:bg-teal-50 hover:text-teal-700">FAQ</a>
                    </div>
                    <div class="pt-4 flex flex-col gap-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-center text-sm font-bold text-white">Buka Aplikasi</a>
                        @else
                            <a href="{{ route('login') }}" class="w-full rounded-xl bg-slate-50 px-4 py-3 text-center text-sm font-bold text-slate-700">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="w-full rounded-xl bg-teal-700 px-4 py-3 text-center text-sm font-bold text-white">Mulai Gratis</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </nav>
        </header>

        <!-- ================================================================ -->
        <!-- HERO SECTION                                                     -->
        <!-- ================================================================ -->
        <section class="relative pt-36 pb-20 lg:pt-44 lg:pb-28 overflow-hidden bg-slate-50">
            <div class="absolute inset-0 bg-grid-pattern"></div>

            <div class="absolute top-0 right-0 -mr-24 -mt-24 w-[28rem] h-[28rem] rounded-full bg-teal-400/25 blur-3xl animate-blob pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 rounded-full bg-emerald-400/20 blur-3xl animate-blob pointer-events-none" style="animation-delay: -6s;"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-14 lg:gap-12 items-center">

                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-teal-50 border border-teal-100 text-teal-700 text-xs font-bold mb-7">
                            <i class="ti ti-sparkles text-sm"></i> Cara Baru Kelola Keuangan Pribadi
                        </div>

                        <h1 class="text-4xl md:text-5xl lg:text-[3.4rem] font-extrabold text-slate-900 tracking-tight mb-6 leading-[1.12]">
                            Catat Pengeluaran,<br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 via-emerald-500 to-teal-600">Semudah Membalas Chat.</span>
                        </h1>

                        <p class="mt-2 max-w-xl mx-auto lg:mx-0 text-lg text-slate-500 font-medium leading-relaxed mb-9">
                            Kirim satu pesan WhatsApp, dan PLMS Finance otomatis mencatat, mengkategorikan, serta memvisualisasikan keuangan Anda di dashboard yang elegan — semuanya dalam hitungan detik.
                        </p>

                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white text-base font-bold rounded-2xl shadow-xl shadow-teal-600/30 hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-2 hover:-translate-y-0.5">
                                    <i class="ti ti-rocket text-xl"></i> Mulai Gratis Sekarang
                                </a>
                            @endif
                            <a href="#demo" class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-50 text-slate-700 text-base font-bold rounded-2xl shadow-sm border border-slate-200 hover:border-teal-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="ti ti-message-chatbot text-xl text-teal-600"></i> Lihat Demo Bot
                            </a>
                        </div>

                        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            <div class="flex -space-x-2.5">
                                <span class="w-9 h-9 rounded-full bg-teal-600 text-white text-[11px] font-extrabold flex items-center justify-center ring-2 ring-white">RA</span>
                                <span class="w-9 h-9 rounded-full bg-indigo-500 text-white text-[11px] font-extrabold flex items-center justify-center ring-2 ring-white">DN</span>
                                <span class="w-9 h-9 rounded-full bg-amber-500 text-white text-[11px] font-extrabold flex items-center justify-center ring-2 ring-white">SP</span>
                                <span class="w-9 h-9 rounded-full bg-slate-700 text-white text-[10px] font-extrabold flex items-center justify-center ring-2 ring-white">1rb+</span>
                            </div>
                            <div class="text-sm text-left">
                                <div class="flex text-amber-400 gap-0.5 mb-0.5">
                                    <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                                </div>
                                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-xs font-semibold text-slate-400 lg:justify-start">
                                    <p class="text-slate-500 font-medium"><span class="font-bold text-slate-700">4,9/5</span> dari 1.200+ pengguna aktif</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-xs font-semibold text-slate-400 lg:justify-start">
                            <span class="flex items-center gap-1.5"><i class="ti ti-check text-teal-600"></i> Mudah digunakan</span>
                            <span class="flex items-center gap-1.5"><i class="ti ti-check text-teal-600"></i> Terpusat</span>
                            <span class="flex items-center gap-1.5"><i class="ti ti-check text-teal-600"></i> Lebih terarah</span>
                        </div>
                    </div>

                    <!-- ===== Kanan: Mockup Dashboard ===== -->
                    <div class="relative flex justify-center lg:justify-end">
                        <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-teal-400/25 to-emerald-400/25 blur-3xl rounded-full scale-90"></div>

                        <div class="relative w-full max-w-md">
                            <div class="relative bg-white rounded-3xl shadow-2xl shadow-slate-900/10 border border-slate-100 overflow-hidden animate-float">
                                <div class="px-6 pt-6 pb-5 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-teal-600 flex items-center justify-center text-white shadow-lg shadow-teal-600/30">
                                            <i class="ti ti-wallet text-lg"></i>
                                        </div>
                                        <p class="font-extrabold text-slate-900">Keuangan Saya</p>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-400 bg-slate-50 border border-slate-100 px-2.5 py-1 rounded-lg">Aug 2026</span>
                                </div>

                                <div class="px-6 pb-6">
                                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total Saldo</p>
                                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight mb-5">Rp 8.450.000</p>

                                    <div class="grid grid-cols-2 gap-3 mb-6">
                                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3.5">
                                            <div class="flex items-center gap-1.5 text-emerald-600 mb-1">
                                                <i class="ti ti-arrow-narrow-up-right text-sm font-bold"></i>
                                                <span class="text-[11px] font-bold uppercase tracking-wide">Pemasukan</span>
                                            </div>
                                            <p class="text-lg font-extrabold text-emerald-700">Rp 6,5jt</p>
                                        </div>
                                        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-3.5">
                                            <div class="flex items-center gap-1.5 text-rose-600 mb-1">
                                                <i class="ti ti-arrow-narrow-down-right text-sm font-bold"></i>
                                                <span class="text-[11px] font-bold uppercase tracking-wide">Pengeluaran</span>
                                            </div>
                                            <p class="text-lg font-extrabold text-rose-600">Rp 3,2jt</p>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-xs font-bold text-slate-700">Pengeluaran Mingguan</p>
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">-12% vs lalu</span>
                                        </div>
                                        <div class="flex items-end justify-between gap-2 h-24">
                                            <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                                                <div class="w-full rounded-t-lg bg-slate-200" style="height: 40%"></div>
                                                <span class="text-[9px] text-slate-400 font-bold">S</span>
                                            </div>
                                            <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                                                <div class="w-full rounded-t-lg bg-slate-300" style="height: 65%"></div>
                                                <span class="text-[9px] text-slate-400 font-bold">S</span>
                                            </div>
                                            <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                                                <div class="w-full rounded-t-lg bg-slate-200" style="height: 50%"></div>
                                                <span class="text-[9px] text-slate-400 font-bold">R</span>
                                            </div>
                                            <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                                                <div class="w-full rounded-t-lg bg-slate-300" style="height: 78%"></div>
                                                <span class="text-[9px] text-slate-400 font-bold">K</span>
                                            </div>
                                            <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                                                <div class="w-full rounded-t-lg bg-slate-200" style="height: 45%"></div>
                                                <span class="text-[9px] text-slate-400 font-bold">J</span>
                                            </div>
                                            <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                                                <div class="w-full rounded-t-lg bg-slate-300" style="height: 60%"></div>
                                                <span class="text-[9px] text-slate-400 font-bold">S</span>
                                            </div>
                                            <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                                                <div class="w-full rounded-t-lg bg-gradient-to-t from-teal-600 to-emerald-500 shadow-sm" style="height: 92%"></div>
                                                <span class="text-[9px] text-teal-600 font-bold">M</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <i class="ti ti-target-arrow text-teal-600"></i>
                                                <p class="text-xs font-bold text-slate-700">Dana Darurat</p>
                                            </div>
                                            <p class="text-xs font-extrabold text-teal-600">68%</p>
                                        </div>
                                        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full w-[68%] bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full"></div>
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-medium mt-2">Rp 6.800.000 dari Rp 10.000.000</p>
                                    </div>
                                </div>
                            </div>

                            <div class="absolute -left-4 sm:-left-10 -top-6 bg-white rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-100 p-3.5 animate-float-delayed">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#25D366]/10 text-[#1da851] flex items-center justify-center"><i class="ti ti-brand-whatsapp text-lg"></i></div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Baru saja tercatat</p>
                                        <p class="text-xs font-extrabold text-slate-800">Kopi 22rb ✓</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3 mt-5 px-1">
                                <div class="bg-white/90 backdrop-blur border border-slate-100 rounded-2xl p-3.5 text-center shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                                    <i class="ti ti-wallet text-teal-600 text-xl"></i>
                                    <p class="text-[11px] font-bold text-slate-600 mt-1.5">Multi-Dompet</p>
                                </div>
                                <div class="bg-white/90 backdrop-blur border border-slate-100 rounded-2xl p-3.5 text-center shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                                    <i class="ti ti-chart-arrows text-teal-600 text-xl"></i>
                                    <p class="text-[11px] font-bold text-slate-600 mt-1.5">Budgeting</p>
                                </div>
                                <div class="bg-white/90 backdrop-blur border border-slate-100 rounded-2xl p-3.5 text-center shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                                    <i class="ti ti-target-arrow text-teal-600 text-xl"></i>
                                    <p class="text-[11px] font-bold text-slate-600 mt-1.5">Target</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================ -->
        <!-- TRUST / VALUE STRIP                                              -->
        <!-- ================================================================ -->
        <section class="border-y border-slate-200 bg-white">
            <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-slate-200 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
                <div class="group px-4 py-7 text-center lg:py-8 hover:bg-teal-600 transition-colors duration-300 cursor-default">
                    <i class="ti ti-wallet text-2xl text-teal-600 group-hover:text-white transition-colors duration-300"></i>
                    <p class="mt-2 text-sm font-bold text-slate-800 group-hover:text-white transition-colors duration-300">Multi-Dompet</p>
                    <p class="mt-1 text-xs text-slate-400 group-hover:text-teal-100 transition-colors duration-300">Semua saldo terpusat</p>
                </div>
                <div class="group px-4 py-7 text-center lg:py-8 hover:bg-teal-600 transition-colors duration-300 cursor-default">
                    <i class="ti ti-chart-bar text-2xl text-teal-600 group-hover:text-white transition-colors duration-300"></i>
                    <p class="mt-2 text-sm font-bold text-slate-800 group-hover:text-white transition-colors duration-300">Analitik</p>
                    <p class="mt-1 text-xs text-slate-400 group-hover:text-teal-100 transition-colors duration-300">Pahami pola pengeluaran</p>
                </div>
                <div class="group border-t px-4 py-7 text-center sm:border-t-0 lg:py-8 hover:bg-teal-600 transition-colors duration-300 cursor-default">
                    <i class="ti ti-target text-2xl text-teal-600 group-hover:text-white transition-colors duration-300"></i>
                    <p class="mt-2 text-sm font-bold text-slate-800 group-hover:text-white transition-colors duration-300">Financial Goals</p>
                    <p class="mt-1 text-xs text-slate-400 group-hover:text-teal-100 transition-colors duration-300">Kejar target lebih terarah</p>
                </div>
                <div class="group border-t px-4 py-7 text-center sm:border-t-0 lg:py-8 hover:bg-teal-600 transition-colors duration-300 cursor-default">
                    <i class="ti ti-brand-whatsapp text-2xl text-teal-600 group-hover:text-white transition-colors duration-300"></i>
                    <p class="mt-2 text-sm font-bold text-slate-800 group-hover:text-white transition-colors duration-300">WhatsApp Bot</p>
                    <p class="mt-1 text-xs text-slate-400 group-hover:text-teal-100 transition-colors duration-300">Catat tanpa ribet</p>
                </div>
            </div>
        </section>

        <!-- ================================================================ -->
        <!-- KEUNGGULAN                                                       -->
        <!-- ================================================================ -->
        <section id="keunggulan" class="py-20 lg:py-28 bg-white relative overflow-hidden">
            <div class="absolute top-1/3 -right-32 w-96 h-96 bg-teal-100/40 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-14 lg:mb-20">
                    <span class="inline-block px-3.5 py-1.5 rounded-full bg-teal-50 border border-teal-100 text-teal-700 text-xs font-bold tracking-widest uppercase mb-5">Satu Tempat Untuk Semuanya</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4 tracking-tight">Bukan Sekadar Pencatat Transaksi.</h2>
                    <p class="text-slate-500 text-lg">Lebih dari sekadar mencatat — PLMS Finance memberi Anda perangkat lengkap untuk memahami, mengendalikan, dan mengoptimalkan keuangan pribadi Anda.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-teal-200 hover:shadow-2xl hover:shadow-teal-600/10 transition-all duration-300 hover:-translate-y-1.5">
                        <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
                            <i class="ti ti-list-check text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Transaksi Terorganisir</h3>
                        <p class="text-slate-500 leading-relaxed text-sm">Setiap pemasukan dan pengeluaran tercatat rapi dengan kategori otomatis — lihat ke mana uang Anda pergi tanpa perlu telusur manual.</p>
                    </div>
                    <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-indigo-200 hover:shadow-2xl hover:shadow-indigo-600/10 transition-all duration-300 hover:-translate-y-1.5">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                            <i class="ti ti-chart-pie text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Dashboard Bermakna</h3>
                        <p class="text-slate-500 leading-relaxed text-sm">Visualisasi data yang dirancang untuk pengambilan keputusan — bukan sekadar grafik, tapi wawasan yang bisa langsung Anda tindaklanjuti.</p>
                    </div>
                    <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-amber-200 hover:shadow-2xl hover:shadow-amber-600/10 transition-all duration-300 hover:-translate-y-1.5">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                            <i class="ti ti-wallet text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Multi-Dompet</h3>
                        <p class="text-slate-500 leading-relaxed text-sm">Kelola uang tunai, rekening bank, hingga e-Wallet dalam satu tempat — lengkap dengan pencatatan transfer antar dompet.</p>
                    </div>
                    <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-rose-200 hover:shadow-2xl hover:shadow-rose-600/10 transition-all duration-300 hover:-translate-y-1.5">
                        <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300">
                            <i class="ti ti-chart-arrows text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Budgeting</h3>
                        <p class="text-slate-500 leading-relaxed text-sm">Tetapkan anggaran per kategori dan pantau progresnya secara real-time. Dapatkan gambaran jelas sebelum kebiasaan boros menghantam.</p>
                    </div>
                    <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-sky-200 hover:shadow-2xl hover:shadow-sky-600/10 transition-all duration-300 hover:-translate-y-1.5">
                        <div class="w-14 h-14 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all duration-300">
                            <i class="ti ti-target-arrow text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Target Finansial</h3>
                        <p class="text-slate-500 leading-relaxed text-sm">Tetapkan tujuan — dana darurat, liburan, hingga gadget impian — dan biarkan sistem memantau progres Anda sampai tercapai.</p>
                    </div>
                    <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:border-emerald-200 hover:shadow-2xl hover:shadow-emerald-600/10 transition-all duration-300 hover:-translate-y-1.5">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                            <i class="ti ti-brand-whatsapp text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Catat via WhatsApp</h3>
                        <p class="text-slate-500 leading-relaxed text-sm">Cara tercepat mencatat: cukup chat. Bot kami memahami pesan natural Anda dan langsung menyimpannya ke sistem — tanpa buka aplikasi.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================ -->
        <!-- PROBLEM SOLVING                                                  -->
        <!-- ================================================================ -->
        <section id="problem-solving" class="py-20 lg:py-28 bg-slate-50 border-y border-slate-100 relative overflow-hidden">
            <div class="absolute bottom-0 -left-32 w-96 h-96 bg-teal-200/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-14 lg:gap-20 items-center">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-5 tracking-tight leading-tight">
                            Keuangan tidak harus<br class="hidden md:block"> terasa <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-500">rumit.</span>
                        </h2>
                        <p class="text-slate-500 text-lg leading-relaxed mb-10">
                            Masalah bukan pada uangnya, tapi pada cara mencatatnya. PLMS Finance menyederhanakan seluruh proses — dari pencatatan hingga perencanaan — menjadi pengalaman yang menyenangkan.
                        </p>

                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 shadow-sm text-teal-600 flex items-center justify-center shrink-0">
                                    <i class="ti ti-bolt text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 mb-1">Cepat untuk dicatat</h3>
                                    <p class="text-slate-500 text-sm leading-relaxed">Kurang dari 5 detik per transaksi lewat WhatsApp. Selesai sebelum kembalian sampai di tangan Anda.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 shadow-sm text-teal-600 flex items-center justify-center shrink-0">
                                    <i class="ti ti-eye-check text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 mb-1">Mudah untuk dipahami</h3>
                                    <p class="text-slate-500 text-sm leading-relaxed">Data mentah diubah menjadi visual yang intuitif — ketahui pola pengeluaran Anda dalam sekali lihat.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 shadow-sm text-teal-600 flex items-center justify-center shrink-0">
                                    <i class="ti ti-target-arrow text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 mb-1">Jelas untuk direncanakan</h3>
                                    <p class="text-slate-500 text-sm leading-relaxed">Dengan data yang rapi, menetapkan budget dan target finansial menjadi keputusan yang terukur, bukan tebakan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative flex justify-center">
                        <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-teal-400/20 to-emerald-400/20 blur-3xl rounded-full scale-90"></div>
                        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl shadow-slate-900/10 border border-slate-100 overflow-hidden animate-float">
                            <div class="px-6 pt-6 pb-5 flex items-center justify-between border-b border-slate-50">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center text-white">
                                        <i class="ti ti-chart-arrows text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-sm leading-tight">Financial Overview</p>
                                        <p class="text-[11px] text-slate-400 font-semibold">Bulan Ini</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-100 text-emerald-600 text-[11px] font-bold px-3 py-1.5 rounded-full">
                                    <i class="ti ti-circle-check"></i> On Track
                                </span>
                            </div>
                            <div class="p-6 space-y-6">
                                <div class="flex items-center justify-between bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-lg shadow-teal-600/30">
                                            <i class="ti ti-wallet text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Saldo</p>
                                            <p class="text-lg font-extrabold text-slate-900">Rp 8,4jt</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                        <i class="ti ti-trending-up"></i> +8,2%
                                    </span>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                                            <p class="text-sm font-bold text-slate-700">Budget Terpakai</p>
                                        </div>
                                        <p class="text-sm font-extrabold text-teal-600">78%</p>
                                    </div>
                                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full w-[78%] bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full"></div>
                                    </div>
                                    <p class="text-[11px] text-slate-400 font-medium mt-1.5">Rp 2.4jt dari Rp 3jt — masih aman minggu ini</p>
                                </div>
                                <div class="flex items-start gap-3 bg-teal-50 border border-teal-100 rounded-2xl px-4 py-3.5">
                                    <i class="ti ti-bulb text-teal-600 text-lg shrink-0"></i>
                                    <p class="text-xs text-teal-800 font-medium leading-relaxed">Pengeluaran kategori Makan &amp; Minum turun 15% dibanding bulan lalu. Pertahankan!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================ -->
        <!-- CARA KERJA                                                       -->
        <!-- ================================================================ -->
        <section id="cara-kerja" class="relative py-24 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-extrabold uppercase tracking-[.2em] text-teal-700">Cara kerja</p>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        Dari transaksi acak menjadi sistem yang rapi.
                    </h2>
                    <p class="mt-5 leading-7 text-slate-500">
                        Mulai dari hal kecil. Setelah itu biarkan data membantu Anda memahami kebiasaan finansial.
                    </p>
                </div>

                <div class="relative mt-16 grid gap-10 md:grid-cols-3">
                    <div class="absolute left-[16%] right-[16%] top-9 hidden h-px bg-gradient-to-r from-transparent via-teal-200 to-transparent md:block"></div>

                    <div class="relative text-center group cursor-default">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-teal-100 bg-white text-2xl font-extrabold text-teal-600 shadow-lg shadow-teal-100/50 transition-all duration-300 group-hover:-translate-y-2 group-hover:bg-teal-600 group-hover:text-white">
                            01
                        </div>
                        <h3 class="mt-6 text-lg font-extrabold text-slate-900 group-hover:text-teal-700 transition-colors">Buat akun</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm leading-6 text-slate-500">
                            Siapkan akun dan mulai dengan kondisi keuangan Anda saat ini dengan cepat.
                        </p>
                    </div>

                    <div class="relative text-center group cursor-default">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-teal-100 bg-white text-2xl font-extrabold text-teal-600 shadow-lg shadow-teal-100/50 transition-all duration-300 group-hover:-translate-y-2 group-hover:bg-teal-600 group-hover:text-white">
                            02
                        </div>
                        <h3 class="mt-6 text-lg font-extrabold text-slate-900 group-hover:text-teal-700 transition-colors">Catat transaksi</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm leading-6 text-slate-500">
                            Masukkan pengeluaran melalui dashboard web atau gunakan chat WhatsApp Bot.
                        </p>
                    </div>

                    <div class="relative text-center group cursor-default">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-teal-100 bg-white text-2xl font-extrabold text-teal-600 shadow-lg shadow-teal-100/50 transition-all duration-300 group-hover:-translate-y-2 group-hover:bg-teal-600 group-hover:text-white">
                            03
                        </div>
                        <h3 class="mt-6 text-lg font-extrabold text-slate-900 group-hover:text-teal-700 transition-colors">Ambil kendali</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm leading-6 text-slate-500">
                            Lihat pola pengeluaran, atur budget, dan bergerak mulus menuju target finansial.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================ -->
        <!-- DEMO FORMAT (Interactive Accordion)                              -->
        <!-- ================================================================ -->
        <section id="demo" class="py-20 lg:py-28 bg-white scroll-mt-24 relative overflow-hidden">
            <div class="absolute top-0 left-0 -ml-32 -mt-32 w-96 h-96 bg-teal-100/50 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-14 lg:gap-20 items-center">

                    <!-- Kiri: Cheat sheet format (Accordion) -->
                    <div class="order-2 lg:order-1">
                        <span class="reveal inline-block px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold tracking-widest uppercase mb-5">Demo Format</span>
                        <h2 class="reveal reveal-delay-1 text-3xl md:text-4xl font-extrabold text-slate-900 mb-4 tracking-tight">Sesederhana Menulis Pesan Biasa</h2>
                        <p class="reveal reveal-delay-2 text-slate-500 text-lg mb-9 leading-relaxed">Tidak ada format kaku yang harus dihafal. Tulis seperti Anda chat dengan teman — bot memahami dan merapikannya untuk Anda.</p>

                        <div class="space-y-3" id="demo-accordion">
                            <!-- Item 1: Pengeluaran -->
                            <div class="demo-item bg-white border border-teal-200 shadow-md rounded-2xl overflow-hidden transition-all duration-300" data-type="pengeluaran">
                                <button class="w-full text-left px-5 py-4 font-bold text-slate-800 flex justify-between items-center focus:outline-none">
                                    <span class="flex items-center gap-2.5"><i class="ti ti-arrow-down-right text-rose-500"></i> Catat Pengeluaran</span>
                                    <i class="ti ti-chevron-down transform transition-transform rotate-180 text-teal-600 icon-arrow"></i>
                                </button>
                                <!-- Content dibungkus .accordion-wrapper (dengan .is-open default) -->
                                <div class="demo-content accordion-wrapper is-open">
                                    <div class="accordion-inner">
                                        <div class="px-5 pb-5 text-sm text-slate-500">
                                            <div class="bg-slate-900 text-teal-300 font-mono text-[12px] p-4 rounded-xl leading-relaxed mb-3 shadow-inner">
                                                Jenis : Pengeluaran<br>
                                                Kategori : Makanan<br>
                                                Nominal : 25000<br>
                                                Dompet : Cash<br>
                                                Keterangan : Makan Siang Nasi Padang
                                            </div>
                                            <p>Format terstruktur agar sistem mencatat pengeluaran Anda dengan akurasi 100%.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item 2: Pemasukan -->
                            <div class="demo-item bg-slate-50 border border-slate-100 hover:border-teal-200 rounded-2xl overflow-hidden transition-all duration-300" data-type="pemasukan">
                                <button class="w-full text-left px-5 py-4 font-bold text-slate-800 flex justify-between items-center focus:outline-none">
                                    <span class="flex items-center gap-2.5"><i class="ti ti-arrow-up-right text-emerald-500"></i> Catat Pemasukan</span>
                                    <i class="ti ti-chevron-down transform transition-transform text-slate-400 icon-arrow"></i>
                                </button>
                                <div class="demo-content accordion-wrapper">
                                    <div class="accordion-inner">
                                        <div class="px-5 pb-5 text-sm text-slate-500">
                                            <div class="bg-slate-900 text-teal-300 font-mono text-[12px] p-4 rounded-xl leading-relaxed mb-3 shadow-inner">
                                                Jenis : Pemasukan<br>
                                                Kategori : Gaji<br>
                                                Nominal : 5000000<br>
                                                Dompet : Bank<br>
                                                Keterangan : Gaji Bulan Ini
                                            </div>
                                            <p>Catat pemasukan bulanan atau tambahan dengan detail dompet tujuan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item 3: Transfer -->
                            <div class="demo-item bg-slate-50 border border-slate-100 hover:border-teal-200 rounded-2xl overflow-hidden transition-all duration-300" data-type="transfer">
                                <button class="w-full text-left px-5 py-4 font-bold text-slate-800 flex justify-between items-center focus:outline-none">
                                    <span class="flex items-center gap-2.5"><i class="ti ti-arrows-exchange text-indigo-500"></i> Transfer Saldo</span>
                                    <i class="ti ti-chevron-down transform transition-transform text-slate-400 icon-arrow"></i>
                                </button>
                                <div class="demo-content accordion-wrapper">
                                    <div class="accordion-inner">
                                        <div class="px-5 pb-5 text-sm text-slate-500">
                                            <div class="bg-slate-900 text-teal-300 font-mono text-[12px] p-4 rounded-xl leading-relaxed mb-3 shadow-inner">
                                                Transfer 200000 Cash ke Bank
                                            </div>
                                            <p>Pindahkan saldo antar dompet Anda dengan satu kalimat perintah sederhana.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item 4: Bantuan & Rekap -->
                            <div class="demo-item bg-slate-50 border border-slate-100 hover:border-teal-200 rounded-2xl overflow-hidden transition-all duration-300" data-type="bantuan">
                                <button class="w-full text-left px-5 py-4 font-bold text-slate-800 flex justify-between items-center focus:outline-none">
                                    <span class="flex items-center gap-2.5"><i class="ti ti-help-hexagon text-amber-500"></i> Command Bantuan</span>
                                    <i class="ti ti-chevron-down transform transition-transform text-slate-400 icon-arrow"></i>
                                </button>
                                <div class="demo-content accordion-wrapper">
                                    <div class="accordion-inner">
                                        <div class="px-5 pb-5 text-sm text-slate-500">
                                            <div class="bg-slate-900 text-teal-300 font-mono text-[12px] p-4 rounded-xl leading-relaxed mb-3 shadow-inner">
                                                BANTUAN
                                            </div>
                                            <p>Cukup ketik command singkat (BANTUAN, SALDO, REKAP) untuk memanggil menu sistem.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kanan: Mockup ponsel chat -->
                    <div class="reveal reveal-delay-1 order-1 lg:order-2 flex justify-center">
                        <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-teal-400/20 to-emerald-400/20 blur-3xl rounded-full scale-75 pointer-events-none"></div>

                        <div class="relative animate-float w-[300px] sm:w-[340px]">
                            <div class="rounded-[2.75rem] border-[10px] border-slate-900 bg-slate-900 shadow-2xl shadow-slate-900/40 overflow-hidden">
                                <!-- Header Bot -->
                                <div class="bg-slate-900 px-5 py-4 flex items-center gap-3 border-b border-white/5">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-emerald-600 flex items-center justify-center">
                                            <i class="ti ti-brand-whatsapp text-white text-xl"></i>
                                        </div>
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full ring-2 ring-slate-900"></span>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm font-bold leading-tight">PLMS Bot</p>
                                        <p class="text-emerald-400 text-[11px] font-semibold">Online — siap mencatat</p>
                                    </div>
                                </div>

                                <!-- Chat Dinamis -->
                                <div id="chat-screen" class="bg-[#111b21] px-4 py-5 space-y-3 min-h-[340px] flex flex-col justify-end transition-opacity duration-300">
                                    <!-- Isi chat akan di-generate oleh JavaScript di bawah -->
                                </div>

                                <!-- Input bar -->
                                <div class="bg-slate-900 px-4 py-3 flex items-center gap-2">
                                    <div class="flex-1 bg-slate-800 rounded-full px-4 py-2.5 text-xs text-slate-500">Ketik transaksi Anda...</div>
                                    <div class="w-9 h-9 rounded-full bg-teal-600 flex items-center justify-center text-white shadow-lg shadow-teal-600/40">
                                        <i class="ti ti-send text-base"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ================================================================ -->
        <!-- FAQ                                                              -->
        <!-- ================================================================ -->
        <section id="faq" class="py-20 lg:py-28 bg-slate-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="inline-block px-3.5 py-1.5 rounded-full bg-slate-200 text-slate-600 text-xs font-bold tracking-widest uppercase mb-5">FAQ</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4 tracking-tight">Paling Sering Ditanyain</h2>
                    <p class="text-slate-500 text-lg">Masih ragu atau ada yang bingung? Cek di sini ya.</p>
                </div>

                <div class="space-y-4" id="faq-container">
                    <div class="faq-item bg-white border border-slate-200 hover:border-teal-300 rounded-2xl transition-all shadow-sm">
                        <button class="w-full text-left flex items-center justify-between gap-4 p-5 lg:p-6 font-bold text-slate-800 text-[15px] focus:outline-none">
                            <span>Beneran gratis nih aplikasinya?</span>
                            <i class="ti ti-plus faq-icon text-teal-600 text-xl shrink-0 transition-transform duration-300"></i>
                        </button>
                        <div class="accordion-wrapper">
                            <div class="accordion-inner">
                                <p class="px-5 lg:px-6 pb-6 -mt-1 text-slate-500 text-sm leading-relaxed">Iya dong! Fitur utamanya kayak bot WA, pencatatan transaksi, sampe dashboard kece itu gratis selamanya. Nggak usah pusing mikirin biaya langganan buat mulai rajin nyatet pengeluaran.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white border border-slate-200 hover:border-teal-300 rounded-2xl transition-all shadow-sm">
                        <button class="w-full text-left flex items-center justify-between gap-4 p-5 lg:p-6 font-bold text-slate-800 text-[15px] focus:outline-none">
                            <span>Harus ngafalin format khusus buat nge-chat bot nggak?</span>
                            <i class="ti ti-plus faq-icon text-teal-600 text-xl shrink-0 transition-transform duration-300"></i>
                        </button>
                        <div class="accordion-wrapper">
                            <div class="accordion-inner">
                                <p class="px-5 lg:px-6 pb-6 -mt-1 text-slate-500 text-sm leading-relaxed">Sama sekali nggak! Ketik aja santai kayak lagi nge-chat temen. Misalnya "makan siang 25rb" atau "gaji 5jt". Sistem kita udah pinter buat nangkep maksud nominal dan kategorinya otomatis.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white border border-slate-200 hover:border-teal-300 rounded-2xl transition-all shadow-sm">
                        <button class="w-full text-left flex items-center justify-between gap-4 p-5 lg:p-6 font-bold text-slate-800 text-[15px] focus:outline-none">
                            <span>Data keuanganku aman kan di sini?</span>
                            <i class="ti ti-plus faq-icon text-teal-600 text-xl shrink-0 transition-transform duration-300"></i>
                        </button>
                        <div class="accordion-wrapper">
                            <div class="accordion-inner">
                                <p class="px-5 lg:px-6 pb-6 -mt-1 text-slate-500 text-sm leading-relaxed">Aman banget. Akun kamu nyambung ke nomor WA yang udah diverifikasi, dan datanya tersimpan di server dengan enkripsi yang kuat. Kita nggak bakal pernah ngintip atau jual data kamu ke siapa pun.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white border border-slate-200 hover:border-teal-300 rounded-2xl transition-all shadow-sm">
                        <button class="w-full text-left flex items-center justify-between gap-4 p-5 lg:p-6 font-bold text-slate-800 text-[15px] focus:outline-none">
                            <span>Bisa bikin banyak dompet atau rekening beda-beda?</span>
                            <i class="ti ti-plus faq-icon text-teal-600 text-xl shrink-0 transition-transform duration-300"></i>
                        </button>
                        <div class="accordion-wrapper">
                            <div class="accordion-inner">
                                <p class="px-5 lg:px-6 pb-6 -mt-1 text-slate-500 text-sm leading-relaxed">Bisa banget! Mau misahin saldo buat uang cash, rekening BCA, GoPay, DANA, sampe ShopeePay juga bisa. Kalau mau nyatet transfer duit antar dompet juga gampang banget tinggal klik.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item bg-white border border-slate-200 hover:border-teal-300 rounded-2xl transition-all shadow-sm">
                        <button class="w-full text-left flex items-center justify-between gap-4 p-5 lg:p-6 font-bold text-slate-800 text-[15px] focus:outline-none">
                            <span>Kalau lagi males ngetik di WA, bisa nyatet manual lewat web nggak?</span>
                            <i class="ti ti-plus faq-icon text-teal-600 text-xl shrink-0 transition-transform duration-300"></i>
                        </button>
                        <div class="accordion-wrapper">
                            <div class="accordion-inner">
                                <p class="px-5 lg:px-6 pb-6 -mt-1 text-slate-500 text-sm leading-relaxed">Pasti bisa. Bot WA itu sebenernya cuma alat bantu biar cepet aja pas lagi di jalan. Kamu tetep bebas nambahin atau ngedit transaksi secara manual langsung dari dashboard web, dan otomatis bakal sinkron dua-duanya.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================ -->
        <!-- CTA & FOOTER                                                     -->
        <!-- ================================================================ -->
        <section class="px-4 pb-20 sm:px-6 sm:pb-24 lg:px-8 pt-10 bg-slate-50">
            <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-teal-700 px-6 py-14 text-center shadow-2xl shadow-teal-900/15 sm:px-12 sm:py-16">
                <div class="absolute -left-20 -top-20 h-60 w-60 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-24 -right-20 h-72 w-72 rounded-full bg-cyan-300/10 blur-3xl"></div>

                <div class="relative mx-auto max-w-2xl">
                    <p class="text-xs font-extrabold uppercase tracking-[.2em] text-teal-100">Mulai dari sekarang</p>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        Saatnya uangmu punya arah.
                    </h2>
                    <p class="mt-5 leading-7 text-teal-100">
                        Tidak perlu menunggu sampai keuangan terasa berantakan.
                        Mulai catat, pahami, dan rencanakan dari sekarang.
                    </p>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-white px-6 py-4 text-sm font-extrabold text-teal-800 shadow-xl transition hover:-translate-y-1 hover:bg-teal-50">
                            Buat Akun Gratis
                            <i class="ti ti-arrow-up-right text-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <footer class="bg-white border-t border-slate-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12">
                    <div class="lg:col-span-2">
                        <a href="#" class="inline-flex items-center gap-2.5 mb-6">
                            <div class="w-9 h-9 bg-teal-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-teal-700/20">
                                <i class="ti ti-wallet text-xl"></i>
                            </div>
                            <span class="font-extrabold text-xl tracking-tight text-slate-900">PLMS<span class="text-teal-600">Finance</span></span>
                        </a>
                        <p class="text-slate-500 text-sm leading-relaxed max-w-sm mb-7">
                            Aplikasi manajemen keuangan pribadi pintar. Kami memadukan kecepatan WhatsApp Bot dengan keakuratan dashboard analitik untuk membuat tracking keuangan tidak lagi membosankan.
                        </p>
                        <div class="flex gap-3">
                            <a href="#" aria-label="GitHub" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-teal-600 hover:text-white hover:-translate-y-0.5 transition-all shadow-sm border border-slate-100">
                                <i class="ti ti-brand-github text-xl"></i>
                            </a>
                            <a href="#" aria-label="Instagram" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-teal-600 hover:text-white hover:-translate-y-0.5 transition-all shadow-sm border border-slate-100">
                                <i class="ti ti-brand-instagram text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-5 text-xs tracking-widest uppercase">Navigasi</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#keunggulan" class="text-slate-500 hover:text-teal-600 transition font-medium">Fitur Keunggulan</a></li>
                            <li><a href="#cara-kerja" class="text-slate-500 hover:text-teal-600 transition font-medium">Cara Kerja Aplikasi</a></li>
                            <li><a href="#demo" class="text-slate-500 hover:text-teal-600 transition font-medium">Demo WhatsApp Bot</a></li>
                            <li><a href="#faq" class="text-slate-500 hover:text-teal-600 transition font-medium">Tanya Jawab (FAQ)</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-5 text-xs tracking-widest uppercase">Portal Akun</h4>
                        <ul class="space-y-3 text-sm">
                            @if (Route::has('login'))
                                <li><a href="{{ route('login') }}" class="text-slate-500 hover:text-teal-600 transition font-medium">Masuk Aplikasi</a></li>
                            @endif
                            @if (Route::has('register'))
                                <li><a href="{{ route('register') }}" class="text-slate-500 hover:text-teal-600 transition font-medium">Registrasi Gratis</a></li>
                            @endif
                            @auth
                                <li><a href="{{ url('/dashboard') }}" class="text-slate-500 hover:text-teal-600 transition font-medium">Dashboard Pribadi</a></li>
                                <li><a href="{{ url('/profile') }}" class="text-slate-500 hover:text-teal-600 transition font-medium">Pengaturan Profil</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-5 text-xs tracking-widest uppercase">Legal &amp; Bantuan</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="text-slate-500 hover:text-teal-600 transition font-medium">Panduan Penggunaan Bot</a></li>
                            <li><a href="#" class="text-slate-500 hover:text-teal-600 transition font-medium">Kebijakan Privasi Data</a></li>
                            <li><a href="#" class="text-slate-500 hover:text-teal-600 transition font-medium">Syarat &amp; Ketentuan</a></li>
                            <li class="pt-3">
                                <a href="mailto:support@plms-finance.app" class="inline-flex items-center justify-center gap-2 text-teal-700 bg-teal-50 border border-teal-100 hover:bg-teal-100 px-4 py-2 rounded-lg transition font-medium w-full">
                                    <i class="ti ti-headset"></i> Hubungi Support
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-slate-100 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-slate-400 text-sm text-center md:text-left font-medium">
                        &copy; {{ date('Y') }} PLMS Finance. Hak Cipta Dilindungi.
                    </p>
                    <div class="flex items-center gap-2 text-slate-400 text-sm font-medium">
                        Dikembangkan oleh <b class="text-teal-600">Moch Miftahul Khoironi</b> di Indonesia
                    </div>
                </div>
            </div>
        </footer>

        <!-- ================================================================ -->
        <!-- SCRIPTS                                                          -->
        <!-- ================================================================ -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {

                // ===== 1. Navbar: Efek Scroll Background =====
                const navbar = document.getElementById('navbar');
                const onScroll = () => {
                    if (window.scrollY > 20) {
                        navbar.classList.add('nav-scrolled');
                    } else {
                        navbar.classList.remove('nav-scrolled');
                    }
                };
                window.addEventListener('scroll', onScroll, { passive: true });
                onScroll();

                // ===== 2. Menu Mobile =====
                const menuBtn  = document.getElementById('menu-btn');
                const menu     = document.getElementById('mobile-menu');
                const menuIcon = document.getElementById('menu-icon');
                const closeMenu = () => {
                    menu.classList.add('hidden');
                    menuIcon.className = 'ti ti-menu-2 text-xl';
                };
                menuBtn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                    menuIcon.className = menu.classList.contains('hidden') ? 'ti ti-menu-2 text-xl' : 'ti ti-x text-xl';
                });
                menu.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));

                // ===== 3. Animasi Accordion FAQ =====
                const faqItems = document.querySelectorAll('.faq-item');
                faqItems.forEach(item => {
                    const btn = item.querySelector('button');
                    const wrapper = item.querySelector('.accordion-wrapper');
                    const icon = item.querySelector('.faq-icon');

                    btn.addEventListener('click', () => {
                        const isOpen = wrapper.classList.contains('is-open');

                        // Tutup FAQ lain yang terbuka
                        faqItems.forEach(otherItem => {
                            otherItem.querySelector('.accordion-wrapper').classList.remove('is-open');
                            otherItem.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
                        });

                        // Buka yang diklik
                        if (!isOpen) {
                            wrapper.classList.add('is-open');
                            icon.style.transform = 'rotate(45deg)';
                        }
                    });
                });
            });
        </script>

        <!-- Script Interaktif Demo Chat -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const accordionItems = document.querySelectorAll('.demo-item');
                const chatScreen = document.getElementById('chat-screen');

                const chatData = {
                    pengeluaran: `
                        <div class="flex justify-center mb-2"><span class="text-[10px] font-bold text-slate-400 bg-slate-800/80 px-3 py-1 rounded-lg">HARI INI</span></div>
                        <div class="flex justify-end fade-in">
                            <div class="bg-teal-600 text-white text-[13px] leading-snug rounded-2xl rounded-br-md px-3.5 py-2.5 max-w-[85%] shadow">
                                Jenis : Pengeluaran<br>Kategori : Makanan<br>Nominal : 25000<br>Dompet : Cash<br>Keterangan : Makan Siang Nasi Padang
                                <span class="block text-[9px] text-teal-200/80 text-right mt-1">12.45 ✓✓</span>
                            </div>
                        </div>
                        <div class="flex justify-start fade-in" style="animation-delay: 0.3s">
                            <div class="bg-[#202c33] text-slate-100 text-[13px] leading-snug rounded-2xl rounded-bl-md px-3.5 py-2.5 max-w-[90%] shadow">
                                <b>✅ Transaksi Dicatat!</b><br><br>📌 <b>Jenis:</b> 🔴 Pengeluaran<br>📂 <b>Kategori:</b> Makanan<br>💰 <b>Nominal:</b> Rp 25.000<br>💳 <b>Dompet:</b> Cash<br>📝 <b>Catatan:</b> Makan Siang Nasi Padang
                            </div>
                        </div>`,
                    pemasukan: `
                        <div class="flex justify-center mb-2"><span class="text-[10px] font-bold text-slate-400 bg-slate-800/80 px-3 py-1 rounded-lg">HARI INI</span></div>
                        <div class="flex justify-end fade-in">
                            <div class="bg-teal-600 text-white text-[13px] leading-snug rounded-2xl rounded-br-md px-3.5 py-2.5 max-w-[85%] shadow">
                                Jenis : Pemasukan<br>Kategori : Gaji<br>Nominal : 5000000<br>Dompet : Bank<br>Keterangan : Gaji Bulan Ini
                                <span class="block text-[9px] text-teal-200/80 text-right mt-1">09.10 ✓✓</span>
                            </div>
                        </div>
                        <div class="flex justify-start fade-in" style="animation-delay: 0.3s">
                            <div class="bg-[#202c33] text-slate-100 text-[13px] leading-snug rounded-2xl rounded-bl-md px-3.5 py-2.5 max-w-[90%] shadow">
                                <b>✅ Transaksi Dicatat!</b><br><br>📌 <b>Jenis:</b> 🟢 Pemasukan<br>📂 <b>Kategori:</b> Gaji<br>💰 <b>Nominal:</b> Rp 5.000.000<br>💳 <b>Dompet:</b> Bank<br>📝 <b>Catatan:</b> Gaji Bulan Ini
                            </div>
                        </div>`,
                    transfer: `
                        <div class="flex justify-center mb-2"><span class="text-[10px] font-bold text-slate-400 bg-slate-800/80 px-3 py-1 rounded-lg">HARI INI</span></div>
                        <div class="flex justify-end fade-in">
                            <div class="bg-teal-600 text-white text-[13px] leading-snug rounded-2xl rounded-br-md px-3.5 py-2.5 max-w-[80%] shadow">
                                Transfer 200000 Cash ke Bank
                                <span class="block text-[9px] text-teal-200/80 text-right mt-1">15.30 ✓✓</span>
                            </div>
                        </div>
                        <div class="flex justify-start fade-in" style="animation-delay: 0.3s">
                            <div class="bg-[#202c33] text-slate-100 text-[13px] leading-snug rounded-2xl rounded-bl-md px-3.5 py-2.5 max-w-[90%] shadow">
                                <b>🔄 Transfer Berhasil!</b><br><br>💰 <b>Nominal:</b> Rp 200.000<br>📤 <b>Dari:</b> Cash<br>📥 <b>Ke:</b> Bank
                            </div>
                        </div>`,
                    bantuan: `
                        <div class="flex justify-center mb-2"><span class="text-[10px] font-bold text-slate-400 bg-slate-800/80 px-3 py-1 rounded-lg">HARI INI</span></div>
                        <div class="flex justify-end fade-in">
                            <div class="bg-teal-600 text-white text-[13px] leading-snug rounded-2xl rounded-br-md px-3.5 py-2.5 max-w-[80%] shadow">
                                BANTUAN
                                <span class="block text-[9px] text-teal-200/80 text-right mt-1">10.05 ✓✓</span>
                            </div>
                        </div>
                        <div class="flex justify-start fade-in" style="animation-delay: 0.3s">
                            <div class="bg-[#202c33] text-slate-100 text-[13px] leading-snug rounded-2xl rounded-bl-md px-3.5 py-2.5 max-w-[95%] shadow">
                                🤖 <b>PANDUAN BOT PLMS FINANCE</b><br><br>📋 <b>Command Cepat:</b><br>• <b>SALDO</b> : Cek saldo dompet<br>• <b>REKAP</b> : Rekap bulan ini<br>• <b>RIWAYAT</b> : 5 transaksi terakhir<br>• <b>BANTUAN</b> : Menu ini
                            </div>
                        </div>`
                };

                const updateChatScreen = (type) => {
                    chatScreen.style.opacity = 0;
                    setTimeout(() => {
                        chatScreen.innerHTML = chatData[type];
                        chatScreen.style.opacity = 1;
                    }, 150);
                };

                updateChatScreen('pengeluaran');

                accordionItems.forEach(item => {
                    const btn = item.querySelector('button');
                    btn.addEventListener('click', () => {
                        if(item.classList.contains('bg-white')) return;

                        accordionItems.forEach(el => {
                            el.classList.remove('bg-white', 'border-teal-200', 'shadow-md');
                            el.classList.add('bg-slate-50', 'border-slate-100');
                            el.querySelector('.demo-content').classList.remove('is-open');

                            const icon = el.querySelector('.icon-arrow');
                            icon.classList.remove('rotate-180', 'text-teal-600');
                            icon.classList.add('text-slate-400');
                        });

                        item.classList.remove('bg-slate-50', 'border-slate-100');
                        item.classList.add('bg-white', 'border-teal-200', 'shadow-md');
                        item.querySelector('.demo-content').classList.add('is-open');

                        const icon = item.querySelector('.icon-arrow');
                        icon.classList.remove('text-slate-400');
                        icon.classList.add('rotate-180', 'text-teal-600');

                        updateChatScreen(item.getAttribute('data-type'));
                    });
                });
            });
        </script>
    </body>
</html>
