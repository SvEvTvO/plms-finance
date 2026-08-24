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
        $token = env('FONNTE_TOKEN');
        $phone = $target ?? env('FONNTE_TARGET_PHONE');

        if (!$token || !$phone) {
            Log::error('Fonnte: FONNTE_TOKEN atau FONNTE_TARGET_PHONE belum diatur di .env');
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

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Fonnte Error: ' . $e->getMessage());
            return false;
        }
    }
}
