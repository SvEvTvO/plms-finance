<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <!-- Kata Sandi Saat Ini -->
        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Kata Sandi Saat Ini</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-key text-lg"></i>
                </div>
                <input id="update_password_current_password" name="current_password" type="password"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-teal-500 rounded-xl text-sm transition"
                       autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- Kata Sandi Baru -->
        <div>
            <label for="update_password_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Kata Sandi Baru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-lock text-lg"></i>
                </div>
                <input id="update_password_password" name="password" type="password"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-teal-500 rounded-xl text-sm transition"
                       autocomplete="new-password" placeholder="Minimal 8 karakter" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Konfirmasi Kata Sandi</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-lock-check text-lg"></i>
                </div>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-teal-500 rounded-xl text-sm transition"
                       autocomplete="new-password" placeholder="Ulangi kata sandi baru" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Tombol Simpan Sandi -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:bg-black text-white text-sm font-semibold rounded-xl transition shadow-sm hover:shadow">
                <i class="ti ti-shield-lock text-base"></i>
                Perbarui Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                   class="text-xs font-semibold text-teal-600 flex items-center gap-1">
                    <i class="ti ti-check text-sm"></i> Kata sandi diperbarui!
                </p>
            @endif
        </div>
    </form>
</section>
