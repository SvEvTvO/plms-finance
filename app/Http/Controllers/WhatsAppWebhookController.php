<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verifikasi Webhook dari Meta (GET Request)
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode', $request->query('hub.mode'));
        $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        $verifyToken = env('WHATSAPP_CLOUD_WEBHOOK_VERIFY_TOKEN', 'PlmsRahasia2026');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Menerima Pesan Masuk dari Pengguna (POST Request)
     */
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info('WhatsApp Webhook Data:', $data);

        // Ambil payload pesan masuk
        $entry = $data['entry'][0]['changes'][0]['value'] ?? null;

        if ($entry && isset($entry['messages'][0])) {
            $message = $entry['messages'][0];
            $senderNumber = $message['from']; // Nomor pengirim (misal: 628123456789)
            $messageBody = $message['text']['body'] ?? ''; // Isi chat

            // TODO: Tambahkan parser transaksi keuangan di sini
            Log::info("Pesan dari {$senderNumber}: {$messageBody}");
        }

        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }
}
