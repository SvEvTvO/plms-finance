<section class="space-y-4">
    <p class="text-sm text-slate-600 max-w-xl">
        Setelah akun Anda dihapus, semua riwayat transaksi, target keuangan, dan saldo dompet akan dihapus secara permanen. Unduh salinan laporan transaksi Anda terlebih dahulu jika diperlukan.
    </p>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-sm font-semibold transition"
    >
        <i class="ti ti-trash text-base"></i>
        {{ __('Hapus Akun Saya') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-800">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                Masukkan kata sandi Anda untuk mengonfirmasi penghapusan permanen akun ini.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Kata Sandi') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 rounded-xl border-slate-200"
                    placeholder="{{ __('Masukkan kata sandi') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="rounded-xl bg-rose-600 hover:bg-rose-700">
                    {{ __('Ya, Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
