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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
// 	return $request->user();
// });

Route::post('addComplexityWiseTarget', 'Api\v1\PrepareController@addComplexityWiseTarget');
Route::post('addGames', 'Api\v1\PrepareController@addGames');
Route::post('addWidgets', 'Api\v1\PrepareController@addWidgets');
Route::get('addRolesAndPermissions', 'Api\v2\AddRolesAndPermission@addRolesAndPermissions');
Route::get('createPermissions', 'Api\v2\AddRolesAndPermission@createPermissions');
Route::get('guestCreate', 'Api\v1\UserController@guestCreate');

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1', 'middleware'=> 'downtime'], function ($router) {

	Route::post('login', 'AuthController@login')->middleware('freshapp');
	Route::post('checkUsernameEmail', 'AuthController@checkUsernameEmail');
	Route::post('register', 'UserController@register')->middleware('freshapp');
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


		// Route::post('changePassword', 'ProfileController@changePassword');
		// Route::post('updateProfile', 'ProfileController@updateProfile');
		// Route::post('updateSetting', 'ProfileController@updateSetting');


		// Route::post('getPayloadData', 'UserController@getPayloadData');
		
		// Route::post('selectkWidgetItem', 'UserController@selectkWidgetItem');
		// Route::post('setMyAvatar', 'UserController@setMyAvatar');
		// Route::post('minigameTutorialsCompleted', 'UserController@minigameTutorialsCompleted');
		// Route::get('getWarehouseData', 'UserController@getWarehouseData');
		
		/** Plans related requets **/
		// Route::get('getThePlans', 'PlanController@getThePlans');
		// Route::post('purchaseTheGolds', 'PlanController@purchaseTheGolds');
		
		/** Events related requests **/
		// Route::post('addParticipation', 'EventController@addParticipation');
		// Route::get('getTheEvents', 'EventController@getTheEvents');
		// Route::get('getEventDetails', 'EventController@getEventDetails');
		// Route::post('hitAnEventAction', 'EventController@hitAnEventAction');
		
		/** News related requests **/
		// Route::resource('news', 'NewsController');

		// Route::post('logout', 'AuthController@logout');
		// Route::post('refresh', 'AuthController@refresh');
		// Route::post('me', 'AuthController@me');

		/** Hunt related requests **/
		// Route::get('getHuntsByDifficulty', 'HuntController@getHuntsByDifficulty');
		// Route::get('getHuntDetails', 'HuntController@getHuntDetails');
		// Route::post('participateInHunt', 'HuntController@participateInHunt');
		// Route::post('getHuntParticipationDetails', 'HuntController@getHuntParticipationDetails');
		// Route::get('getNearByHunts', 'HuntController@getNearByHunts');
		// Route::post('getHuntUser', 'HuntController@getHuntUser');
		// Route::get('getHuntsInProgress', 'HuntController@getHuntsInProgress');
		// Route::get('getPreviousHuntDetails', 'HuntController@getPreviousHuntDetails');

		// Route::get('getHuntsByDifficultyV2', 'HuntController@getHuntsByDifficultyV2');
		// Route::post('participateInHuntV2', 'HuntController@participateInHuntV2');
		// Route::get('getHuntParticipationDetailsV2', 'HuntController@getHuntParticipationDetailsV2');
		// Route::get('getNearByHuntsV2', 'HuntController@getNearByHuntsV2');
		// Route::get('getHuntDetailsV2', 'HuntController@getHuntDetailsV2');
		// Route::get('getHuntsInProgressV2', 'HuntController@getHuntsInProgressV2');

		/** Clues related requests  **/
		// Route::post('revealTheClueV2', 'ClueController@revealTheClueV2');
		// Route::post('startTheClueV2', 'ClueController@startTheClueV2');
		// Route::post('pauseTheClueV2', 'ClueController@pauseTheClueV2');
		// Route::post('endTheClueV2', 'ClueController@endTheClueV2');
		// Route::post('useTheSkeletonKeyV2', 'ClueController@useTheSkeletonKeyV2');

		// Route::post('revealTheClue', 'ClueController@revealTheClue');
		// Route::get('quitTheHunt', 'ClueController@quitTheHunt');
		// Route::get('cluePause', 'ClueController@cluePause');
		// Route::post('skeleton', 'ClueController@skeleton');
		// Route::post('startTheClue', 'ClueController@startTheClue');
		// Route::post('endTheClue', 'ClueController@endTheClue');
		// Route::post('clueGame', 'ClueController@clueGame');
		// Route::get('userHuntInfo', 'ClueController@userHuntInfo');

	});

});




// Route::get('/v1/updatedWidgetData', 'Api\v2\WidgetItemController@updatedWidgetData');

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1', 'middleware' => ['jwt-auth', 'downtime']], function ($router) {

	/** Profile requests **/
	Route::post('changePassword', 'ProfileController@changePassword');
	Route::post('updateProfile', 'ProfileController@updateProfile');
	Route::post('updateSetting', 'ProfileController@updateSetting');
	Route::post('logout', 'AuthController@logout');
	Route::post('refresh', 'AuthController@refresh');
	Route::post('me', 'AuthController@me');

	/** News Requests **/
	Route::resource('news', 'NewsController');

	/** Avatar requests **/
	Route::post('setMyAvatar', 'UserController@setMyAvatar');
	
	/** Payload requests **/
	Route::post('getPayloadData', 'UserController@getPayloadData');
});

