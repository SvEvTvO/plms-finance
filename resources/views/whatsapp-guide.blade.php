<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
            {{ __('Integrasi WhatsApp Bot') }}
        </h2>
    </x-slot>

    <!-- WRAPPER UTAMA -->
    <div class="max-w-7xl mx-auto space-y-12 pb-12">

        <!-- ========================================== -->
        <!-- SECTION 0: INTRO & PENJELASAN FITUR        -->
        <!-- ========================================== -->
        <section class="mb-8">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 lg:p-10 shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">

                    <!-- Kiri: Teks Utama -->
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 text-teal-700 text-xs font-bold rounded-full uppercase tracking-widest mb-5 border border-teal-100">
                            <i class="ti ti-sparkles text-sm"></i> Inovasi Baru
                        </span>

                        <h2 class="text-3xl font-extrabold text-slate-900 leading-tight mb-4">
                            Catat Keuangan Semudah Balas Chat Teman.
                        </h2>

                        <p class="text-slate-600 text-sm leading-relaxed text-justify lg:text-left">
                            Pernah merasa malas membuka aplikasi web hanya untuk mencatat pengeluaran kecil seperti uang parkir atau beli kopi? <strong>PLMS-Finance WhatsApp Bot</strong> hadir untuk memecahkan masalah itu.
                            <br><br>
                            Kini Anda bisa mencatat transaksi, mengecek saldo, hingga melihat rekap bulanan langsung dari WhatsApp—aplikasi yang paling sering Anda buka setiap hari.
                        </p>
                    </div>

                    <!-- Kanan: 3 Poin Keunggulan (List Style) -->
                    <div class="space-y-4">

                        <!-- Poin 1 -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-teal-200 transition">
                            <div class="w-12 h-12 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                                <i class="ti ti-bolt text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm mb-1">Cepat & Instan</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">Tanpa <i>login</i> atau <i>loading browser</i>. Ketik nominal, kirim, dan data langsung tersimpan secara <i>real-time</i>.</p>
                            </div>
                        </div>

                        <!-- Poin 2 -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-teal-200 transition">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="ti ti-plug text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm mb-1">Mudah Digunakan</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">Cukup daftarkan nomor WA di menu profil, *scan QR* di bawah, dan bot siap membantu 24/7.</p>
                            </div>
                        </div>

                        <!-- Poin 3 -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-teal-200 transition">
                            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <i class="ti ti-shield-check text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm mb-1">Privasi Terjaga Terjamin</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">Sistem kami hanya akan membaca dan merespon pesan dari nomor WhatsApp yang sudah terverifikasi.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- SECTION 1: HERO (QR CODE & STATUS)         -->
        <!-- ========================================== -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Kiri: Card QR Code (Proporsi lebih besar) -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <!-- Header Hijau -->
                <div class="bg-teal-600 p-8 text-center">
                    <h3 class="text-white font-extrabold text-xl flex items-center justify-center gap-2">
                        Yuk, Mulai Chat Bot-nya!
                    </h3>
                    <p class="text-teal-100 text-sm mt-1">Scan QR atau klik tombol di bawah untuk terhubung.</p>
                </div>

                <!-- Body Card -->
                <div class="p-8 flex flex-col items-center bg-white">
                    <!-- Frame QR -->
                    <div class="p-4 bg-white border border-slate-200 rounded-3xl shadow-sm mb-6">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=https://wa.me/6283839717167?text=BANTUAN" alt="QR Code WhatsApp" class="w-44 h-44 object-contain">
                    </div>

                    <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Nomor WhatsApp Bot</span>

                    <!-- Nomor & Copy -->
                    <div class="flex items-center justify-center gap-3 mt-2 mb-8">
                        <span class="text-3xl font-black text-slate-800 tracking-tight" id="bot-number">083839717167</span>
                        <button onclick="copyText('bot-number', this)" class="text-slate-400 hover:text-teal-600 transition p-2 hover:bg-slate-100 rounded-lg" title="Copy Nomor">
                            <i class="ti ti-copy text-2xl"></i>
                        </button>
                    </div>

                    <!-- Tombol Aksi -->
                    <a href="https://wa.me/6283839717167?text=BANTUAN" target="_blank" class="w-full max-w-md py-3.5 bg-[#24cc63] hover:bg-[#1ea650] text-white rounded-xl font-bold text-sm text-center transition flex items-center justify-center gap-2 shadow-md">
                        <i class="ti ti-brand-whatsapp text-xl text-white"></i> Buka WhatsApp Sekarang
                    </a>
                </div>
            </div>

            <!-- Kanan: Card Status Sistem -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm h-full">
                <h4 class="font-extrabold text-slate-800 text-sm tracking-widest uppercase mb-6 border-b border-slate-100 pb-4">STATUS SISTEM</h4>

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm font-semibold text-slate-700">Bot Engine</span>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Online</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm font-semibold text-slate-700">WhatsApp API</span>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Connected</span>
                    </div>

                    <div class="flex items-center justify-between opacity-50">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span class="text-sm font-semibold text-slate-700">Webhooks</span>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Idle</span>
                    </div>
                </div>
            </div>
        </section>


        <!-- ========================================== -->
        <!-- SECTION 2: FORMAT CHAT TRANSAKSI           -->
        <!-- ========================================== -->
        <section>
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-slate-900">Format Chat Transaksi</h2>
                <p class="text-sm text-slate-500 mt-1">Salin format di bawah ini dan isi datanya untuk mencatat transaksi melalui WhatsApp.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Kiri: List Format -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- Format Pengeluaran -->
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-800">Mencatat Pengeluaran</span>
                            <button onclick="copyText('fmt-pengeluaran', this)" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-teal-600 transition">
                                <i class="ti ti-copy text-base"></i> Copy
                            </button>
                        </div>
                        <div class="p-5">
                            <pre id="fmt-pengeluaran" class="font-mono text-sm text-slate-700 leading-loose">Jenis : Pengeluaran
