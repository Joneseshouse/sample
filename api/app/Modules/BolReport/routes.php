<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\BolReport\Controllers';
	Route::group(
		['prefix' => 'bol-report', 'module'=>'BolReport', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'BolReportController@list'
			]);
		}
	);
});