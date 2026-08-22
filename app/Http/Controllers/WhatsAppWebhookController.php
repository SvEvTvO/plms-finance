<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers\Api;

use App\Services\WABotService;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    protected WABotService $wa;

    public function __construct(WABotService $wa)
    {
        $this->wa = $wa;
    }

    public function verify(Request $request)
    {
        if ($request->get('hub_verify_token') === config('services.whatsapp.verify_token')) {
            return response($request->get('hub_challenge'));
        }
        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        $this->wa->processMessage($payload);
        return response()->json(['status' => 'ok']);
    }
}
