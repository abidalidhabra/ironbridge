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

Route::post('addComplexityWiseTarget', 'Api\v1\PrepareController@addComplexityWiseTarget');
Route::post('addGames', 'Api\v1\PrepareController@addGames');
Route::post('addWidgets', 'Api\v1\PrepareController@addWidgets');
Route::get('addRolesAndPermissions', 'Api\v2\AddRolesAndPermission@addRolesAndPermissions');

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1'], function ($router) {

	Route::post('login', 'AuthController@login');
	Route::post('checkUsernameEmail', 'AuthController@checkUsernameEmail');
	Route::post('register', 'UserController@register');
	// Route::get('checkMyBalance', 'UserController@checkMyBalance');
	Route::get('getParks', 'UserController@getParks');
	Route::get('watercheck/{lat}/{long}', 'UserController@watercheck');
	
	//LOCATION


	// Route::get('getParks1', 'LocationController@getParks1');
	// Route::post('updateClues', 'LocationController@updateClues');
	// Route::get('getLocation', 'LocationController@getLocation');
	// Route::get('getClue', 'LocationController@getClue');
	Route::get('getParks1', 'HuntController@getParks1');
	Route::get('getClue', 'HuntController@getClue');
	Route::post('updateClues', 'HuntController@updateClues');
	
	/* password reset */
	/** registerd by email **/
	Route::post('/forgotPassword', 'PasswordResetController@forgotPassword');
	Route::post('/matchOtp', 'PasswordResetController@matchOtp');
	Route::post('/resetpasswordByEmail', 'PasswordResetController@resetpasswordByEmail');
	
	Route::group(['middleware' => 'jwt-auth'], function ($router) {


		Route::post('changePassword', 'ProfileController@changePassword');
		Route::post('updateProfile', 'ProfileController@updateProfile');
		Route::post('updateSetting', 'ProfileController@updateSetting');


		Route::post('getPayloadData', 'UserController@getPayloadData');
		Route::post('unlockWidgetItem', 'UserController@unlockWidgetItem');
		// Route::post('selectkWidgetItem', 'UserController@selectkWidgetItem');
		Route::post('setMyAvatar', 'UserController@setMyAvatar');
		// Route::get('getWarehouseData', 'UserController@getWarehouseData');
		
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
		Route::post('getHuntUser', 'HuntController@getHuntUser');
		Route::get('getHuntsInProgress', 'HuntController@getHuntsInProgress');
		Route::get('getPreviousHuntDetails', 'HuntController@getPreviousHuntDetails');

		// Route::get('getHuntsByDifficultyV2', 'HuntController@getHuntsByDifficultyV2');
		// Route::post('participateInHuntV2', 'HuntController@participateInHuntV2');
		// Route::get('getHuntParticipationDetailsV2', 'HuntController@getHuntParticipationDetailsV2');
		// Route::get('getNearByHuntsV2', 'HuntController@getNearByHuntsV2');
		// Route::get('getHuntDetailsV2', 'HuntController@getHuntDetailsV2');
		// Route::get('getHuntsInProgressV2', 'HuntController@getHuntsInProgressV2');

		/** Clues related requests  **/
		Route::post('revealTheClueV2', 'ClueController@revealTheClueV2');
		Route::post('startTheClueV2', 'ClueController@startTheClueV2');
		Route::post('pauseTheClueV2', 'ClueController@pauseTheClueV2');
		Route::post('endTheClueV2', 'ClueController@endTheClueV2');
		Route::post('useTheSkeletonKeyV2', 'ClueController@useTheSkeletonKeyV2');

		Route::post('revealTheClue', 'ClueController@revealTheClue');
		Route::get('quitTheHunt', 'ClueController@quitTheHunt');
		Route::get('cluePause', 'ClueController@cluePause');
		Route::post('skeleton', 'ClueController@skeleton');
		Route::post('startTheClue', 'ClueController@startTheClue');
		Route::post('endTheClue', 'ClueController@endTheClue');
		Route::post('clueGame', 'ClueController@clueGame');
		Route::get('userHuntInfo', 'ClueController@userHuntInfo');

	});

});




Route::group(['namespace' => 'Api\v2', 'prefix' => 'v2'], function ($router) {
	
	Route::group(['middleware' => 'jwt-auth'], function ($router) {

		/** Hunt related requests **/
		Route::get('getHuntsByDifficulty', 'HuntController@getHuntsByDifficulty');
		Route::get('getNearByHunts', 'HuntController@getNearByHunts');
		Route::get('getHuntDetails', 'HuntController@getHuntDetails');
		Route::post('participateInHunt', 'HuntController@participateInHunt');
		Route::get('getHuntParticipationDetails', 'HuntController@getHuntParticipationDetails');
		Route::get('getHuntsInProgress', 'HuntController@getHuntsInProgress');
		Route::post('pauseTheHunt', 'HuntController@pauseTheHunt');
		Route::post('quitTheHunt', 'HuntController@quitTheHunt');

		/** Clues related requests  **/
		Route::post('actionOnClue', 'ClueController@actionOnClue');
		Route::post('useTheSkeletonKey', 'ClueController@useTheSkeletonKey');
		
		// Route::post('generateReward', 'HuntController@generateReward');
		
		Route::get('getGamesData', 'MGController@getGamesData');
		Route::post('addSkeletonKey', 'MGController@addSkeletonKey');
	});
});