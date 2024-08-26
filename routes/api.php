<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::get('/doc-type', [App\Http\Controllers\Api\DashboardController::class, 'getDocType']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/reset-pass', [App\Http\Controllers\Api\MailController::class, 'sent_password']);
Route::post('/confirm-pass', [App\Http\Controllers\Api\MailController::class, 'confirm_forgot']);

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/register-finish', [App\Http\Controllers\Api\AuthController::class, 'regisFinish']);

Route::post('/all-departs', [App\Http\Controllers\Api\AuthController::class, 'allDepartment']);
Route::post('/all-sectors', [App\Http\Controllers\Api\AuthController::class, 'allSector']);
Route::post('/dpart-sectors', [App\Http\Controllers\Api\AuthController::class, 'sectorByDpart']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Api\DashboardController::class, 'getDashboard']);

    Route::post('/docc', [App\Http\Controllers\Api\DoccController::class, 'getDocc']);
    Route::post('/docc-store', [App\Http\Controllers\Api\DoccController::class, 'store']);
    Route::put('/docc-edit/{id}', [App\Http\Controllers\Api\DoccController::class, 'edit']);
    Route::put('/docc-update/{id}', [App\Http\Controllers\Api\DoccController::class, 'update']);
    Route::put('/docc-view/{id}', [App\Http\Controllers\Api\DoccController::class, 'view']);
    Route::delete('/docc-del/{id}', [App\Http\Controllers\Api\DoccController::class, 'destroy']);
    Route::post('/log-docc', [App\Http\Controllers\Api\DoccController::class, 'getLogDocc']);
    Route::put('/log-docc-view/{id}', [App\Http\Controllers\Api\DoccController::class, 'getLogDoccId']);
    Route::get('/log-docc-count', [App\Http\Controllers\Api\DoccController::class, 'getCountLog']);

    Route::post('/doc-it', [App\Http\Controllers\Api\DocumentController::class, 'getDocIt']);
    Route::post('/doc-it-type', [App\Http\Controllers\Api\DocumentController::class, 'getDocType']);
    Route::post('/doc-store', [App\Http\Controllers\Api\DocumentController::class, 'store']);
    Route::put('/doc-edit/{id}', [App\Http\Controllers\Api\DocumentController::class, 'edit']);
    Route::post('/doc-update', [App\Http\Controllers\Api\DocumentController::class, 'update']);
    Route::delete('/doc-del/{id}', [App\Http\Controllers\Api\DocumentController::class, 'destroy']);
    Route::post('/log-doc', [App\Http\Controllers\Api\DocumentController::class, 'getLogDoc']);

    Route::post('/tag-store', [App\Http\Controllers\Api\DocumentController::class, 'storeTag']);
    Route::post('/tag-depart', [App\Http\Controllers\Api\DocumentController::class, 'getTagDepart']);
    Route::post('/tag-sector', [App\Http\Controllers\Api\DocumentController::class, 'getTagSector']);
    Route::post('/tag-user', [App\Http\Controllers\Api\DocumentController::class, 'getTagUser']);

    
    Route::post('/doc-type-id', [App\Http\Controllers\Api\DashboardController::class, 'getDocTypeId']);

    //message
    Route::post('/inbox', [App\Http\Controllers\Api\InboxOutboxController::class, 'getData']);
    Route::get('/inbox-count', [App\Http\Controllers\Api\InboxOutboxController::class, 'getCountData']);
    Route::post('/outbox', [App\Http\Controllers\Api\InboxOutboxController::class, 'sendData']);
    Route::post('/book-user', [App\Http\Controllers\Api\InboxOutboxController::class, 'bookDataUser']);
    Route::post('/book', [App\Http\Controllers\Api\InboxOutboxController::class, 'bookData']);
    Route::post('/message-store', [App\Http\Controllers\Api\InboxOutboxController::class, 'store']);
    Route::post('/message-del-user', [App\Http\Controllers\Api\InboxOutboxController::class, 'delDataUser']);
    Route::post('/message-del', [App\Http\Controllers\Api\InboxOutboxController::class, 'delData']);
    Route::put('/message-remove-user/{id}', [App\Http\Controllers\Api\InboxOutboxController::class, 'destroyUser']);
    Route::put('/message-remove/{id}', [App\Http\Controllers\Api\InboxOutboxController::class, 'destroy']);
    Route::put('/message-destroy/{id}', [App\Http\Controllers\Api\InboxOutboxController::class, 'destroyData']);
    Route::put('/message-view/{id}', [App\Http\Controllers\Api\InboxOutboxController::class, 'getDatafirst']);
    Route::put('/message-book-user/{id}', [App\Http\Controllers\Api\InboxOutboxController::class, 'postBookmaskUser']);
    Route::put('/message-book/{id}', [App\Http\Controllers\Api\InboxOutboxController::class, 'postBookmask']);
    Route::put('/message-download/{id}', [App\Http\Controllers\Api\InboxOutboxController::class, 'downloadMessage']);

    //settings
    Route::post('/group', [App\Http\Controllers\Api\Settings\GroupController::class, 'getData']);
    Route::post('/group-store', [App\Http\Controllers\Api\Settings\GroupController::class, 'store']);
    Route::put('/group-edit/{id}', [App\Http\Controllers\Api\Settings\GroupController::class, 'edit']);
    Route::post('/group-update', [App\Http\Controllers\Api\Settings\GroupController::class, 'update']);
    Route::delete('/group-del/{id}', [App\Http\Controllers\Api\Settings\GroupController::class, 'destroy']);

    Route::post('/doc-group', [App\Http\Controllers\Api\Settings\DocGroupController::class, 'getData']);
    Route::post('/doc-dpart', [App\Http\Controllers\Api\Settings\DocDpartController::class, 'getData']);
    Route::post('/doc-dpartment', [App\Http\Controllers\Api\Settings\DocDpartmentController::class, 'getData']);
    Route::post('/doc-sheft', [App\Http\Controllers\Api\Settings\DocSheftController::class, 'getData']);
    Route::post('/doc-dock', [App\Http\Controllers\Api\Settings\DocDockController::class, 'getData']);
    
    Route::post('/all-user', [App\Http\Controllers\Api\AuthController::class, 'allUser']);
    Route::post('/all-depart', [App\Http\Controllers\Api\AuthController::class, 'allDepartment']);
    Route::post('/all-sector', [App\Http\Controllers\Api\AuthController::class, 'allSector']);
    Route::post('/dpart-sector', [App\Http\Controllers\Api\AuthController::class, 'sectorByDpart']);
    Route::post('/dpart-user', [App\Http\Controllers\Api\AuthController::class, 'getDpartUser']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
});