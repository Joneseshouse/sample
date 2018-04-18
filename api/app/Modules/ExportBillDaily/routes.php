<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\ExportBillDaily\Controllers';
	Route::group(
		['prefix' => 'export-bill-daily', 'module'=>'ExportBillDaily', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'ExportBillDailyController@list'
			]);
		}
	);
});