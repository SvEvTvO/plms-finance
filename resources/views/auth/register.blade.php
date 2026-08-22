<x-guest-layout>
    <!-- Header Text -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900">Buat Akun Baru</h2>
        <p class="text-sm text-slate-500 mt-1">Mulai kelola keuangan Anda hari ini secara gratis.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-user text-lg"></i>
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="pl-10 w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 text-slate-900 bg-slate-50 py-2.5 transition-colors" placeholder="Fulan bin Fulan">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-mail text-lg"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="pl-10 w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 text-slate-900 bg-slate-50 py-2.5 transition-colors" placeholder="nama@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-lock text-lg"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="pl-10 w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 text-slate-900 bg-slate-50 py-2.5 transition-colors" placeholder="Minimal 8 karakter">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Sandi</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-shield-check text-lg"></i>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="pl-10 w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 text-slate-900 bg-slate-50 py-2.5 transition-colors" placeholder="Ulangi kata sandi">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-teal-600/30 text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors">
                Buat Akun Gratis <i class="ti ti-user-plus ml-2 text-lg"></i>
            </button>
        </div>

        <!-- Link ke Login -->
        <div class="text-center mt-4">
            <p class="text-sm text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline transition-colors">Masuk di sini</a>
            </p>
        </div>
    </form>
</x-guest-layout>
