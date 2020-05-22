<?php

use App\Http\Middleware\CheckBank;
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

Route::post('transfers', 'TransferController@store')->middleware('checkbank','logtransfer');
Route::get('users/{user}', 'UserController@show');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
