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
Route::group([
    'prefix' => 'remembers'
], function ($router) {
    Route::post('add', 'RememberController@store');
    Route::post('search', 'RememberController@show');
});
Route::group([
    'middleware' => 'auth',
    'prefix' => 'debt'
], function ($router) {
    Route::post('add', 'DebtController@store');
    Route::get('/', 'DebtController@index');
    Route::get('/{id}', 'DebtController@show');
    Route::post('/{id}/paid','DebtController@paid');
    Route::delete('/{id}', 'DebtController@destroy');
});
Route::get('bank/accounts/{id}','AccountController@show')->middleware('auth');
Route::get('banks','BankController@index')->middleware('auth');
Route::post('transfers', 'TransferController@store')->middleware('checkbank','logtransfer');
Route::post('bank/transfers', 'TransferController@store')->middleware('auth');
Route::post('confirm/transfers', 'TransferController@confirm')->middleware('auth','logtransfer');
Route::post('refresh/transfers/{id}', 'TransferController@getOTP')->middleware('auth');
Route::get('accounts/{id}', 'AccountController@show')->middleware('checkuser');
Route::post('sendMoney','BankController@sendMoney')->middleware('logtransfer');
Route::get('viewuser','BankController@viewuser');
