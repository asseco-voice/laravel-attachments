<?php

use Asseco\Attachments\App\Http\Controllers\AttachmentController;
use Asseco\Attachments\App\Http\Controllers\FilingPurposeController;
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

Route::prefix(config('asseco-attachments.routes.prefix'))
    ->middleware(config('asseco-attachments.routes.middleware'))
    ->group(function () {
        Route::delete('attachments/bulk-delete', [AttachmentController::class, 'bulkDelete'])->name('attachments.bulkDelete');

        Route::delete('attachments/bulk-soft-delete', [AttachmentController::class, 'bulkSoftDelete'])->name('attachments.bulkSoftDelete');

        Route::apiResource('attachments', AttachmentController::class);

        Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
        Route::post('attachments/bulk-download', [AttachmentController::class, 'bulkDownload'])->name('attachments.bulkDownload');

        Route::apiResource('filing-purpose', FilingPurposeController::class);
    });
