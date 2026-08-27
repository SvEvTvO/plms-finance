<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-user text-lg"></i>
                </div>
                <input id="name" name="name" type="text"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-teal-500 rounded-xl text-sm font-medium transition"
                       value="{{ old('name', $user->name) }}" required autofocus />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Alamat Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-mail text-lg"></i>
                </div>
                <input id="email" name="email" type="email"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-teal-500 rounded-xl text-sm font-medium transition"
                       value="{{ old('email', $user->email) }}" required />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Nomor WhatsApp -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="whatsapp_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Nomor WhatsApp (Chatbot)</label>
                <span class="text-[11px] text-teal-600 font-medium">Format: 08xxxxxxxxxx</span>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-500">
                    <i class="ti ti-brand-whatsapp text-lg"></i>
                </div>
                <input id="whatsapp_number" name="whatsapp_number" type="text"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-teal-500 rounded-xl text-sm font-medium transition"
                       value="{{ old('whatsapp_number', $user->whatsapp_number) }}" required placeholder="08xxxxxxxxxx" />
            </div>
            <p class="mt-1.5 text-xs text-slate-400">Nomor ini digunakan untuk mengenali perintah chat transaksi keuangan Anda.</p>
            <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white text-sm font-semibold rounded-xl transition shadow-sm hover:shadow">
                <i class="ti ti-device-floppy text-base"></i>
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                   class="text-xs font-semibold text-teal-600 flex items-center gap-1">
                    <i class="ti ti-check text-sm"></i> Berhasil disimpan!
                </p>
            @endif
        </div>
    </form>
</section>