Route::group(['namespace' => 'Api\v2', 'prefix' => 'v1', 'middleware' => ['jwt-auth', 'downtime']], function ($router) {

	/** Widget Items requests **/
	Route::post('unlockWidgetItem', 'WidgetItemController@unlockWidgetItem');
});

Route::group(['namespace' => 'Api\v2', 'prefix' => 'v2', 'middleware'=> 'downtime'], function ($router) {
	
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
		Route::post('markTheMiniGameAsFail', 'ClueController@markTheMiniGameAsFail');
		
		/** Store requests **/
		Route::post('createAPurchase', 'PlanController@createAPurchase');
		Route::post('buySkeletonFromGold', 'PlanController@buySkeletonFromGold');
		
		/** Mini games requests **/
		Route::get('getGamesData', 'MGController@getGamesData');
		Route::post('markTheGameAsComplete', 'MGController@markTheGameAsComplete');
		Route::post('setupMiniGamesForUser', 'MGController@setupMiniGamesForUser');
		Route::post('unlockAMiniGame', 'MGController@unlockAMiniGame');
		Route::post('markMiniGameTutorialAsComplete', 'MGController@markMiniGameTutorialAsComplete');
        Route::post('markMiniGameAsFavourite', 'MGController@markMiniGameAsFavourite');
		Route::post('markMiniGameAsUncomplete', 'MGController@markMiniGameAsUncomplete');
		
		/** Event **/
		Route::get('getEventsCities', 'EventController@getEventsCities');
		Route::get('getEventsInCity', 'EventController@getEventsInCity');
		Route::post('participateInEvent', 'EventsUserController@participateInEvent');
		Route::post('markTheEventMGAsComplete', 'EventsMiniGameController@markTheEventMGAsComplete');
		Route::get('getPresentDayEventDetail', 'EventsUserController@getPresentDayEventDetail');
	
		/** DISCOUNT COUPON */
		//Route::get('getDiscountCoupon', 'DiscountCouponController@getDiscountCoupon');
		Route::post('useTheGoldCoupon', 'DiscountCouponController@useTheGoldCoupon');
	});
});

Route::group(['namespace' => 'Api\Hunt', 'prefix' => 'hunts', 'middleware' => ['jwt-auth', 'downtime']], function ($router) {

	/** Hunt requests **/
	Route::post('random/participate', 'RandomHuntController@participate');
	// Route::get('getHuntsOnStartup', 'RandomHuntController@initiateTheHunts');
	// Route::put('random/{hunt_user}/terminate', 'RandomHuntController@terminate');
	Route::post('random/revokeTheReveal', 'RandomHuntController@revokeTheReveal');
	Route::get('random/getRelicDetails', 'RandomHuntController@getRelicDetails');
	Route::get('random/getMinigamesForNode', 'RandomHuntController@getMinigamesForNode');
	Route::post('random/claimPrizeForBonuseTreasureNode', 'RandomHuntController@claimPrizeForBonuseTreasureNode');
	Route::post('random/claimPrizeForMinigameNode', 'RandomHuntController@claimPrizeForMinigameNode');
	Route::post('map/ARMode', 'RandomHuntController@updateARMode');
	Route::post('random/boostThePower', 'RandomHuntController@boostThePower');
	Route::post('random/boostThePower/active', 'RandomHuntController@activateThePower');
	Route::get('random/cell-data', 'RandomHuntController@getCellData');
});

Route::group(['namespace' => 'Api\Profile', 'prefix' => 'profile', 'middleware' => ['jwt-auth', 'downtime']], function ($router) {

	/** Hunt requests **/
	Route::get('getRelicsData', 'ProfileController@getRelics');
	Route::post('markTutorialAsComplete', 'ProfileController@markTutorialAsComplete');
	Route::post('openTheChest', 'ProfileController@openTheChest');
	Route::post('changeTheChestMG', 'ProfileController@changeTheChestMG');
	Route::post('removeTheChestFromBucket', 'ProfileController@removeTheChestFromBucket');
});

Route::group(['namespace' => 'Api\Relic', 'prefix' => 'relics', 'middleware' => ['jwt-auth', 'downtime']], function ($router) {

	/** Relic requests **/
	Route::post('markTheRelicAsComplete', 'RelicController@markTheRelicAsComplete');
});

Route::group(['namespace' => 'Api\User', 'prefix' => 'v2', 'middleware'=> ['downtime']], function ($router) {
	Route::post('login', 'AuthController@login')->middleware('freshapp');
});

Route::get('/app/url', 'Api\User\AuthController@getAppURL')->middleware('freshapp', 'downtime');