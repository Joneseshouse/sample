<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\Purchase\Controllers';
	Route::group(
		['prefix' => 'purchase', 'module'=>'Purchase', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'PurchaseController@list'
			]);
			Route::get('check-list', [
				'middleware' => 'token_required',
				'as' => 'check',
				'uses' => 'PurchaseController@check'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'PurchaseController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'PurchaseController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'PurchaseController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'PurchaseController@removeItem'
			]);
			Route::post('upload', [
				'as' => 'upload',
				'uses' => 'PurchaseController@upload'
			]);
		}
	);
});