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
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('register', 'AuthController@register');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('changePassword', 'AuthController@changePassword');
    Route::post('resetPassword', 'AuthController@changePasswordWithMail');
    Route::get('resetPassword', 'AuthController@resetPassword');
    Route::get('me', 'AuthController@me');
});
Route::group([
    'middleware' => 'auth',
    'prefix' => 'remembers'
], function ($router) {
    Route::post('', 'RememberController@store');
    Route::post('search', 'RememberController@show');
    Route::put('/{id}', 'RememberController@update');
    Route::delete('/{id}', 'RememberController@destroy');
});
Route::group([
    'middleware' => 'auth',
    'prefix' => 'debt'
], function ($router) {
    Route::post('/add', 'DebtController@store');
    Route::get('/', 'DebtController@index');
    Route::get('/other/', 'DebtController@otherindex');
    Route::get('/{id}', 'DebtController@show');
    Route::post('/{id}/paid', 'DebtController@paid');
    Route::delete('/{id}', 'DebtController@destroy');
});
Route::group([
    'middleware' => 'auth',
    'prefix' => 'bank'
], function ($router) {
    Route::get('/accounts/{id}', 'AccountController@show');
    Route::post('/accounts/{id}/recharge', 'AccountController@recharge');
    Route::get('/users/{id}', 'UserController@show');
    Route::get('/users/{id}/transfers', 'UserController@showTransfer');
    Route::get('/users/{id}/debt-transfers', 'UserController@showDebtTransfer');
    Route::get('/users/{id}/recharge-transfers', 'UserController@showRechargeTransfer');
    Route::get('/users/{id}/accounts', 'UserController@showAccount');
    Route::post('/users/{id}/accounts', 'AccountController@store');
    Route::get('/users/me/notify', 'UserController@showNotify');
    Route::post('/users/me/notify/{id}', 'UserController@readNotify');
    Route::post('/users/me/notify', 'UserController@readAllNotify');
    Route::get('/customers', 'UserController@customerIndex');
    Route::get('/employees', 'UserController@employeeIndex');
    Route::post('/employees', 'UserController@employeeStore');
    Route::put('/employees/{id}', 'UserController@employeeUpdate');
    Route::delete('/employees/{id}', 'UserController@employeeDestroy');
    Route::post('/transfers', 'TransferController@store');
    Route::post('/transfers/{id}/confirm', 'TransferController@confirm')->middleware('logtransfer');
    Route::get('/transfers/{id}/refresh', 'TransferController@getOTP');
});
Route::group([
    'middleware' => 'auth',
    'prefix' => 'banks'
], function ($router) {
    Route::get('', 'BankController@index');
    Route::get('/{id}/transfers', 'BankController@bankTransfer');
    Route::get('/transfers', 'BankController@transfer');
});
Route::post('/transfers', 'TransferController@store')->middleware('checkbank', 'logtransfer');
Route::get('accounts/{id}', 'AccountController@show')->middleware('checkuser');
Route::post('sendMoney', 'BankController@sendMoney')->middleware('auth');
Route::get('viewuser', 'BankController@viewuser')->middleware('auth');
Route::post('confirm', 'BankController@send')->middleware('auth','logtransfer');
