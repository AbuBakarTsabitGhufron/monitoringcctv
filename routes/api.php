<?php

use App\Http\Controllers\Api\ApiLokasiController;
use App\Http\Controllers\Api\ApiPanoramaController;
use App\Http\Controllers\Api\ApiUsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LokasiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('cctvlokasi', ApiLokasiController::class);

Route::apiResource('cctvpanorama', ApiPanoramaController::class);

Route::apiResource('users', ApiUsersController::class);

Route::post('/cctvlokasi/{id}/toggle', [LokasiController::class, 'toggle']);

Route::post('/cctvlokasi/bulk-toggle', [LokasiController::class, 'bulkToggle']);

