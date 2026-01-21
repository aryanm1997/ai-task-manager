<?php

use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Public
Route::get('/health', fn () => response()->json(['status' => 'ok']));
Route::post('/login', [TaskApiController::class, 'login']);

// Protected (Sanctum)
Route::middleware('auth:sanctum')->group(function () {


    Route::get('/user', fn (Request $request) => $request->user());

    Route::get('/tasks', [TaskApiController::class, 'index']);
    Route::post('/tasks', [TaskApiController::class, 'store']);
    Route::get('/tasks/{task}', [TaskApiController::class, 'show']);
    Route::patch('/tasks/{task}/status', [TaskApiController::class, 'updateStatus']);
    Route::get('/tasks/{task}/ai-summary', [TaskApiController::class, 'getAiSummary']);
    Route::delete('/tasks/{task}', [TaskApiController::class, 'destroy']);
});
