<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GiveRewardsController;
use App\Http\Controllers\Api\RequisitionsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/requisitions', [RequisitionsController::class, 'store']);


Route::get('/give-rewards', [GiveRewardsController::class, 'index']);

