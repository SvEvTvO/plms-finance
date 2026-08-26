<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\FonnteWebhookController;




Route::get('/', function () {
    return view('welcome');
});

// Webhook WhatsApp (Wajib Publik - Di Luar Middleware Auth)
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle']);

Route::get('/api/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);
Route::post('/api/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('wallets', WalletController::class)->except(['show']);
    Route::post('wallets/{wallet}/set-default', [WalletController::class, 'setDefault'])->name('wallets.set-default');

    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::resource('transactions', TransactionController::class)->except(['show']);

    // Goals (Target Keuangan)
    Route::resource('goals', GoalController::class);

    // Savings (Tabungan)
    Route::resource('savings', SavingController::class)->only(['create', 'store', 'destroy']);

    // Reports & Export
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    //Panduan Chat Whatsapp
    Route::get('/whatsapp-guide', function () {
        return view('whatsapp-guide');
    })->name('whatsapp.guide');

    // Endpoint ini akan menerima pesan masuk (POST) dari Fonnte
    Route::post('/fonnte/webhook', [FonnteWebhookController::class, 'handle']);
});

Route::post('/api/fonnte/webhook', [FonnteWebhookController::class, 'handle']);

require __DIR__.'/auth.php';
