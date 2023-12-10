<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InspectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueryController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return Inertia::render('Splash');
});

Route::get('/login/github', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/github', function () {
    $githubUser = Socialite::driver('github')->user();

    $user = User::updateOrCreate(
        ['github_id' => $githubUser->id], // Check if GitHub ID exists
        [
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'github_nickname' => $githubUser->nickname,
            'github_avatar' => $githubUser->avatar,
            // Add other fields as needed
        ]
    );

    // Perform any post-login operations with $user
    dd($user);
});

if (env('APP_ENV') !== "production") {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth']);

    Route::get('/login', function () {
        return Inertia::render('Login');
    })->name('login');

    Route::get('/inspect', [InspectController::class, 'index'])->name('inspect');
    Route::get('/run/{id}', [InspectController::class, 'showRun'])->name('inspect-run');
    Route::get('/task/{id}', [InspectController::class, 'showTask'])->name('inspect-task');
    Route::get('/step/{id}', [InspectController::class, 'showStep'])->name('inspect-step');

    Route::post('/api/agents', [AgentController::class, 'store'])
      ->middleware(['auth']);

    Route::post('/api/conversations', [ConversationController::class, 'store'])
      ->middleware(['auth'])
      ->name('conversations.store');

    Route::post('/api/messages', [MessageController::class, 'store'])
      ->middleware(['auth'])
      ->name('messages.store');

    Route::post('/api/files', [FileController::class, 'store'])
      ->name('files.store');

    Route::post('/api/query', [QueryController::class, 'store'])
      ->name('query.store');
}

// Add a catch-all redirect to the homepage
Route::get('/{any}', function () {
  return redirect('/');
})->where('any', '.*');
