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

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1'], function ($router) {

	Route::post('login', 'AuthController@login');
	Route::post('register', 'UserController@register');
	Route::get('checkMyBalance', 'UserController@checkMyBalance');
	
	Route::group(['middleware' => 'jwt-auth'], function ($router) {

		Route::post('changePassword', 'ProfileController@changePassword');
		Route::post('updateProfile', 'ProfileController@updateProfile');
		Route::post('updateSetting', 'ProfileController@updateSetting');


		Route::post('getPayloadData', 'UserController@getPayloadData');
		Route::post('setMyAvatar', 'UserController@setMyAvatar');
		Route::get('getWarehouseData', 'UserController@getWarehouseData');
		
		/** Plans related requets **/
		Route::get('getThePlans', 'PlanController@getThePlans');
		Route::post('purchaseTheGolds', 'PlanController@purchaseTheGolds');
		
		/** Events related requests **/
		Route::post('addParticipation', 'EventController@addParticipation');
		Route::get('getTheEvents', 'EventController@getTheEvents');
		Route::get('getEventDetails', 'EventController@getEventDetails');
		Route::post('hitAnEventAction', 'EventController@hitAnEventAction');
		
		/** News related requests **/
		Route::resource('news', 'NewsController');

		Route::post('logout', 'AuthController@logout');
		Route::post('refresh', 'AuthController@refresh');
		Route::post('me', 'AuthController@me');

	});

});