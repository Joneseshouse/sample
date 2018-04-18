<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\OrderItem\Controllers';
	Route::group(
		['prefix' => 'order-item', 'module'=>'OrderItem', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'OrderItemController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'OrderItemController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'OrderItemController@addItem'
			]);
			Route::post('add-cart', [
				'middleware' => 'token_required',
				'as' => 'addCart',
				'uses' => 'OrderItemController@addCart'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'OrderItemController@editItem'
			]);
			Route::post('edit-unit-price', [
				'middleware' => 'token_required',
				'as' => 'editUnitPrice',
				'uses' => 'OrderItemController@editUnitPrice'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'OrderItemController@removeItem'
			]);
			Route::post('empty', [
				'middleware' => 'token_required',
				'as' => 'empty',
				'uses' => 'OrderItemController@empty'
			]);
		}
	);
});