<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\BillOfLanding\Controllers';
	Route::group(
		['prefix' => 'bill-of-landing', 'module'=>'BillOfLanding', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'BillOfLandingController@list'
			]);
			Route::get('list-pure', [
				'middleware' => 'token_required',
				'as' => 'listPure',
				'uses' => 'BillOfLandingController@listPure'
			]);
			Route::get('list-check-bill', [
				'middleware' => 'token_required',
				'as' => 'listCheckBill',
				'uses' => 'BillOfLandingController@listCheckBill'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'BillOfLandingController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'BillOfLandingController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'BillOfLandingController@editItem'
			]);
			Route::post('reset-complain', [
				'middleware' => 'token_required',
				'as' => 'resetComplain',
				'uses' => 'BillOfLandingController@resetComplain'
			]);
			Route::post('edit-complain', [
				'middleware' => 'token_required',
				'as' => 'editComplain',
				'uses' => 'BillOfLandingController@editComplain'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'BillOfLandingController@removeItem'
			]);
			Route::get('check-duplicate-code', [
				'middleware' => 'token_required',
				'as' => 'checkDuplicateCode',
				'uses' => 'BillOfLandingController@checkDuplicateCode'
			]);
		}
	);
});