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
	Route::post('checkUsernameEmail', 'AuthController@checkUsernameEmail');
	Route::post('register', 'UserController@register');
	Route::get('checkMyBalance', 'UserController@checkMyBalance');
	Route::get('getParks', 'UserController@getParks');
	Route::get('watercheck/{lat}/{long}', 'UserController@watercheck');
	
	//LOCATION


	// Route::get('getParks1', 'LocationController@getParks1');
	// Route::post('updateClues', 'LocationController@updateClues');
	// Route::get('getLocation', 'LocationController@getLocation');
	// Route::get('getClue', 'LocationController@getClue');
	Route::get('getParks1', 'HuntController@getParks1');
	Route::get('getClue', 'HuntController@getClue');
	
	
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

		/** Hunt related requests **/
		Route::get('getHuntsByDifficulty', 'HuntController@getHuntsByDifficulty');
		Route::get('getHuntDetails', 'HuntController@getHuntDetails');
		Route::post('participateInHunt', 'HuntController@participateInHunt');
		Route::post('getHuntParticipationDetails', 'HuntController@getHuntParticipationDetails');
		Route::get('getNearByHunts', 'HuntController@getNearByHunts');
		Route::post('updateClues', 'HuntController@updateClues');
		Route::post('getHuntUser', 'HuntController@getHuntUser');
		Route::get('getHuntsInProgress', 'HuntController@getHuntsInProgress');


		/** Clues related requests  **/
		Route::post('revealTheClue', 'ClueController@revealTheClue');
		Route::get('userHuntInfo', 'ClueController@userHuntInfo');
		Route::post('clueGame', 'ClueController@clueGame');
		Route::get('quitTheHunt', 'ClueController@quitTheHunt');
		Route::get('cluePause', 'ClueController@cluePause');
		Route::post('skeleton', 'ClueController@skeleton');
		Route::post('endTheClue', 'ClueController@endTheClue');

	});

});