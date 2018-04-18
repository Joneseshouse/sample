<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\CartItem\Controllers';
	Route::group(
		['prefix' => 'cart-item', 'module'=>'CartItem', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'CartItemController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'CartItemController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'CartItemController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'CartItemController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'CartItemController@removeItem'
			]);
			Route::get('rate', [
				'middleware' => 'token_user_check',
				'as' => 'rate',
				'uses' => 'CartItemController@rate'
			]);
		}
	);
});