<?php

use Asseco\CustomFields\App\Http\Controllers\CustomFieldController;

use Asseco\CustomFields\App\Http\Controllers\ValueController;
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


Route::prefix('api')->middleware('api')->group(function () {
    Route::apiResource('custom-fields', CustomFieldController::class);

    Route::prefix('custom-field')->name('custom-field.')->group(function () {
        Route::get('types', [TypeController::class, 'index'])->name('types.index');
        Route::get('models', [ModelController::class, 'index'])->name('models.index');

        Route::get('plain/{plain_type?}', [PlainCustomFieldController::class, 'index'])->name('plain.index');
        Route::post('plain/{plain_type}', [PlainCustomFieldController::class, 'store'])->name('plain.store');

        Route::apiResource('remote', RemoteCustomFieldController::class)->only(['index', 'store']);
        Route::get('remote-values/{remote_type}', [RemoteValuesController::class, 'show'])->name('remote-values.show');

        Route::get('selection', [SelectionCustomFieldController::class, 'index'])->name('selection.index');
        Route::get('selection/{selection}', [SelectionCustomFieldController::class, 'show'])->name('selection.show');
        Route::post('selection/{plain_type}', [SelectionCustomFieldController::class, 'store'])->name('selection.store');

        Route::apiResource('selection-values', SelectionValueController::class);

        Route::apiResource('validations', ValidationController::class);
        Route::apiResource('relations', RelationController::class);
        Route::apiResource('values', ValueController::class);

        Route::post('forms/{form_name}/validate', [FormController::class, 'validateAgainstCustomInput'])->name('forms.validate');
        Route::apiResource('forms', FormController::class);
    });
});
