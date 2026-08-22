<x-guest-layout>
    <!-- Header Text -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900">Selamat Datang Kembali</h2>
        <p class="text-sm text-slate-500 mt-1">Silakan masuk ke akun Anda untuk melanjutkan.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-mail text-lg"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="pl-10 w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 text-slate-900 bg-slate-50 py-2.5 transition-colors" placeholder="nama@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-sm font-semibold text-slate-700">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-teal-600 hover:text-teal-700 hover:underline" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-lock text-lg"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="pl-10 w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 text-slate-900 bg-slate-50 py-2.5 transition-colors" placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 h-4 w-4 cursor-pointer">
            <label for="remember_me" class="ml-2 block text-sm text-slate-600 cursor-pointer">
                Ingat saya di perangkat ini
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-teal-600/30 text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors">
                Masuk ke Dasbor <i class="ti ti-arrow-right ml-2 text-lg"></i>
            </button>
        </div>

        <!-- Link ke Register -->
        <div class="text-center mt-4">
            <p class="text-sm text-slate-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline transition-colors">Daftar sekarang</a>
            </p>
        </div>
    </form>
</x-guest-layout>
