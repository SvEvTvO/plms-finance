<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteService
{
    public static function send(string $message, ?string $target = null): bool
    {
        $token = config('services.fonnte.token', env('FONNTE_TOKEN'));
        $rawPhone = $target ?? config('services.fonnte.target_phone', env('FONNTE_TARGET_PHONE'));

        if (empty($token) || empty($rawPhone)) {
            Log::warning('Fonnte Send Skipped: Token atau nomor tujuan tidak terkonfigurasi.');
            return false;
        }

        // Sanitasi nomor telepon (hanya digit angka)
        $phone = preg_replace('/[^0-9]/', '', $rawPhone);

        try {
            // Pasang timeout ketat agar tidak membekukan (freeze) serverless function
            $response = Http::timeout(4)
                ->connectTimeout(2)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'  => $phone,
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                Log::warning("Fonnte Send Failed [{$phone}]: " . $response->body());
                return false;
            }

            Log::info("Fonnte Send Success to [{$phone}]");
            return true;
        } catch (Throwable $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }
}
