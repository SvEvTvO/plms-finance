<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FonnteWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('webhook')->group(function () {
    Route::get('/whatsapp', [WhatsAppWebhookController::class, 'verify']);
    Route::post('/whatsapp', [WhatsAppWebhookController::class, 'handle']);
});

// Rute Webhook Fonnte (Mendukung GET dan POST)
Route::match(['get', 'post'], '/fonnte/webhook', [FonnteWebhookController::class, 'handle']);
