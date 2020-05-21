<?php

use Illuminate\Http\Request;
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

Route::apiResource('transfer', 'Api\TransferController')->only(['store'])->middleware('CheckBank');
Route::apiResource('user', 'Api\UserController')->only(['show'])->middleware('CheckBank');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
