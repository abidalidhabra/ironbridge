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
	Route::get('/usersParticipatedList', 'UserController@usersParticipatedList')->name('usersParticipatedList');
	Route::get('/getUsertParticipatedList', 'UserController@getUsertParticipatedList')->name('getUsertParticipatedList');
	Route::get('/userHuntDetails/{id}', 'UserController@userHuntDetails')->name('userHuntDetails');
	Route::get('/accountInfo/{id}', 'UserController@accountInfo')->name('accountInfo');
	Route::get('/treasureHunts/{id}', 'UserController@treasureHunts')->name('treasureHunts');
	Route::get('/getTreasureHunts', 'UserController@getTreasureHunts')->name('getTreasureHunts');
	Route::get('/activity/{id}', 'UserController@activity')->name('activity');

	//News
	Route::resource('news', 'NewsController');
	Route::get('getNewsList', 'NewsController@getNewsList')->name('getNewsList');

	//MAPS
	Route::get('/mapsList', 'MapsController@index')->name('mapsList');
	Route::get('/getMaps', 'MapsController@getMaps')->name('getMaps');
	Route::get('/boundary_map/{id}', 'MapsController@boundaryMap')->name('boundary_map');
	Route::get('/star_complexity_map/{id}/{complexity}', 'MapsController@starComplexityMap')->name('starComplexityMap');
	Route::post('/store_star_complexity', 'MapsController@storeStarComplexity')->name('storeStarComplexity');
	Route::delete('/clearAllClues/{id}', 'MapsController@clearAllClues')->name('clearAllClues');
	Route::post('/verifiedUpdate', 'MapsController@verifiedUpdate')->name('verifiedUpdate');


	Route::get('/add_location', 'MapsController@addLocation')->name('add_location');
	Route::get('/edit_location/{id}', 'MapsController@editLocation')->name('edit_location');
	Route::post('/store_location', 'MapsController@storeLocation')->name('store_location');
	Route::delete('/locationDelete/{id}', 'MapsController@locationDelete')->name('locationDelete');
	Route::get('/removeStar', 'MapsController@removeStar')->name('removeStar');
	Route::post('/update_location', 'MapsController@updateLocation')->name('update_location');
	Route::get('/test_location', 'MapsController@testLocation')->name('test_location');
	Route::get('/getGameVariations', 'MapsController@getGameVariations')->name('getGameVariations');
	

	Route::get('/customRecordStore', 'MapsController@customRecordStore')->name('customRecordStore');
	
	//GAME
	Route::get('game', 'GameController@index')->name('game.index');
	Route::post('addgame', 'GameController@addgame')->name('addgame');
	Route::get('getGameList', 'GameController@getGameList')->name('getGameList');
	Route::put('editGame', 'GameController@editGame')->name('editGame');
	Route::delete('deleteGame', 'GameController@deleteGame')->name('deleteGame');
	Route::get('practiceGamesTargets', 'GameController@practiceGame')->name('practiceGame');
	Route::post('gameTargetUpdate', 'GameController@gameTargetUpdate')->name('gameTargetUpdate');
	Route::post('variationSizeUpdate', 'GameController@variationSizeUpdate')->name('variationSizeUpdate');
	Route::post('practiceDeleteImage', 'GameController@practiceDeleteImage')->name('practiceDeleteImage');


	//GameVariationController
	Route::resource('gameVariation', 'GameVariationController');
	Route::get('getGameVariationList', 'GameVariationController@getGameVariationList')->name('getGameVariationList');
	Route::get('deleteImage', 'GameVariationController@deleteImage')->name('deleteImage');


	//TARGET COMPLEXITY
	Route::get('complexityTarget', 'ComplexityTargetController@index')->name('complexityTarget.index');
	Route::get('getComplexityTarget', 'ComplexityTargetController@getComplexityTarget')->name('getComplexityTarget');
	Route::put('editComplexityTarget', 'ComplexityTargetController@editComplexityTarget')->name('editComplexityTarget');


	//AVATAR
	Route::get('avatar', 'AvatarController@index')->name('avatar.index');
	Route::get('/getAvatarsList', 'AvatarController@getAvatarsList')->name('getAvatarsList');
	Route::get('/avatarDetails/{id}', 'AvatarController@avatarDetails')->name('avatarDetails');
	Route::post('/widgetPriceUpdate', 'AvatarController@widgetPriceUpdate')->name('widgetPriceUpdate');
	Route::post('/avatarColorUpdate', 'AvatarController@avatarColorUpdate')->name('avatarColorUpdate');
	Route::post('/widgetCategoryUpdate', 'AvatarController@widgetCategoryUpdate')->name('widgetCategoryUpdate');

});

Auth::routes();