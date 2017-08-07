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
Route::get('pustahas/search-research', 'PustahaController@searchResearch');

Route::get('approvals', 'ApprovalController@index');
Route::get('approvals/detail', 'ApprovalController@showApproval');
Route::post('approvals/create', 'ApprovalController@store');
Route::get('approvals/ajax', 'ApprovalController@getAjax');
Route::get('approvals/download-document', 'ApprovalController@downloadDocument');

Route::get('users/', 'UserController@index');
Route::get('users/create', 'UserController@create');
Route::post('users/create', 'UserController@store');
Route::post('users/delete', 'UserController@destroy');
Route::get('users/ajax', 'UserController@getAjax');
Route::delete('users/delete', 'UserController@destroy');
Route::get('users/edit', 'UserController@edit');
Route::put('users/edit', 'UserController@update');
Route::get('users/ajax/search', 'UserController@searchUser');

Route::get('callback.php', 'CallbackController@callback');
