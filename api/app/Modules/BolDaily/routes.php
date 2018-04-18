<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\BolDaily\Controllers';
	Route::group(
		['prefix' => 'bol-daily', 'module'=>'BolDaily', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'BolDailyController@list'
			]);
		}
	);
});