<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/audit/callback', \App\Http\Controllers\Api\AuditCallbackController::class)->middleware('throttle:60,1');
Route::post('/bot/query', [\App\Http\Controllers\Api\BotQueryController::class, 'query'])->middleware('throttle:60,1');

Route::get('/version', function () {
    return response()->json([
        'version' => config('dx.version')
    ]);
});
