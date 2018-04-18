<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\CnBillOfLandingFail\Controllers';
	Route::group(
		['prefix' => 'cn-bill-of-landing-fail', 'module'=>'CnBillOfLandingFail', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'CnBillOfLandingFailController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'CnBillOfLandingFailController@obj'
			]);
			Route::get('obj-filter', [
				'middleware' => 'token_required',
				'as' => 'objFilter',
				'uses' => 'CnBillOfLandingFailController@objFilter'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'CnBillOfLandingFailController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'CnBillOfLandingFailController@removeItem'
			]);
		}
	);
});