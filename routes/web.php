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

Route::get('admin/setPassword/{token}', 'Admin\AdminController@setPassword')->name('admin.setPassword');
Route::post('admin/savePassword/{id}', 'Admin\AdminController@savePassword')->name('admin.savePassword');

//Route::group(['prefix'=> 'admin', 'namespace'=>'Admin','as'=>'admin.','middleware'=>'auth:admin'],function(){
Route::group(['prefix'=> 'admin','middleware'=>'auth:admin', 'namespace'=>'Admin', 'as'=>'admin.'],function(){

	//dashboards
	Route::get('/', [ 'middleware' => ['permission:Dashboard'], 'uses' => 'AdminController@index' ])->name('dashboards');
	
	//User List
	Route::get('/userList', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@index' ])->name('userList');

	Route::get('/getUsers', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@getUsers' ])->name('getUsers');
	Route::get('/usersParticipatedList', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@usersParticipatedList' ])->name('usersParticipatedList');
	Route::get('/getUsertParticipatedList', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@getUsertParticipatedList' ])->name('getUsertParticipatedList');
	Route::get('/userHuntDetails/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@userHuntDetails' ])->name('userHuntDetails');
	Route::get('/accountInfo/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@accountInfo' ])->name('accountInfo');
	Route::get('/treasureHunts/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@treasureHunts' ])->name('treasureHunts');
	Route::get('/getTreasureHunts',[ 'middleware' => ['permission:View Users'], 'uses' =>  'UserController@getTreasureHunts' ])->name('getTreasureHunts');
	Route::get('/activity/{id}', [ 'middleware' => ['permission:View Users'], 'uses' => 'UserController@activity' ])->name('activity');

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
		Route::get('deleteImage', 'GameVariationController@deleteImage')->name('deleteImage');
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
	});

});

Auth::routes();