Kategori : Makanan
Nominal : 25000
Dompet : Cash
Keterangan : Makan Siang Nasi Padang</pre>
                        </div>
                    </div>

                    <!-- Format Pemasukan -->
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-800">Mencatat Pemasukan</span>
                            <button onclick="copyText('fmt-pemasukan', this)" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-teal-600 transition">
                                <i class="ti ti-copy text-base"></i> Copy
                            </button>
                        </div>
                        <div class="p-5">
                            <pre id="fmt-pemasukan" class="font-mono text-sm text-slate-700 leading-loose">Jenis : Pemasukan
Kategori : Gaji
Nominal : 5000000
Dompet : BCA
Keterangan : Gaji Bulan Ini</pre>
                        </div>
                    </div>

                    <!-- Format Transfer -->
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-800">Transfer Antar Dompet</span>
                            <button onclick="copyText('fmt-transfer', this)" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-teal-600 transition">
                                <i class="ti ti-copy text-base"></i> Copy
                            </button>
                        </div>
                        <div class="p-5">
                            <pre id="fmt-transfer" class="font-mono text-sm text-slate-700 leading-loose">Transfer 50000 BCA ke GoPay</pre>
                        </div>
                    </div>

                </div>

                <!-- Kanan: Mockup Layar HP 1 -->
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="w-full max-w-[320px] bg-slate-100 border-[8px] border-slate-800 rounded-[2.5rem] h-[520px] relative shadow-xl overflow-hidden flex flex-col">
                        <!-- Poni HP (Notch) -->
                        <div class="absolute top-0 inset-x-0 h-5 bg-slate-800 w-32 mx-auto rounded-b-xl z-20"></div>

                        <!-- Header Chat -->
                        <div class="bg-white border-b px-4 py-3 pt-6 flex items-center gap-3 z-10 shrink-0">
                            <i class="ti ti-arrow-left text-slate-400"></i>
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
                                <i class="ti ti-robot text-slate-500"></i>
                            </div>
                            <span class="font-bold text-slate-800 text-sm truncate">PLMS-Finance Bot</span>
                        </div>

                        <!-- Body Chat -->
                        <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-[#efeae2]" style="background-image: radial-gradient(#d4cece 1px, transparent 1px); background-size: 10px 10px;">

                            <!-- Chat User (Format) -->
                            <div class="self-end ml-auto bg-[#d9fdd3] text-slate-800 p-2.5 rounded-xl rounded-tr-none shadow-sm max-w-[85%] text-xs font-mono border border-[#c4ebb5]">
                                Jenis : Pengeluaran<br>
                                Kategori : Makanan<br>
                                Nominal : 25000<br>
                                Dompet : Cash<br>
                                Keterangan : Nasi Padang
                            </div>

                            <!-- Chat Bot (Respon) -->
                            <div class="self-start mr-auto bg-white text-slate-800 p-3 rounded-xl rounded-tl-none shadow-sm max-w-[85%] text-xs border border-slate-200">
                                ✅ <strong>Transaksi Berhasil Dicatat!</strong><br><br>
                                📌 Jenis: 🔴 Pengeluaran<br>
                                📂 Kategori: Makanan<br>
                                💰 Nominal: Rp 25.000<br>
                                💳 Dompet: Cash (Sisa: Rp 125.000)<br>
                                📝 Catatan: Nasi Padang
                            </div>

                        </div>

                        <!-- Footer Chat (Input) -->
                        <div class="bg-white p-2 border-t flex gap-2 items-center shrink-0">
                            <div class="flex-1 bg-slate-100 rounded-full h-9 px-4 flex items-center text-slate-400 text-xs">Ketik pesan...</div>
                            <div class="w-9 h-9 rounded-full bg-teal-600 flex items-center justify-center text-white shrink-0 shadow-sm"><i class="ti ti-send text-sm"></i></div>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        <!-- ========================================== -->
        <!-- SECTION 3: COMMAND BOT INTERAKTIF          -->
        <!-- ========================================== -->
        <section>
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="ti ti-command text-teal-600"></i> Command Bot Interaktif
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kirimkan perintah singkat ini ke bot untuk mengecek laporan keuangan Anda.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Kiri: Mockup Layar HP 2 -->
                <div class="lg:col-span-5 flex justify-center lg:justify-start">
                    <div class="w-full max-w-[320px] bg-slate-100 border-[8px] border-slate-800 rounded-[2.5rem] h-[520px] relative shadow-xl overflow-hidden flex flex-col">
                        <div class="absolute top-0 inset-x-0 h-5 bg-slate-800 w-32 mx-auto rounded-b-xl z-20"></div>

                        <div class="bg-white border-b px-4 py-3 pt-6 flex items-center gap-3 z-10 shrink-0">
                            <i class="ti ti-arrow-left text-slate-400"></i>
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
                                <i class="ti ti-robot text-slate-500"></i>
                            </div>
                            <span class="font-bold text-slate-800 text-sm truncate">PLMS-Finance Bot</span>
                        </div>

                        <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-[#efeae2]" style="background-image: radial-gradient(#d4cece 1px, transparent 1px); background-size: 10px 10px;">
                            <!-- Chat User -->
                            <div class="self-end ml-auto bg-[#d9fdd3] text-slate-800 p-2.5 rounded-xl rounded-tr-none shadow-sm max-w-[85%] text-[13px] font-mono border border-[#c4ebb5] font-bold">
                                SALDO
                            </div>

                            <!-- Chat Bot -->
                            <div class="self-start mr-auto bg-white text-slate-800 p-3 rounded-xl rounded-tl-none shadow-sm max-w-[90%] text-xs border border-slate-200">
                                💳 <strong>INFORMASI SALDO DOMPET</strong><br><br>
                                • Cash: Rp 150.000<br>
                                • BCA: Rp 3.250.000<br>
                                • GoPay: Rp 45.000<br>
                                ────────────────<br>
                                💰 <strong>Total Aset:</strong> Rp 3.445.000
                            </div>
                        </div>

                        <div class="bg-white p-2 border-t flex gap-2 items-center shrink-0">
                            <div class="flex-1 bg-slate-100 rounded-full h-9 px-4 flex items-center text-slate-400 text-xs">Ketik pesan...</div>
                            <div class="w-9 h-9 rounded-full bg-teal-600 flex items-center justify-center text-white shrink-0 shadow-sm"><i class="ti ti-send text-sm"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Kanan: List Command -->
                <div class="lg:col-span-7 space-y-4">

                    <!-- Command 1: SALDO -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-900">Cek Saldo Dompet</h4>
                            <p class="text-sm text-slate-500 mt-1">Bot akan membalas dengan rincian saldo semua dompet aktif beserta total aset Anda.</p>
                        </div>
                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl p-2 pl-4">
                            <code class="font-mono font-bold text-slate-700" id="cmd-saldo">SALDO</code>
                            <button onclick="copyText('cmd-saldo', this)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-teal-600 transition shadow-sm">
                                <i class="ti ti-copy"></i> Copy
                            </button>
                        </div>
                    </div>

                    <!-- Command 2: REKAP -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-900">Ringkasan Bulan Ini</h4>
                            <p class="text-sm text-slate-500 mt-1">Melihat total uang masuk, uang keluar, dan surplus/defisit di bulan berjalan secara instan.</p>
                        </div>
                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl p-2 pl-4">
                            <code class="font-mono font-bold text-slate-700" id="cmd-rekap">REKAP</code>
                            <button onclick="copyText('cmd-rekap', this)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-teal-600 transition shadow-sm">
                                <i class="ti ti-copy"></i> Copy
                            </button>
                        </div>
                    </div>

                    <!-- Command 3: RIWAYAT -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-900">Cek Histori Transaksi</h4>
                            <p class="text-sm text-slate-500 mt-1">Menampilkan daftar 5 aktivitas transaksi terakhir yang sukses tercatat di sistem.</p>
                        </div>
                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl p-2 pl-4">
                            <code class="font-mono font-bold text-slate-700" id="cmd-riwayat">RIWAYAT</code>
                            <button onclick="copyText('cmd-riwayat', this)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-teal-600 transition shadow-sm">
                                <i class="ti ti-copy"></i> Copy
                            </button>
                        </div>
                    </div>

                    <!-- Command 4: BANTUAN -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-900">Menu Petunjuk</h4>
                            <p class="text-sm text-slate-500 mt-1">Lupa format? Ketik ini dan bot akan mengirimkan pesan berisi panduan pemakaian.</p>
                        </div>
                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl p-2 pl-4">
                            <code class="font-mono font-bold text-slate-700" id="cmd-bantuan">BANTUAN</code>
                            <button onclick="copyText('cmd-bantuan', this)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-teal-600 transition shadow-sm">
                                <i class="ti ti-copy"></i> Copy
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </div>

    <!-- SCRIPT COPY -->
    <script>
        function copyText(elementId, buttonElement) {
            const textToCopy = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(textToCopy).then(() => {
                const originalHTML = buttonElement.innerHTML;
                // Ubah state jadi copied
                buttonElement.innerHTML = '<i class="ti ti-check text-emerald-600 text-base"></i> <span class="text-emerald-600">Copied</span>';

                // Reset setelah 2 detik
                setTimeout(() => {
                    buttonElement.innerHTML = originalHTML;
                }, 2000);
            }).catch(err => alert("Gagal menyalin teks."));
        }
    </script>
</x-app-layout>
