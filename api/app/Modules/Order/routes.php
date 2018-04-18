<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\Order\Controllers';
	Route::group(
		['prefix' => 'order', 'module'=>'Order', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'OrderController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'OrderController@obj'
			]);
			Route::post('upload-cart', [
				'middleware' => 'token_user_check',
				'as' => 'uploadCart',
				'uses' => 'OrderController@uploadCart'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'OrderController@addItem'
			]);
			Route::post('add-full', [
				'middleware' => 'token_required',
				'as' => 'addItemFull',
				'uses' => 'OrderController@addItemFull'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'OrderController@editItem'
			]);
			Route::post('update-delivery-fee-unit', [
				'middleware' => 'token_required',
				'as' => 'updateDeliveryFeeUnit',
				'uses' => 'OrderController@updateDeliveryFeeUnit'
			]);
			Route::post('draft-to-new', [
				'middleware' => 'token_required',
				'as' => 'draftToNew',
				'uses' => 'OrderController@draftToNew'
			]);
			Route::post('mass-confirm', [
				'middleware' => 'token_required',
				'as' => 'massConfirm',
				'uses' => 'OrderController@massConfirm'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'OrderController@removeItem'
			]);
			Route::get('download/{id}/{uid}', [
				'as' => 'download',
				'uses' => 'OrderController@download'
			]);
		}
	);
});