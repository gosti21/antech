<?php

use App\Http\Controllers\Api\v1\Ia\RecomendationIaController;
use Illuminate\Support\Facades\Route;

Route::post('recommend', [RecomendationIaController::class, 'recommend'])->name('recomendation.recommend');
Route::post('sync-catalog', [RecomendationIaController::class, 'syncCatalog'])
    ->middleware('auth:sanctum')
    ->name('recomendation.syncCatalog');
