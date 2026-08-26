<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Kirim notifikasi WhatsApp via Fonnte
     */
    public static function send(string $message, ?string $target = null): bool
    {
        // Menggunakan token dari .env atau fallback ke token akun Fonnte Anda
        $token = env('FONNTE_TOKEN') ?: 'n4R5D9UjUyjBxJw9m3Uv';
        $phone = $target ?? env('FONNTE_TARGET_PHONE');

        if (!$phone) {
            Log::error('Fonnte Error: Nomor target pengiriman tidak ditemukan.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            Log::info("Fonnte Send Response to [{$phone}]: " . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }
}
