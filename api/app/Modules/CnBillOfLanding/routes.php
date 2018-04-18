<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\CnBillOfLanding\Controllers';
	Route::group(
		['prefix' => 'cn-bill-of-landing', 'module'=>'CnBillOfLanding', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'CnBillOfLandingController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'CnBillOfLandingController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'CnBillOfLandingController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'CnBillOfLandingController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'CnBillOfLandingController@removeItem'
			]);
			Route::post('upload', [
				'middleware' => 'token_required',
				'as' => 'upload',
				'uses' => 'CnBillOfLandingController@upload'
			]);
		}
	);
});