<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/reset-pass', [App\Http\Controllers\Api\MailController::class, 'sent_password']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/doc-it', [App\Http\Controllers\Api\DocumentController::class, 'getDocIt']);
    

    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
});