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
    Route::post('/{id}/paid', 'DebtController@paid');
    Route::delete('/{id}', 'DebtController@destroy');
});
Route::group([
    'middleware' => 'auth',
    'prefix' => 'bank'
], function ($router) {
    Route::get('/accounts/{id}', 'AccountController@show');
    Route::get('/customers/{id}', 'UserController@show');
    Route::get('/customers', 'UserController@customerIndex');
    Route::get('/employee', 'UserController@employeeIndex');
    Route::post('/transfers', 'TransferController@store');
});
Route::group([
    'middleware' => 'auth',
    'prefix' => 'transfers'
], function ($router) {
    Route::post('', 'TransferController@store')->middleware('checkbank', 'logtransfer');
    Route::post('/confirm', 'TransferController@confirm')->middleware('logtransfer');
    Route::get('/{id}/refresh', 'TransferController@getOTP');
});
Route::get('banks', 'BankController@index')->middleware('auth');
Route::get('accounts/{id}', 'AccountController@show')->middleware('checkuser');
Route::post('sendMoney', 'BankController@sendMoney');
Route::get('viewuser', 'BankController@viewuser');
Route::post('confirm', 'BankController@send')->middleware('logtransfer');
