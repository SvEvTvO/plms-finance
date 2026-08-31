<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FonnteWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute Webhook Fonnte
Route::match(['get', 'post'], '/fonnte/webhook', [FonnteWebhookController::class, 'handle']);
