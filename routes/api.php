<?php

use App\Http\Controllers\API\AgentController;
use App\Http\Controllers\API\AgentFileController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\FlowController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\NodeController;
use App\Http\Controllers\API\PluginController;
use App\Http\Controllers\API\RunController;
use App\Http\Controllers\API\ThreadController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Core API routes
    Route::apiResource('agents', AgentController::class);
    Route::apiResource('files', FileController::class);
    Route::apiResource('flows', FlowController::class);
    Route::apiResource('messages', MessageController::class);
    Route::apiResource('nodes', NodeController::class);
    Route::apiResource('plugins', PluginController::class);
    Route::apiResource('runs', RunController::class);
    Route::apiResource('threads', ThreadController::class);

    Route::apiResource('agents.files', AgentFileController::class)->shallow();

    // Add file to agent
    //    Route::post('/agents/{agent}/files', [AgentFileControllerPrev::class, 'store']);
});
