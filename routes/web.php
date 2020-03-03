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

/*Route::get('/', function () {
	return view('welcome');
});*/
Route::get('login',array('as'=>'login',function(){
	return abort(404);
}));

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

Route::get('admin/setPassword/{token}', 'Admin\AdminController@setPassword')->name('admin.setPassword');
Route::post('admin/savePassword/{id}', 'Admin\AdminController@savePassword')->name('admin.savePassword');

//Route::group(['prefix'=> 'admin', 'namespace'=>'Admin','as'=>'admin.','middleware'=>'auth:admin'],function(){
Route::group(['prefix'=> 'admin','middleware'=>'auth:admin', 'namespace'=>'Admin', 'as'=>'admin.'],function(){

	//dashboards
	Route::get('/', [ 'middleware' => ['permission:Dashboard'], 'uses' => 'AdminController@index' ])->name('dashboards');
	Route::get('signedUpDateFilter', [ 'middleware' => ['permission:Dashboard'], 'uses' => 'AdminController@signedUpDateFilter' ])->name('signedUpDateFilter');

	Route::get('analyticsMetrics', [ 'middleware' => ['permission:View Analytics'], 'uses' => 'AnalyticMetricController@analyticsMetrics' ])->name('analyticsMetrics');
	Route::get('analyticsMetricsFilter', 'AnalyticMetricController@analyticsMetricsFilter')->name('analyticsMetricsFilter');
	Route::get('getStoreDateFilter', 'AnalyticMetricController@getStoreDateFilter')->name('getStoreDateFilter');
	Route::get('getUserDateFilter', 'AnalyticMetricController@getUserDateFilter')->name('getUserDateFilter');
	Route::get('getTutorialDateFilter', 'AnalyticMetricController@getTutorialDateFilter')->name('getTutorialDateFilter');
	Route::get('getHuntDateFilter', 'AnalyticMetricController@getHuntDateFilter')->name('getHuntDateFilter');
	Route::get('getEventDateFilter', 'AnalyticMetricController@getEventDateFilter')->name('getEventDateFilter');
	Route::get('getAnalytic', 'AnalyticMetricController@getAnalytic')->name('getAnalytic');
	Route::get('getAvtarDateFilter', 'AnalyticMetricController@getAvtarDateFilter')->name('getAvtarDateFilter');
	Route::get('analyticsMetrics/XPList', 'AnalyticMetricController@XPList')->name('analyticsMetrics.XPList');
	Route::get('analyticsMetrics/getXPList', 'AnalyticMetricController@getXPList')->name('analyticsMetrics.getXPList');
	Route::get('analyticsMetrics/relicsList', 'AnalyticMetricController@relicsList')->name('analyticsMetrics.relicsList');
	Route::get('analyticsMetrics/getRelicsList', 'AnalyticMetricController@getRelicsList')->name('analyticsMetrics.getRelicsList');
	
	//User List
	// Route::post('/users/{id}/reset', 'UserController@reserTheUser')->name('user.reset');
	Route::post('/users/{id}/reset', [ 'middleware' => ['permission:Reset Users'], 'uses' => 'UserController@reserTheUser' ])->name('user.reset');
	Route::get('/userList', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@index' ])->name('userList');

	Route::get('/getUsers', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@getUsers' ])->name('getUsers');
	Route::get('/usersParticipatedList', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@usersParticipatedList' ])->name('usersParticipatedList');
	Route::get('/getUsertParticipatedList', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@getUsertParticipatedList' ])->name('getUsertParticipatedList');
	Route::get('/userHuntDetails/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@userHuntDetails' ])->name('userHuntDetails');
	Route::get('/accountInfo/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@accountInfo' ])->name('accountInfo');
	Route::get('/treasureHunts/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@treasureHunts' ])->name('treasureHunts');
	Route::get('/getTreasureHunts',[ 'middleware' => ['permission:View Users'], 'uses' =>  'UserController@getTreasureHunts' ])->name('getTreasureHunts');
	Route::get('/activity/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@activity' ])->name('activity');

	Route::post('/addGold',[ 'middleware' => ['permission:Add Users'], 'uses' => 'UserController@addGold'])->name('addGold');
	Route::post('/addSkeletonKey',[ 'middleware' => ['permission:Add Users'], 'uses' => 'UserController@addSkeletonKey'])->name('addSkeletonKey');

	Route::get('/practiceGameUser/{id}','UserController@practiceGameUser')->name('practiceGameUser');
	Route::get('/avatarItems/{id}','UserController@avatarItems')->name('user.avatarItems');
	Route::get('/planPurchase/{id}','UserController@planPurchase')->name('user.planPurchase');
	Route::get('getPlanPurchaseList','UserController@getPlanPurchaseList')->name('getPlanPurchaseList');
	Route::get('/miniGameStatistics/{id}','UserController@miniGameStatistics')->name('miniGameStatistics');
	Route::get('/tutorialsProgress/{id}','UserController@tutorialsProgress')->name('tutorialsProgress');
	Route::get('/chestInverntory/{id}','UserController@chestInverntory')->name('chestInverntory');
	


	//News
	Route::group(['middleware' => ['permission:View News']], function () {
		Route::resource('news', 'NewsController');
		Route::get('getNewsList', 'NewsController@getNewsList')->name('getNewsList');
	});

	//MAPS
	Route::get('/mapsList', [ 'middleware' => ['permission:View Treasure Locations'], 'uses' => 'MapsController@index' ])->name('mapsList');
	Route::get('/getMaps', [ 'middleware' => ['permission:View Treasure Locations'], 'uses' => 'MapsController@getMaps' ])->name('getMaps');
	Route::get('/boundary_map/{id}', [ 'middleware' => ['permission:View Treasure Locations'], 'uses' => 'MapsController@boundaryMap' ])->name('boundary_map');
	Route::get('/star_complexity_map/{id}/{complexity}', [ 'middleware' => ['permission:Edit Treasure Locations'], 'uses' => 'MapsController@starComplexityMap' ])->name('starComplexityMap');
	Route::post('/store_star_complexity', [ 'middleware' => ['permission:Edit Treasure Locations'], 'uses' => 'MapsController@storeStarComplexity' ])->name('storeStarComplexity');
	Route::delete('/clearAllClues/{id}', [ 'middleware' => ['permission:Delete Treasure Locations'], 'uses' => 'MapsController@clearAllClues' ])->name('clearAllClues');
	Route::post('/verifiedUpdate', [ 'middleware' => ['permission:Edit Treasure Locations'], 'uses' => 'MapsController@verifiedUpdate' ])->name('verifiedUpdate');


	Route::get('/add_location', [ 'middleware' => ['permission:Add Treasure Locations'], 'uses' => 'MapsController@addLocation' ])->name('add_location');
	Route::get('/edit_location/{id}', [ 'middleware' => ['permission:Edit Treasure Locations'], 'uses' => 'MapsController@editLocation' ])->name('edit_location');
	Route::post('/store_location', [ 'middleware' => ['permission:Add Treasure Locations'], 'uses' => 'MapsController@storeLocation' ])->name('store_location');
	Route::delete('/locationDelete/{id}', [ 'middleware' => ['permission:Delete Game Variations'], 'uses' => 'MapsController@locationDelete' ])->name('locationDelete');
	Route::get('/removeStar', [ 'middleware' => ['permission:Delete Treasure Locations'], 'uses' => 'MapsController@removeStar' ])->name('removeStar');
	Route::post('/update_location', [ 'middleware' => ['permission:Edit Treasure Locations'], 'uses' => 'MapsController@updateLocation' ])->name('update_location');
	Route::get('/test_location', [ 'middleware' => ['permission:View Treasure Locations'], 'uses' => 'MapsController@testLocation' ])->name('test_location');

	Route::get('/getGameVariations', [ 'middleware' => ['permission:View Game Variations'], 'uses' => 'MapsController@getGameVariations' ])->name('getGameVariations');
	

	Route::get('/customRecordStore', [ 'middleware' => ['permission:Add Treasure Locations'], 'uses' => 'MapsController@customRecordStore' ])->name('customRecordStore');
	
	//GAME
	Route::get('game', [ 'middleware' => ['permission:View Games'], 'uses' => 'GameController@index' ])->name('game.index');
	Route::post('addgame', [ 'middleware' => ['permission:Add Games'], 'uses' => 'GameController@addgame' ])->name('addgame');
	Route::get('getGameList', [ 'middleware' => ['permission:View Games'], 'uses' => 'GameController@getGameList' ])->name('getGameList');
	Route::put('editGame', [ 'middleware' => ['permission:Edit Games'], 'uses' => 'GameController@editGame' ])->name('editGame');
	Route::delete('deleteGame', [ 'middleware' => ['permission:View Games'], 'uses' => 'GameController@deleteGame' ])->name('deleteGame');

	Route::get('practiceGamesTargets',[ 'middleware' => ['permission:Add Practice Games'],  'uses' => 'GameController@practiceGame' ])->name('practiceGame');
	Route::post('gameTargetUpdate',[ 'middleware' => ['permission:Edit Practice Games'],  'uses' => 'GameController@gameTargetUpdate' ])->name('gameTargetUpdate');
	Route::post('variationSizeUpdate',[ 'middleware' => ['permission:Edit Practice Games'],  'uses' => 'GameController@variationSizeUpdate'])->name('variationSizeUpdate');
	Route::post('practiceDeleteImage',[ 'middleware' => ['permission:Edit Practice Games'],  'uses' => 'GameController@practiceDeleteImage'])->name('practiceDeleteImage');


	//GameVariationController
	Route::group(['middleware' => ['permission:View Game Variations']], function () {
		Route::resource('gameVariation', 'GameVariationController');
		Route::get('getGameVariationList', 'GameVariationController@getGameVariationList')->name('getGameVariationList');
		Route::delete('deleteImage', 'GameVariationController@deleteImage')->name('deleteImage');
	});


	//TARGET COMPLEXITY
	Route::get('complexityTarget', [ 'middleware' => ['permission:View Complexity Targets'], 'uses' => 'ComplexityTargetController@index' ])->name('complexityTarget.index');
	Route::get('getComplexityTarget', [ 'middleware' => ['permission:View Complexity Targets'], 'uses' => 'ComplexityTargetController@getComplexityTarget' ])->name('getComplexityTarget');
	Route::put('editComplexityTarget', [ 'middleware' => ['permission:Edit Complexity Targets'], 'uses' => 'ComplexityTargetController@editComplexityTarget' ])->name('editComplexityTarget');


	//AVATAR
	Route::get('avatar', [ 'middleware' => ['permission:View Avatars'], 'uses' => 'AvatarController@index' ])->name('avatar.index');
	Route::get('/getAvatarsList', [ 'middleware' => ['permission:View Avatars'], 'uses' => 'AvatarController@getAvatarsList' ])->name('getAvatarsList');
	Route::get('/avatarDetails/{id}', [ 'middleware' => ['permission:View Avatars'], 'uses' => 'AvatarController@avatarDetails' ])->name('avatarDetails');
	Route::post('/widgetPriceUpdate', [ 'middleware' => ['permission:Edit Avatars'], 'uses' => 'AvatarController@widgetPriceUpdate' ])->name('widgetPriceUpdate');
	Route::post('/avatarColorUpdate', [ 'middleware' => ['permission:Edit Avatars'], 'uses' => 'AvatarController@avatarColorUpdate' ])->name('avatarColorUpdate');
	Route::post('/widgetCategoryUpdate', [ 'middleware' => ['permission:Edit Avatars'], 'uses' => 'AvatarController@widgetCategoryUpdate' ])->name('widgetCategoryUpdate');


	Route::group(['middleware' => ['role:Super Admin']], function () {
		Route::resource('adminManagement', 'AdminManagement');
		Route::get('getAdminsList', 'AdminManagement@getAdminList')->name('getAdminsList');
		Route::get('resendMail/{id}', 'AdminManagement@resendMail')->name('resendMail');
	});


	//EVENTS
	Route::get('events/list', 'EventController@list')->name('events.list');
	Route::resource('events', 'EventController');
	// Route::get('/events/{id}','UserController@eventsUser')->name('eventsUser');
	Route::get('basicDetails/{id?}', [ 'middleware' => ['permission:Add Event'], 'uses' => 'EventController@basicDetails' ])->name('event.basicDetails');
	Route::post('addBasicStore', [ 'middleware' => ['permission:Add Event'], 'uses' => 'EventController@addBasicStore' ])->name('event.addBasicStore');
	Route::get('miniGame/{id}', [ 'middleware' => ['permission:Add Event'], 'uses' => 'EventController@miniGame' ])->name('event.miniGame');
	Route::post('addMiniGame', [ 'middleware' => ['permission:Add Event'], 'uses' => 'EventController@addMiniGame' ])->name('event.addMiniGame');
	Route::get('huntDetails/{id}', [ 'middleware' => ['permission:Add Event'], 'uses' => 'EventController@huntDetails'])->name('event.huntDetails');
	Route::get('getEventList', [ 'middleware' => ['permission:View Event'], 'uses' => 'EventController@getEventList'])->name('getEventList');
	Route::post('addHuntDetails', [ 'middleware' => ['permission:Add Event'], 'uses' => 'EventController@addHuntDetails'])->name('event.addHuntDetails');
	Route::post('updateEvent', [ 'middleware' => ['permission:Edit Event'], 'uses' => 'EventController@updateEvent'])->name('event.updateEvent');
	Route::post('getHuntList', [ 'middleware' => ['permission:Add Event'], 'uses' => 'EventController@getHuntList'])->name('event.getHuntList');

	//event participated
	Route::get('event_participated', [ 'middleware' => ['permission:View Event Participated'], 'uses' => 'EventParticipatedController@index'])->name('eventparticipated.index');
	Route::get('getEventParticipatedList', [ 'middleware' => ['permission:View Event Participated'], 'uses' => 'EventParticipatedController@getEventParticipatedList'])->name('getEventParticipatedList');


	//PAYMENT
	Route::get('payment', [ 'middleware' => ['permission:View Payments'], 'uses' => 'PaymentController@index' ])->name('payment.index');
	Route::get('getPaymentList', [ 'middleware' => ['permission:View Payments'], 'uses' => 'PaymentController@getPaymentList' ])->name('getPaymentList');


	/* Discount Coupons */
	Route::group(['middleware' => ['permission:View Discount Coupons']], function () {
		Route::resource('discounts', 'DiscountCouponController');
		Route::get('getDiscountsList', 'DiscountCouponController@getDiscountsList')->name('getDiscountsList');
	});
	
	/* hunt rewards */
	Route::group(['middleware' => ['permission:View Hunt Loot Tables']], function () {
		Route::resource('rewards', 'RewardController');
	});
	
	Route::resource('loots', 'LootController');
	Route::group(['middleware' => ['permission:View MGC Loot Table']], function () {
		Route::resource('mgc_loot', 'MgcController');
	});
	Route::resource('hunt_statistics', 'HuntStatisticController');
	Route::get('goldHTML', 'LootController@goldHTML')->name('loots.goldHTML');
	Route::get('skeletonHTML', 'LootController@skeletonHTML')->name('loots.skeletonHTML');
	Route::get('skeletonGoldHTML', 'LootController@skeletonGoldHTML')->name('loots.skeletonGoldHTML');
	Route::get('avatarHTML', 'LootController@avatarHTML')->name('loots.avatarHTML');
	Route::get('widgetHTML', 'LootController@widgetHTML')->name('loots.widgetHTML');
	Route::get('changeStatus', 'LootController@changeStatus')->name('loots.changeStatus');
	Route::get('edit_details/{id}', 'LootController@edit_details')->name('loots.edit_details');
	
	Route::resource('treasure_nodes_targets', 'TreasureNodesTargetController');
	Route::get('getTreasureNodesTargetsList', 'TreasureNodesTargetController@getTreasureNodesTargetsList')->name('getTreasureNodesTargetsList');

	Route::group(['middleware' => ['permission:View Seasonal Hunt']], function () {
		Route::get('sponser-hunts/list', 'SponserHuntController@list')->name('sponser-hunts.list');
		Route::get('sponser-hunts/hunt-html', 'SponserHuntController@huntHTML')->name('sponser-hunts.hunt-html');
		Route::get('sponser-hunts/clue-html', 'SponserHuntController@clueHTML')->name('sponser-hunts.clue-html');
		Route::resource('sponser-hunts', 'SponserHuntController');
	});
	
	Route::get('seasons/list', 'SeasonController@list')->name('seasons.list');
	// Route::get('seasons/hunt-html', 'SeasonController@huntHTML')->name('seasons.hunt-html');
	// Route::get('seasons/clue-html', 'SeasonController@clueHTML')->name('seasons.clue-html');
	Route::resource('seasons', 'SeasonController');
	Route::get('relics/list', 'RelicController@list')->name('relics.list');
	Route::get('relics/create', 'RelicController@create')->name('relics.create');
	Route::post('relics/{season_slug}/store', 'RelicController@store')->name('relics.store');
	Route::get('relics/clues/html', 'RelicController@clueHTML')->name('relics.clue.html');
	// Route::get('relics/{season_slug}/edit/{id}', 'RelicController@edit')->name('relics.edit');
	Route::resource('relics', 'RelicController')->except(['create', 'store']);;
	Route::post('removePieces', 'RelicController@removePieces')->name('removePieces');

	Route::group(['middleware' => ['permission:View App Settings']], function () {
		Route::get('app/settings', 'AppSettingController@index')->name('app.settings.index');
		Route::put('app/settings', 'AppSettingController@update')->name('app.settings.update');
	});


	Route::resource('relicReward', 'RelicRewardController');
	Route::get('list', 'RelicRewardController@list')->name('relicReward.list');


	Route::resource('practiceGame', 'PracticeGameController');
	Route::get('GetPracticeGameList', 'PracticeGameController@GetPracticeGameList')->name('GetPracticeGameList');
	Route::get('practiceGame/clues/html', 'PracticeGameController@targerHTML')->name('practiceGame.clue.html');

	Route::group(['middleware' => ['permission:View Hunts XP']], function () {
		Route::get('getXpManagementList', 'XpManagementController@getXpManagementList')->name('getXpManagementList');
		Route::post('updateDistanceXp', 'XpManagementController@updateDistanceXp')->name('xpManagement.updateDistanceXp');
		Route::resource('xpManagement', 'XpManagementController');
	});
	
	Route::group(['middleware' => ['permission:View Agent Levels']], function () {
		Route::get('agent-levels/list', 'AgentLevel\AgentLevelController@list')->name('agent-levels.list');
		Route::resource('agent-levels', 'AgentLevel\AgentLevelController');
	});
	
	Route::group(['middleware' => ['permission:View Bucket Size / Agent Levels']], function () {
		Route::get('agent-level/bucket-sizes/list', 'AgentLevel\BucketSizeAgentLevelController@list')->name('bucket-sizes.list');
		Route::resource('agent-level/bucket-sizes', 'AgentLevel\BucketSizeAgentLevelController');
	});

	Route::group(['middleware' => ['permission:View Hunt / Agent Levels']], function () {
		Route::resource('hunts-agent-levels', 'AgentLevel\HuntAgentLevelController')->except('index', 'store');
		Route::get('hunts-agent-levels-list', 'AgentLevel\HuntAgentLevelController@list')->name('hunts-agent-levels-list');
	});

	Route::group(['middleware' => ['permission:View Minigames / Agent Levels']], function () {
		Route::resource('minigames-agent-levels', 'AgentLevel\MinigameAgentLevelController');
		Route::get('minigames-agent-levels-list', 'AgentLevel\MinigameAgentLevelController@list')->name('minigames-agent-levels-list');
	});

	Route::resource('nodes-agent-levels', 'AgentLevel\NodeController');
	Route::get('nodes-agent-levels-list', 'AgentLevel\NodeController@list')->name('nodes-agent-levels-list');


	Route::group(['middleware' => ['permission:View Avatar / Agent Levels']], function () {
		Route::resource('avatar-agent-levels', 'AgentLevel\AvatarAgentLevelController');
		Route::get('avatar-agent-levels-list', 'AgentLevel\AvatarAgentLevelController@list')->name('avatar-agent-levels-list');
	});


	Route::get('plans/list', 'PlanController@list')->name('plans.list');
	Route::resource('plans', 'PlanController');
	/* NOTIFICATION */
	Route::resource('event-notifications', 'EventNotificationController');
	Route::resource('notifications', 'NotificationController');
	Route::post('reported-locations/submit', 'ReportLocationController@submit')->name('reported-locations.submit');
	Route::post('reported-locations/updateIt', 'ReportLocationController@updateIt')->name('reported-locations.updateIt');
	Route::get('reported-locations/list', 'ReportLocationController@list')->name('reported-locations.list');
	Route::resource('reported-locations', 'ReportLocationController');
});

// Auth::routes();