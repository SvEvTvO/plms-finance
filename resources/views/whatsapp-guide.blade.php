<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panduan Integrasi WhatsApp Bot') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Status Nomor Terdaftar -->
            <div class="p-6 bg-white shadow sm:rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Status Nomor WhatsApp</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Nomor terdaftar:
                        <span class="font-semibold text-emerald-600">
                            {{ auth()->user()->whatsapp_number ?? 'Belum Terdaftar' }}
                        </span>
                    </p>
                    @if(!auth()->user()->whatsapp_number)
                        <p class="text-xs text-rose-500 mt-1">⚠️ Daftarkan nomor WhatsApp Anda di menu Profil untuk mulai mencatat via chat.</p>
                    @endif
                </div>
                <a href="https://wa.me/6283839717167?text=BANTUAN" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 transition">
                    💬 Chat Bot Sekarang
                </a>
            </div>

            <!-- Cheat Sheet Format Chat -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Format Chat Transaksi</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Format Cepat -->
                    <div class="p-4 border rounded-lg bg-gray-50 space-y-3">
                        <span class="font-bold text-gray-800">⚡ Format 1 Baris (Cepat)</span>
                        <div class="text-sm space-y-2 font-mono">
                            <p class="bg-white p-2 border rounded">🔴 <strong>Pengeluaran:</strong><br>Makan Siang 25000 Cash<br>Beli Kopi 18000 GoPay</p>
                            <p class="bg-white p-2 border rounded">🟢 <strong>Pemasukan:</strong><br>+ Gaji 5000000 BCA<br>Masuk Uang Saku 50000</p>
                            <p class="bg-white p-2 border rounded">🔄 <strong>Transfer:</strong><br>Transfer 50000 BCA ke GoPay</p>
                        </div>
                    </div>

                    <!-- Command Bantu -->
                    <div class="p-4 border rounded-lg bg-gray-50 space-y-3">
                        <span class="font-bold text-gray-800">📋 Command Bantu</span>
                        <div class="text-sm space-y-2 font-mono">
                            <p class="bg-white p-2 border rounded"><strong>SALDO</strong><br><span class="text-xs text-gray-500 font-sans">Melihat daftar saldo semua dompet aktif & total aset.</span></p>
                            <p class="bg-white p-2 border rounded"><strong>REKAP</strong><br><span class="text-xs text-gray-500 font-sans">Melihat total pemasukan, pengeluaran & cashflow bulan ini.</span></p>
                            <p class="bg-white p-2 border rounded"><strong>RIWAYAT</strong><br><span class="text-xs text-gray-500 font-sans">Melihat 5 transaksi terakhir yang tercatat.</span></p>
                            <p class="bg-white p-2 border rounded"><strong>BANTUAN</strong><br><span class="text-xs text-gray-500 font-sans">Menampilkan petunjuk format pesan lengkap.</span></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
