<?php

use App\Http\Middleware\CheckBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


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

Route::post('transfers', 'TransferController@store')->middleware('logtransfer');
Route::get('accounts/{id}', 'AccountController@show');
Route::post('sendMoney','BankController@sendMoney')->middleware('logtransfer');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
