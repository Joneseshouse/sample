<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\CheckBill\Controllers';
	Route::group(
		['prefix' => 'check-bill', 'module'=>'CheckBill', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'CheckBillController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'CheckBillController@obj'
			]);
			Route::post('check-full', [
				'middleware' => 'token_required',
				'as' => 'checkFull',
				'uses' => 'CheckBillController@checkFull'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'CheckBillController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'CheckBillController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'CheckBillController@removeItem'
			]);
			Route::get('rate', [
				'as' => 'rate',
				'uses' => 'CheckBillController@rate'
			]);
		}
	);
});