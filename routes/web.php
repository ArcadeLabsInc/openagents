<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BitcoinController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\StaticController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StaticController::class, 'splash'])->name('home');

// Disable all these routes in production
if (!app()->environment('production')) {
    // Plugin uploading
    Route::get('/plugins', [PluginController::class, 'index'])->name('plugins');
    Route::get('/plugin/{plugin}', [PluginController::class, 'show'])->name('plugins.show');
    Route::get('/plugins/create', [PluginController::class, 'create'])->name('plugins.create');
    Route::post('/plugins', [PluginController::class, 'store'])->name('plugins.store');
    Route::post('/plugins/call', [PluginController::class, 'call'])->name('plugins.call');

    // Agents
    Route::get('/agent/connie', [AgentController::class, 'coder'])->name('agent.coder');
    Route::get('/agent/{id}', [AgentController::class, 'show'])->name('agent');
    Route::post('/agent/{id}/run', [AgentController::class, 'run_task'])->name('agent.run_task');

    // Auth
    Route::get('/login', [AuthController::class, 'login']);
    Route::get('/login/github', [AuthController::class, 'loginGithub']);
    Route::get('/github', [AuthController::class, 'githubCallback']);
    Route::get('/login/twitter', [AuthController::class, 'loginTwitter']);
    Route::get('/twitter', [AuthController::class, 'twitterCallback']);

    // Authed routes
    Route::middleware(['auth'])->group(function () {
        // Withdrawals
        Route::get('/withdraw', [BitcoinController::class, 'withdraw'])->name('withdraw');
        Route::post('/withdraw', [BitcoinController::class, 'initiate_withdrawal'])->name('withdraw.initiate');
    });
}

require __DIR__.'/auth.php';

// Add a catch-all redirect to the homepage
Route::get('/{any}', function () {
    return redirect('/');
})->where('any', '.*');
