<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GiveRewardsController;
use App\Http\Controllers\Api\RequisitionsController;
use App\Http\Controllers\Api\OrientationSchedule;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/requisitions', [RequisitionsController::class, 'store']);
Route::get('/orientation-schedule', [OrientationScheduleController::class, 'index']);
Route::patch('/orientation-schedule/{id}/status', [OrientationScheduleController::class, 'updateStatus']);
Route::get('/give-rewards', [GiveRewardsController::class, 'index']);
Route::patch('/give-rewards/{id}/status', [GiveRewardsController::class, 'updateStatus']);
