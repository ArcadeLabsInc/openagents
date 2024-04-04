<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\StaticController;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Auth\VerifyAccount;
use App\Livewire\Chat;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// CHAT
Route::get('/', Chat::class)->name('chat.intro');
Route::get('/chat/{id}', Chat::class)->name('chat');

// AUTH
Route::get('/reset/account/{token}', ChangePassword::class)->name('password.reset');
Route::get('/email/verify//{id}/{hash}', VerifyAccount::class)->name('verification.verify');

// BILLING
Route::get('/billing', [BillingController::class, 'stripe_billing_portal'])->middleware(['auth-old']);

// STATIC
Route::get('/pro', [StaticController::class, 'pro']);
Route::get('/launch', [StaticController::class, 'launch']);
Route::get('/docs', [StaticController::class, 'docs']);

// Add GET logout route
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy']);

// Catch-all redirect to the homepage
Route::get('/{any}', function () {
    return redirect('/');
})->where('any', '.*');
