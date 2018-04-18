<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\VnBillOfLanding\Controllers';
	Route::group(
		['prefix' => 'vn-bill-of-landing', 'module'=>'VnBillOfLanding', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'VnBillOfLandingController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'VnBillOfLandingController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'VnBillOfLandingController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'VnBillOfLandingController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'VnBillOfLandingController@removeItem'
			]);
			Route::post('upload', [
				'middleware' => 'token_required',
				'as' => 'upload',
				'uses' => 'VnBillOfLandingController@upload'
			]);
		}
	);
});