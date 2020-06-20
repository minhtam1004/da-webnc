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
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('register', 'AuthController@register');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');

});
Route::post('transfers', 'TransferController@store')->middleware('checkbank','logtransfer');
Route::get('accounts/{id}', 'AccountController@show')->middleware('checkuser');
Route::post('sendMoney','BankController@sendMoney')->middleware('logtransfer');
Route::get('viewuser','BankController@viewuser');
