<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PLMS Finance') }} - Otentikasi</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .bg-dots {
                background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
                background-size: 24px 24px;
            }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 bg-dots selection:bg-teal-500 selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">

            <!-- Logo Brand -->
            <div>
                <a href="/" class="flex items-center gap-2 mb-6 group">
                    <div class="w-12 h-12 rounded-xl bg-teal-600 flex items-center justify-center text-white font-bold shadow-lg shadow-teal-600/30 group-hover:scale-105 transition-transform">
                        <i class="ti ti-wallet text-3xl"></i>
                    </div>
                    <span class="font-bold text-3xl tracking-tight text-slate-900">PLMS<span class="text-teal-600">Finance</span></span>
                </a>
            </div>

            <!-- Card Formulir -->
            <div class="w-full sm:max-w-md mt-2 px-8 py-10 bg-white shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-100 overflow-hidden">
                {{ $slot }}
            </div>

            <!-- Footer Simple -->
            <div class="mt-8 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} PLMS Finance. All rights reserved.
            </div>
        </div>

        <!-- Script Anti Double-Submit -->
        <script>
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form.getAttribute('data-submitting') === 'true') {
                    e.preventDefault();
                    return;
                }
                form.setAttribute('data-submitting', 'true');

                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                }
            });
        </script>
    </body>
</html>
