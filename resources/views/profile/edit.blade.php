<x-app-layout>
    <div class="py-6 space-y-6">
        <!-- Header Profil -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-teal-600 text-white font-bold text-2xl flex items-center justify-center shadow-md shadow-teal-600/20">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">{{ Auth::user()->name }}</h1>
                    <p class="text-sm text-slate-500">{{ Auth::user()->email }} • <span class="text-teal-600 font-medium">{{ Auth::user()->whatsapp_number ?? 'Belum ada nomor WA' }}</span></p>
                </div>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 text-xs font-semibold border border-teal-100 w-fit">
                <i class="ti ti-shield-check text-base"></i>
                Akun Terverifikasi
            </div>
        </div>

        <!-- Grid Form: Informasi Profil & Keamanan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kolom 1: Informasi Akun & WhatsApp -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm h-fit">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="p-2 bg-teal-50 text-teal-600 rounded-xl">
                        <i class="ti ti-user-edit text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Informasi Pribadi</h2>
                        <p class="text-xs text-slate-500">Kelola identitas, email, dan integrasi WhatsApp bot.</p>
                    </div>
                </div>

                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Kolom 2: Keamanan Kata Sandi -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm h-fit">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                        <i class="ti ti-lock text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Keamanan & Sandi</h2>
                        <p class="text-xs text-slate-500">Perbarui kata sandi secara berkala untuk menjaga akun tetap aman.</p>
                    </div>
                </div>

                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Zona Hapus Akun -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-rose-100 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                    <i class="ti ti-alert-triangle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Hapus Akun</h2>
                    <p class="text-xs text-slate-500">Tindakan ini permanen dan akan menghapus seluruh data transaksi Anda.</p>
                </div>
            </div>

            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
