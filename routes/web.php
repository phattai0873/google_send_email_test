<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\EmailCampaignController;

Route::get('/', [EmailCampaignController::class, 'index'])->name('home');

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/send-email', fn() => redirect()->route('home'));
    Route::post('/send-email', [EmailCampaignController::class, 'send'])->name('email.send');
    Route::get('/logs', [EmailCampaignController::class, 'logs'])->name('email.logs');
    Route::get('/email-logs/status-updates', [EmailCampaignController::class, 'getStatusUpdates'])->name('email.logs.updates');
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
});
