<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public static function send(string $message, ?string $target = null): bool
    {
        $token = env('FONNTE_TOKEN') ?: 'n4R5D9UjUyjBxJw9m3Uv';
        $phone = $target ?? env('FONNTE_TARGET_PHONE');

        if (!$phone) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);

            Log::info("Fonnte Send Response to [{$phone}]: " . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }
}
