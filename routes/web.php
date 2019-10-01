<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Non Login User Route
Route::get('{lang}/language','LanguageController@index');
Route::get('{lang}/language/{key}','LanguageController@index');
Route::get('{lang}/{key}/transaction','LanguageController@transaction');
Route::get('{lang}/{key}/addressbook','LanguageController@addressbook');
Route::get('{lang}/{key}/addressbookfavirote','LanguageController@addressbookfavirote');


//Custom Auth Route
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
        

Route::get('/home', 'AdminController@index')->name('home');
Route::get('/userlist', 'AdminController@userlist')->name('userlist');
Route::get('/addressbooks', 'AdminController@addressbooks')->name('addressbooks');
Route::get('/transactions', 'AdminController@transactions')->name('transactions');
Route::get('/user/{id}', 'AdminController@user')->name('user');
Route::get('/disable/2fa/{id}', 'AdminController@g2fa')->name('disable2fa');
Route::get('/disable/add/{id}', 'AdminController@add')->name('disableadd');
