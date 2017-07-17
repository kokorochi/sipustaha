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

Route::get('/', 'PustahaController@index');
Route::get('pustahas', 'PustahaController@index');
Route::get('pustahas/create', 'PustahaController@create');
Route::post('pustahas/create', 'PustahaController@store');
Route::get('pustahas/display', 'PustahaController@display');
Route::get('pustahas/edit', 'PustahaController@edit');
Route::put('pustahas/edit', 'PustahaController@update');
Route::delete('pustahas/delete', 'PustahaController@destroy');
Route::get('pustahas/download-document', 'PustahaController@downloadDocument');
Route::get('pustahas/ajax', 'PustahaController@getAjax');

Route::get('approvals', 'ApprovalController@index');
Route::get('approvals/create', 'ApprovalController@showApproval');

Route::get('users/ajax/search', 'UserController@searchUser');

Route::get('callback.php', 'CallbackController@callback');
