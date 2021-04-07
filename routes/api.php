<?php

use Asseco\Attachments\App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('api')
    ->middleware('api')
    ->group(function () {

        Route::apiResource('attachments', AttachmentController::class)->except(['update']);

        Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download']);
    });

