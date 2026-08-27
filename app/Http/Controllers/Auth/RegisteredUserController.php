<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Services\FonnteService;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'whatsapp_number' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ], [
            'whatsapp_number.regex' => 'Format nomor WhatsApp harus diawali dengan 08.',
            'whatsapp_number.unique' => 'Nomor WhatsApp ini sudah terdaftar di sistem kami.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // ==========================================
        // FITUR BARU: AUTO-SEND WELCOME MESSAGE
        // ==========================================
        $welcomeMsg = "🎉 *Halo {$user->name}, Selamat Datang di PLMS Finance!*\n\n"
                    . "Terima kasih telah mendaftar. Akun Anda berhasil dibuat dan nomor WhatsApp ini sudah otomatis terhubung dengan sistem kami.\n\n"
                    . "Mulai sekarang, Anda bisa mencatat pengeluaran semudah membalas chat. Coba ketik *BANTUAN* di chat ini untuk melihat panduannya.\n\n"
                    . "Selamat mengelola keuangan dengan lebih cerdas! 🚀\n"
                    . "_PLMS Finance Management_";

        // Kirim pesan secara asinkron (tidak memblokir loading registrasi)
        FonnteService::send($welcomeMsg, $user->whatsapp_number);
        // ==========================================

        return redirect(route('dashboard', absolute: false));
    }
}
