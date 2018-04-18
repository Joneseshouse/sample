<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\UserTransaction\Controllers';
	Route::group(
		['prefix' => 'user-transaction', 'module'=>'UserTransaction', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'UserTransactionController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'UserTransactionController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'UserTransactionController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'UserTransactionController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'UserTransactionController@removeItem'
			]);
			Route::get('rate', [
				'as' => 'rate',
				'uses' => 'UserTransactionController@rate'
			]);
			Route::get('test-amazon', [
				'as' => 'testAmazon',
				'uses' => 'UserTransactionController@testAmazon'
			]);
		}
	);
});