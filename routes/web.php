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

Route::group(['prefix'=> 'admin', 'namespace'=>'Admin\Auth','as'=>'admin.'],function(){

	Route::get('login', 'LoginController@showLoginForm')->name('login');
	Route::post('login', 'LoginController@login');
	Route::post('logout', 'LoginController@logout')->name('logout');

	//password reset routes
	Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
	Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('password/reset', 'ResetPasswordController@reset');
	Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
});

//Route::group(['prefix'=> 'admin', 'namespace'=>'Admin','as'=>'admin.','middleware'=>'auth:admin'],function(){
Route::group(['prefix'=> 'admin','middleware'=>'auth:admin', 'namespace'=>'Admin', 'as'=>'admin.'],function(){

	//dashboards
	Route::get('/', 'AdminController@index')->name('dashboards');
	
	//User List
	Route::get('/userList', 'UserController@index')->name('userList');
	Route::get('/getUsers', 'UserController@getUsers')->name('getUsers');

	//News
	Route::resource('news', 'NewsController');
	Route::get('getNewsList', 'NewsController@getNewsList')->name('getNewsList');

	//MAPS
	Route::get('/mapsList', 'MapsController@index')->name('mapsList');
	Route::get('/getMaps', 'MapsController@getMaps')->name('getMaps');
	
});

Auth::routes();