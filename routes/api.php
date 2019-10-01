<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//Common API
Route::get('resendcode/{key}','ApiController@codesend');
Route::get('varifycode/{code}/{key}/{validator}','ApiController@verifycode');

//Login Process
Route::get('login/{email}/{password}','ApiController@login');
Route::get('otpconfirm/{key}/{code}','ApiController@otpconfirm');

//Reset Password Process
Route::get('resetpassword/{email}','ApiController@resetpassword');

//Sign Up Process
Route::get('registration/{email}/{pass}','ApiController@registration');

//Login User Action
Route::get('user/{key}','ApiController@user');

Route::get('send/{key}/{address}/{amount}','ApiController@send');
Route::get('addresscreate/{key}/{name}/{address}/{fav}','ApiController@addresscreate');
Route::get('addressedit/{key}/{name}/{address}/{fav}/{id}','ApiController@addressedit');
Route::get('acdelete/{key}','ApiController@acdelete');
Route::get('passchange/{key}/{old}/{new}/{cnew}','ApiController@passchange');
Route::get('adddelete/{key}/{id}','ApiController@adddelete');
Route::get('otp/{key}/{code}/{type}','ApiController@otp');





